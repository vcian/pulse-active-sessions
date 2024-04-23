<?php

namespace Vcian\Pulse\PulseActiveSessions\Recorders;

use Carbon\CarbonImmutable;
use Illuminate\Config\Repository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Token;
use Laravel\Pulse\Events\SharedBeat;
use Laravel\Pulse\Pulse;
use Laravel\Sanctum\PersonalAccessToken;
use ReflectionClass;
use \Illuminate\Support\Facades\Redis;
use Vcian\Pulse\PulseActiveSessions\Constant;
use Illuminate\Support\Facades\Cache;

class PulseActiveSessionRecorder
{
    /**
     * The events to listen for.
     *
     * @var class-string
     */
    public string $listen = SharedBeat::class;

    /**
     * Create a new recorder instance.
     */
    public function __construct(
        protected Pulse      $pulse,
        protected Repository $config
    )
    {
        //
    }

    /**
     * @param SharedBeat $event
     * @return void
     * @throws \ReflectionException
     */
    public function record(SharedBeat $event): void
    {
        if ($event->time->second % Constant::RENDER_TIME_SEC !== Constant::ZERO) {
            return;
        }

        foreach (authProviders() as $authProvider) {
            $activeSessions['web'] = $this->webLoginCounts($authProvider);
            $activeSessions['api_driver'] = $this->currentApiDriver();
            $activeSessions['api'] = $this->apiLoginCounts($activeSessions['api_driver'], $authProvider);
            $activeSessions['total'] = $activeSessions['web'] + $activeSessions['api'];

            $this->pulse->set('pulse_active_session_' . $authProvider, 'result', json_encode($activeSessions));
        }
    }

    /**
     * Record the sanctum token count.
     *
     * @param $authProvider
     * @return int
     */
    private function recordSanctum($authProvider): int
    {
        return PersonalAccessToken::query()->where(function ($query) {
            $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
        })->with('tokenable')->whereHas('tokenable', function ($q) use ($authProvider) {
            return $q->where('tokenable_type', config('auth.providers.' . $authProvider . '.model'));
        })->count();
    }

    /**
     * Record the passport token count.
     *
     * @param $authProvider
     * @return int
     */
    private function recordPassport($authProvider): int
    {
        $activeTokens = Token::query()->whereNull('expires_at')
            ->orWhere('expires_at', '>', now())
            ->where('revoked', 0)
            ->pluck('user_id');

        $tokenCounts = array_count_values($activeTokens->toArray());
        $uniqueClientIds = array_keys($tokenCounts);

        return authProviderModel($authProvider)
                ->whereIn('id', $uniqueClientIds)
                ->count() + (array_sum($tokenCounts) - count($uniqueClientIds));
    }

    /**
     * Record the session count
     *
     * @param $authProvider
     * @return int
     */
    private function recordDatabase($authProvider): int
    {
        $response = Constant::ZERO;
        $guards = authGuards($authProvider);

        try {
            $sessions = DB::table('sessions')
                ->where('last_activity', '>', now()->subMinutes(config('session.lifetime')))
                ->whereNotNull('user_id')
                ->get(['payload']);

            $response = $sessions->filter(function ($item) use ($authProvider, $guards) {
                $data = unserialize(base64_decode($item->payload));

                $filteredPayload = Arr::where($data, function ($value, $key) use ($guards) {
                    foreach ($guards as $guardKey => $guardValue) {
                        return strpos($key, $guardValue) !== false;
                    }
                });

                return !empty($filteredPayload);
            })->count();
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
        }

        return $response;
    }

    /**
     * Get count of active file sessions
     *
     * @param $authProvider
     * @return int
     */
    private function countActiveFileSessions($authProvider): int
    {
        $response = Constant::ZERO;

        foreach (glob(base_path() . "/storage/framework/sessions/*") as $filename) {
            $payload = unserialize(file_get_contents($filename));
            $lastActivity = $payload['auth']['password_confirmed_at'] ?? '';
            if ($lastActivity && now()->subMinutes(config('session.lifetime'))->timestamp < $lastActivity) {
                foreach ($payload as $key => $value) {
                    if ($this->guardMatch($authProvider, $key)) {
                        $response++;
                    }
                }
            }
        }

        return $response;
    }

    /**
     * Get count of active redis sessions
     *
     * @param $authProvider
     * @return int
     */
    private function countActiveRedisSession($authProvider): int
    {
        $keys = array_map(fn($key) => str_replace(config('database.redis.options.prefix'), '', $key), Redis::keys('*'));
        $sessionData = array_map(fn($data) => unserialize(unserialize($data)), Redis::mget($keys));
        $response = Constant::ZERO;

        if ($sessionData) {
            foreach ($sessionData as $session) {
                foreach ($session as $key => $value) {
                    if ($this->guardMatch($authProvider, $key)) {
                        $response++;
                    }
                }
            }
        }

        return $response;
    }

    /**
     * Get count of active memcached sessions
     *
     * @param $authProvider
     * @return int
     */
    private function countActiveMemcachedSession($authProvider): int
    {
        $memcachedStore = Cache::store('memcached')->getStore();
        $memcached = $memcachedStore->getMemcached();
        $memcacheData = $memcached->getAllKeys();
        $response = Constant::ZERO;

        foreach ($memcacheData as $data) {
            $payload = unserialize($memcached->get($data));
            foreach ($payload as $key => $value) {
                if ($this->guardMatch($authProvider, $key)) {
                    $response++;
                };
            }
        }

        return $response;
    }

    /**
     * @param $authProvider
     * @param $data
     * @return int
     */
    private function guardMatch($authProvider, $data): int
    {
        $active = Constant::ZERO;
        $guards = authGuards($authProvider);

        foreach ($guards as $guardKey => $guardValue) {
            if (strpos($data, $guardValue) !== false) {
                $active++;
            }
        }

        return $active;
    }

    /**
     * @param string $type
     * @param string $key
     * @param int $value
     * @return void
     */
    private function storePulseRecord(string $type, string $key, int $value): void
    {
        $this->pulse->record(
            type: $type,
            key: $key,
            value: $value,
            timestamp: CarbonImmutable::now()->getTimestamp()
        )->max()->onlyBuckets();
    }

    /**
     * @return string[]
     */
    private function supportedDrivers(): array
    {
        return [
            'database',
            'file',
            'redis',
            'memcached'
        ];
    }

    /**
     * @return string[]
     */
    private function supportedPersonalAccessTokenMethods(): array
    {
        return [
            'sanctum',
            'passport'
        ];
    }

    /**
     * @return Repository|\Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|mixed|string
     * @throws \ReflectionException
     */
    private function currentApiDriver()
    {
        $apiDriver = config(
            'auth.guards.sanctum.driver',
            Constant::API_DRIVER_SANCTUM
        );

        $userClass = new ReflectionClass(
            config('auth.providers.users.model', Constant::DEFAULT_MODEL)
        );
        $traits = $userClass->getTraitNames();

        return in_array(Constant::PASSPORT_NAMESPACE, $traits)
            ? Constant::API_DRIVER_PASSPORT
            : (in_array(Constant::SANCTUM_NAMESPACE, $traits)
                ? Constant::API_DRIVER_SANCTUM
                : $apiDriver
            );
    }

    /**
     * @param $apiDriver
     * @param $authProvider
     * @return int
     */
    private function apiLoginCounts($apiDriver, $authProvider): int
    {
        $key = "active_session_" . $authProvider;
        
        $response = match ($apiDriver) {
            'sanctum' => $this->recordSanctum($authProvider),
            'passport' => $this->recordPassport($authProvider),
            default => Constant::ZERO,
        };

        if (in_array($apiDriver, $this->supportedPersonalAccessTokenMethods())) {
            $this->storePulseRecord('api_hit', $key, $response);
        }

        return $response;
    }

    /**
     * @param $driver
     * @param $authProvider
     * @return int
     */
    private function webLoginCounts($authProvider): int
    {
        $key = "active_session_" . $authProvider;
        $driver = env('SESSION_DRIVER', 'file');

        $response = match ($driver) {
            'database' => $this->recordDatabase($authProvider),
            'file' => $this->countActiveFileSessions($authProvider),
            'redis' => $this->countActiveRedisSession($authProvider),
            'memcached' => $this->countActiveMemcachedSession($authProvider),
            default => throw new RuntimeException(
                'Session driver for ' . $driver . ' is not yet implemented.'
            ),
        };

        if (in_array($driver, $this->supportedDrivers())) {
            $this->storePulseRecord(
                'login_hit',
                $key,
                $response
            );
        }

        return $response;
    }
}

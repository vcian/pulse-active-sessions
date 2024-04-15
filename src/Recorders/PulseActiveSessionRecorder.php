<?php

namespace Vcian\Pulse\PulseActiveSessions\Recorders;

use App\Models\User;
use Carbon\CarbonImmutable;
use Exception;
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
use RuntimeException;
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
     * Record the job.
     *
     * @param SharedBeat $event
     * @return void
     */
    public function record(SharedBeat $event): void
    {
        try {
            $authProviders = authProviders();
            foreach ($authProviders as $authProvider) {
                $activeSessions = [
                    'web' => Constant::ZERO,
                    'api' => Constant::ZERO,
                    'total' => Constant::ZERO,
                    'api_driver' => Constant::API_DRIVER_SANCTUM, // Default value for api_driver
                ];

                if ($event->time->second % Constant::RENDER_TIME_SEC !== Constant::ZERO) {
                    return;
                }

                $driver = env('SESSION_DRIVER', 'file');
                $apiDriver = config('auth.guards.api.driver');

                if (!empty(config('auth.guards.sanctum'))) {
                    $apiDriver = config('auth.guards.sanctum.driver', $apiDriver);
                }

                $userClass = new ReflectionClass(config('auth.providers.users.model', User::class));
                $traits = $userClass->getTraitNames();

                $apiDriver = in_array(\Laravel\Passport\HasApiTokens::class, $traits) ? 'passport' :
                    (in_array(\Laravel\Sanctum\HasApiTokens::class, $traits) ? 'sanctum' : $apiDriver);

                $activeSessions['api_driver'] = $apiDriver;

                $activeSessions['web'] = match ($driver) {
                    'database' => $this->recordDatabase($authProvider),
                    'file' => $this->countActiveFileSessions($authProvider),
                    'redis' => $this->countActiveRedisSession($authProvider),
                    'memcached' => $this->countActiveMemcachedSession($authProvider),
                    default => throw new RuntimeException('Session driver for ' . $driver . ' is not yet implemented.'),
                };

                if (in_array($driver, ['database', 'file', 'redis', 'memcached'])) {
                    $this->pulse->record(
                        type: 'login_hit',
                        key: "active_session_" . $authProvider,
                        value: $activeSessions['web'],
                        timestamp: CarbonImmutable::now()->getTimestamp(),
                    )->max()->onlyBuckets();
                }

                $activeSessions['api'] = match ($apiDriver) {
                    'sanctum' => $this->recordSanctum($authProvider),
                    'passport' => $this->recordPassport($authProvider),
                    default => Constant::ZERO, // You may want to set a default value based on your requirements
                };

                if (in_array($apiDriver, ['sanctum', 'passport'])) {
                    $this->pulse->record(
                        type: 'api_hit',
                        key: "active_session_" . $authProvider,
                        value: $activeSessions['api'],
                        timestamp: CarbonImmutable::now()->getTimestamp(),
                    )->max()->onlyBuckets();
                }

                $activeSessions['total'] = $activeSessions['web'] + $activeSessions['api'];

                $this->pulse->set('pulse_active_session_' . $authProvider, 'result', json_encode($activeSessions));
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
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
        $users = Token::query()->where(function ($query) {
            $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
        })->where('revoked', 0)->pluck('user_id');  // Consider only tokens that are not revoked

        return authProviderModel($authProvider)->whereIn('id', $users)->count();
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
        $activeSession = Constant::ZERO;

        foreach (glob(base_path() . "/storage/framework/sessions/*") as $filename) {
            $payload = unserialize(file_get_contents($filename));
            $lastActivity = $payload['auth']['password_confirmed_at'] ?? '';
            if ($lastActivity && now()->subMinutes(config('session.lifetime'))->timestamp < $lastActivity) {
                foreach ($payload as $key => $value) {
                    if ($this->guardMatch($authProvider, $key)) {
                        $activeSession++;
                    }
                }
            }
        }

        return $activeSession;
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
        $activeSession = Constant::ZERO;

        if ($sessionData) {
            foreach ($sessionData as $session) {
                foreach ($session as $key => $value) {
                    if ($this->guardMatch($authProvider, $key)) {
                        $activeSession++;
                    }
                }
            }
        }

        return $activeSession;
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
        $activeSession = Constant::ZERO;

        foreach ($memcacheData as $data) {
            $payload = unserialize($memcached->get($data));
            foreach ($payload as $key => $value) {
                if ($this->guardMatch($authProvider, $key)) {
                    $activeSession++;
                };
            }
        }

        return $activeSession;
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
}

<?php

namespace Vcian\Pulse\PulseActiveSessions\Recorders;

use App\Models\User;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Config\Repository;
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
        protected Pulse $pulse,
        protected Repository $config
    ) {
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
            $activeSessions = [
                'web' => Constant::ZERO,
                'api' => Constant::ZERO,
                'total' => Constant::ZERO,
                'api_driver' => 'sanctum', // Default value for api_driver
            ];

            if ($event->time->second % Constant::RENDER_TIME_SEC !== Constant::ZERO) {
                return;
            }

            $driver = env('SESSION_DRIVER', 'file');
            $apiDriver = config('auth.guards.api.driver');

            if (!empty(config('auth.guards.sanctum'))) {
                $apiDriver = config('auth.guards.sanctum.driver', $apiDriver);
            }

            $userClass = new ReflectionClass(User::class);
            $traits = $userClass->getTraitNames();

            $apiDriver = in_array(\Laravel\Passport\HasApiTokens::class, $traits) ? 'passport' :
                (in_array(\Laravel\Sanctum\HasApiTokens::class, $traits) ? 'sanctum' : $apiDriver);

            $activeSessions['api_driver'] = $apiDriver;

            $activeSessions['web'] = match ($driver) {
                'database' => $this->recordDatabase(),
                'file' => $this->countActiveFileSessions(),
                'redis' => $this->countActiveRedisSession(),
                'memcached' => $this->countActiveMemcachedSession(),
                default => throw new RuntimeException('Session driver for ' . $driver . ' is not yet implemented.'),
            };

            if (in_array($driver, ['database', 'file', 'redis', 'memcached'])) {
                $this->pulse->record(
                    type: 'login_hit',
                    key: "active_session",
                    value: $activeSessions['web'],
                    timestamp: CarbonImmutable::now()->getTimestamp(),
                )->max()->onlyBuckets();
            }

            $activeSessions['api'] = match ($apiDriver) {
                'sanctum' => $this->recordSanctum(),
                'passport' => $this->recordPassport(),
                default => Constant::ZERO, // You may want to set a default value based on your requirements
            };

            if (in_array($apiDriver, ['sanctum', 'passport'])) {
                $this->pulse->record(
                    type: 'api_hit',
                    key: "active_session",
                    value: $activeSessions['api'],
                    timestamp: CarbonImmutable::now()->getTimestamp(),
                )->max()->onlyBuckets();
            }

            $activeSessions['total'] = $activeSessions['web'] + $activeSessions['api'];
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }

        $this->pulse->set('pulse_active_session', 'result', json_encode($activeSessions));
    }

    /**
     * Record the sanctum token count.
     *
     * @return int
     */
    private function recordSanctum(): int
    {
        return PersonalAccessToken::where(function ($query) {
            $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
        })->count();
    }

    /**
     * Record the passport token count.
     * @return int
     */
    private function recordPassport(): int
    {
        return Token::where(function ($query) {
            $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
        })
            ->where('revoked', 0) // Consider only tokens that are not revoked
            ->count();
    }

    /**
     * Record the session count
     *
     * @return int
     */
    private function recordDatabase(): int
    {
        return DB::table(config("session.table"))
            ->where('last_activity', '>', now()->subMinutes(config('session.lifetime')))
            ->whereNotNull('user_id')
            ->count();
    }

    /**
     * Get count of active file sessions
     * 
     * @return int
     */
    private function countActiveFileSessions(): int
    {
        $sessionPath = storage_path('framework/sessions');
        $activeSessions = Constant::ZERO;
        $files = array_diff(scandir($sessionPath), ['.', '..']);
        $files = array_values(array_diff($files, ['.gitignore'])); // Exclude .ignore file

        foreach ($files as $file) {
            $content = file_get_contents($sessionPath . '/' . $file);
            $data = unserialize($content);

            if (isset($data['_token'], $data['auth']['password_confirmed_at'])) {
                $lastActivity = $data['auth']['password_confirmed_at'];

                if (now()->subMinutes(config('session.lifetime'))->timestamp < $lastActivity) {
                    $activeSessions++;
                }
            }
        }

        return $activeSessions;
    }

    /**
     * Get count of active redis sessions
     *
     * @return int
     */
    private function countActiveRedisSession(): int
    {
        $keys = array_map(fn($key) => str_replace(config('database.redis.options.prefix'), '', $key), Redis::keys('*'));
        $data = array_map(fn($data) => unserialize(unserialize($data)), Redis::mget($keys));
        return count($keys);
    }

    /**
     * Get count of active memcached sessions
     *
     * @return int
     */
    private function countActiveMemcachedSession(): int
    {
        // Define the prefix used by Laravel for storing sessions in Memcached
        $prefix = config('cache.prefix');

        // Get all keys from the Memcached server
        $allKeys = Cache::store('memcached')->getStore()->getMemcached()->getAllKeys();
        
        // Filter the keys to get only the session keys
        $sessionKeys = array_filter($allKeys, function ($key) use ($prefix) {
            return strpos($key, $prefix) === 0;
        });
        
        return count($sessionKeys);
    }
}

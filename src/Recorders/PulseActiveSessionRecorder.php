<?php

namespace Vcian\Pulse\PulseActiveSessions\Recorders;

use App\Models\User;
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

            $activeSessions['api_driver'] = $apiDriver;
            $userClass = new ReflectionClass(User::class);
            $traits = $userClass->getTraitNames();

            if (in_array(\Laravel\Passport\HasApiTokens::class, $traits)) {
                // Laravel Passport's HasApiTokens trait is used
                $activeSessions['api_driver'] = $apiDriver ='passport';
            } elseif (in_array(\Laravel\Sanctum\HasApiTokens::class, $traits)) {
                // Laravel Sanctum's HasApiTokens trait is used
                $activeSessions['api_driver'] = $apiDriver = 'sanctum';
            }

            if ($driver == 'database') {
                $activeSessions['web'] = $this->recordDatabase();
            } else if ($driver == 'file') {
                $sessionPath = storage_path('framework/sessions');
                $activeSessions['web'] = $this->countActiveFileSessions($sessionPath);
            } else if ($driver == 'redis') {
                $keys = array_map(fn($key) => str_replace(config('database.redis.options.prefix'), '', $key), Redis::keys('*'));
                $data = array_map(fn($data) => unserialize(unserialize($data)), Redis::mget($keys));
                $activeSessions['web'] = count($keys);
            } else if ($driver == 'memcached') {
                // Define the prefix used by Laravel for storing sessions in Memcached
                $prefix = config('cache.prefix');

                // Get all keys from the Memcached server
                $allKeys = Cache::store('memcached')->getStore()->getMemcached()->getAllKeys();
                // dd(2, $allKeys);
                // Filter the keys to get only the session keys
                $sessionKeys = array_filter($allKeys, function ($key) use ($prefix) {
                    return strpos($key, $prefix) === 0;
                });
                
                $activeSessions['web'] = count($sessionKeys);
            } else {
                throw new RuntimeException('Session driver for ' . $driver . ' is not yet implemented.');
            }

            $activeSessions['api'] = match ($apiDriver) {
                'sanctum' => $this->recordSanctum(),
                'passport' => $this->recordPassport(),
                default => Constant::ZERO, // You may want to set a default value based on your requirements
            };

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
     * @param string $sessionPath
     * @return int
     */
    private function countActiveFileSessions(string $sessionPath): int
    {
        $activeSessions = Constant::ZERO;
        $files = array_diff(scandir($sessionPath), ['.', '..']);

        foreach ($files as $file) {
            $content = file_get_contents($sessionPath . '/' . $file);
            $data = unserialize($content);

            if (isset($data['_token'], $data['_last_activity'])) {
                $lastActivity = $data['_last_activity'];

                if (now()->subMinutes(config('session.lifetime'))->timestamp < $lastActivity) {
                    $activeSessions++;
                }
            }
        }

        return $activeSessions;
    }
}

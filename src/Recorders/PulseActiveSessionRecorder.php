<?php

namespace Vcian\Pulse\PulseActiveSessions\Recorders;

use App\Models\User;
use Exception;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Laravel\Passport\Token;
use Laravel\Pulse\Events\SharedBeat;
use Laravel\Pulse\Pulse;
use Laravel\Sanctum\PersonalAccessToken;
use ReflectionClass;

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

    public function record(SharedBeat $event): void
    {
        try {
            //code...
            $activeSessions['web'] = 0;
            $activeSessions['api'] = 0;
    
            if ($event->time->second % 5 !== 0) {
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
                $activeSessions['web'] = DB::table(config("session.table")
                    ->where('last_activity', '>', now()->subMinutes(config('session.lifetime')))
                    ->whereNotNull('user_id')
                    ->count();
            } else if ($driver == 'file') {
                $sessionPath = storage_path('framework/sessions');
    
                // Get all session files
                $files = scandir($sessionPath);
    
                // Iterate through each file
                foreach ($files as $file) {
                    Session::flush();
                    if ($file !== '.' && $file !== '..') {
                        // Read the content of the session file
                        $content = file_get_contents($sessionPath . '/' . $file);
    
                        // Decode the session data
                        $data = unserialize($content);
    
                        // Check if the session is not expired
                        if (isset($data['_token']) && isset($data['_last_activity'])) {
                            $lastActivity = $data['_last_activity'];
    
                            if (now()->subMinutes(config('session.lifetime'))->timestamp < $lastActivity) {
                                $activeSessions['web'] = $activeSessions['web'] + 1;
                            }
                        }
                    }
                }
    
            }

            if($apiDriver == 'sanctum') {
                $activeSessions['api'] = PersonalAccessToken::where(function ($query) {
                    $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })->count();
            } else if ($apiDriver == 'passport') { 
                $activeSessions['api'] = Token::where(function ($query) {
                    $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->where('revoked', 0) // Consider only tokens that are not revoked
                ->count();
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
        

        $this->pulse->set('pulse_active_session', 'result', json_encode($activeSessions));
    }
}

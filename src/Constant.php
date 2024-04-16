<?php

namespace Vcian\Pulse\PulseActiveSessions;
use App\Models\User;

class Constant
{
    public const ZERO = 0;
    public const RENDER_TIME_SEC = 5;
    public const API_DRIVER_SANCTUM = "sanctum";
    public const API_DRIVER_PASSPORT = "passport";
    public const WEB = 'web';
    public const USERS = 'users';

    public const DEFAULT_MODEL = User::class;
    public const PASSPORT_NAMESPACE = \Laravel\Passport\HasApiTokens::class;
    public const SANCTUM_NAMESPACE = \Laravel\Sanctum\HasApiTokens::class;
}

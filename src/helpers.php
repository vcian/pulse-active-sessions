<?php

use Vcian\Pulse\PulseActiveSessions\Constant;

if (!function_exists('authProviders')) {
    /**
     * @return array
     */
    function authProviders(): array
    {
        return array_keys(config('auth.providers')) ?? [Constant::USERS];
    }
}
if (!function_exists('authProviderModel')) {
    /**
     * @param $provider
     * @return mixed|string
     */
    function authProviderModel($provider)
    {
        if (!$provider) {
            return '';
        }

        return call_user_func(config('auth.providers.' . $provider . '.model') . '::query');
    }
}

if (!function_exists('authGuards')) {
    /**
     * @param $authProvider
     * @return array
     */
    function authGuards($authProvider): array
    {
        $authGuards = config('auth.guards');
        return array_keys(array_filter($authGuards, function ($guard) use ($authProvider) {
            return ($guard['provider'] === $authProvider && $guard['driver'] === 'session');
        }));
    }
}

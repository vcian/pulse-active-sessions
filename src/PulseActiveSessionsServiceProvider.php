<?php
/**
 * @author Aaron Francis <aarondfrancis@gmail.com|https://twitter.com/aarondfrancis>
 */

namespace Vcian\Pulse\PulseActiveSessions;

use Vcian\Pulse\PulseActiveSessions\Livewire\PulseActiveSessions;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Livewire\LivewireManager;

class PulseActiveSessionsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'pulse_active_session');

        $this->callAfterResolving('livewire', function (LivewireManager $livewire, Application $app) {
            $livewire->component('pulse_active_session', PulseActiveSessions::class);
        });
    }
}

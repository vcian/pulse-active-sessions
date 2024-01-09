<?php

namespace Vcian\Pulse\PulseActiveSessions\Livewire;

use Illuminate\Support\Facades\View;
use Laravel\Pulse\Facades\Pulse;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;
use Livewire\Livewire;

#[Lazy]
class PulseActiveSessions extends Card
{
    public function render()
    {
        // Get the data out of the Pulse data store.
        $webLoginCount = Pulse::values('pulse_active_session', ['result'])->first();

        $webLoginCount = $webLoginCount
            ? json_decode($webLoginCount->value, associative: true, flags: JSON_THROW_ON_ERROR)
            : [];

        if (Livewire::isLivewireRequest()) {
            $this->dispatch('servers-chart-update-session', servers: $webLoginCount);
        }

        // Get login count periodically
        [$session, $time, $runAt] = $this->remember(fn () => Pulse::graph(
            ['login_hit', 'api_hit'],
            'max',
            $this->periodAsInterval(),
        ));

        if (Livewire::isLivewireRequest()) {
            $this->dispatch('session-chart-update', session: $session);
        }
        
        return View::make('pulse_active_session::livewire.pulse_active_session', [
            'webLoginCount' => $webLoginCount,
            'time' => $time,
            'runAt' => $runAt,
            'session' => $session,
        ]);
    }

    protected function css()
    {
        return __DIR__.'/../../resources/css/style.css';
    }
}

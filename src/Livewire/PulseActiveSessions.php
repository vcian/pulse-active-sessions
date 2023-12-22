<?php

namespace Vcian\Pulse\PulseActiveSessions\Livewire;

use Illuminate\Support\Facades\View;
use Laravel\Pulse\Facades\Pulse;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;
use Livewire\Livewire;

class PulseActiveSessions extends Card
{
    #[Lazy]
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
        
        return View::make('pulse_active_session::livewire.pulse_active_session', [
            'webLoginCount' => $webLoginCount,
        ]);
    }

    protected function css()
    {
        return __DIR__.'/../../resources/css/style.css';
    }
}

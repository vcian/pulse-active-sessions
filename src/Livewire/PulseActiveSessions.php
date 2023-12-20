<?php
/**
 * @author Aaron Francis <aarondfrancis@gmail.com|https://twitter.com/aarondfrancis>
 */

namespace Vcian\Pulse\PulseActiveSessions\Livewire;

use Illuminate\Support\Facades\View;
use Laravel\Pulse\Facades\Pulse;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;

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

        return View::make('pulse_active_session::livewire.pulse_active_session', [
            'webLoginCount' => $webLoginCount,
        ]);
    }
}

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
    public $webLoginCount = [];
    public $time;
    public $runAt;
    public $session;
    public $filters;
    public $provider;

    /**
     * @return void
     */
    public function mount()
    {
        $this->filters = authProviders();
        $this->session = collect([]);
        $this->provider = authProviders()[0];
    }

    /**
     * @return \Illuminate\Contracts\View\View
     * @throws \JsonException
     */
    public function render()
    {
        // Get the data out of the Pulse data store.
        $this->webLoginCount = Pulse::values('pulse_active_session_' . $this->provider, ['result'])->first();

        $this->webLoginCount = $this->webLoginCount
            ? json_decode($this->webLoginCount->value, associative: true, flags: JSON_THROW_ON_ERROR)
            : [];

        if (Livewire::isLivewireRequest()) {
            $this->dispatch('servers-chart-update-session', servers: $this->webLoginCount);
        }
        // Get login count periodically
        [$this->session, $this->time, $this->runAt] = $this->remember(fn () => Pulse::graph(
            ['login_hit', 'api_hit'],
            'max',
            $this->periodAsInterval(),
        ));

        if (Livewire::isLivewireRequest()) {
            $this->session = $this->session->filter(
                function ($item, $key) {
                    return $key == 'active_session_' . $this->provider;
                }
            );
            $this->dispatch('session-chart-update', session: $this->session);
        }

        return View::make('pulse_active_session::livewire.pulse_active_session');
    }

    /**
     * @param $provider
     * @return void
     * @throws \JsonException
     */
    public function filterByProviders($provider)
    {
        $this->provider = $provider;

        $this->webLoginCount = Pulse::values('pulse_active_session_' . $this->provider, ['result'])->first();

        $this->session = $this->session->filter(function ($item, $key) use ($provider) {
            return $key == 'active_session_' . $provider;
        });

        $this->webLoginCount = $this->webLoginCount
            ? json_decode($this->webLoginCount->value, associative: true, flags: JSON_THROW_ON_ERROR)
            : [];
    }

    /**
     * @return string
     */
    protected function css()
    {
        return __DIR__ . '/../../resources/css/style.css';
    }
}

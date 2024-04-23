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
    public array|object $webLoginCount = [];
    public string $time;
    public string $runAt;
    public object $session;
    public array $filters;
    public string $provider;

    /**
     * @return void
     */
    public function mount(): void
    {
        $this->filters = authProviders();
        $this->session = collect();
        $this->provider = $this->filters[0];
    }

    /**
     * @return \Illuminate\Contracts\View\View
     * @throws \JsonException
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        // Get the data out of the Pulse data store.
        $this->webCount($this->provider);

        if (Livewire::isLivewireRequest()) {
            $this->dispatch(
                'servers-chart-update-session',
                servers: $this->webLoginCount
            );
        }

        // Get login count periodically
        [$this->session, $this->time, $this->runAt] = $this->pulseGraph();

        if (Livewire::isLivewireRequest()) {
            $this->session = $this->sessionFilter();
            $this->dispatch(
                'session-chart-update',
                session: $this->session
            );
        }

        return View::make('pulse_active_session::livewire.pulse_active_session');
    }

    /**
     * @param $provider
     * @return void
     * @throws \JsonException
     */
    public function filterByProviders($provider): void
    {
        $this->provider = $provider;
        $this->webCount($this->provider);
        $this->session = $this->sessionFilter();
    }

    /**
     * @param string $provider
     * @return void
     * @throws \JsonException
     */
    public function webCount(string $provider): void
    {
        $this->webLoginCount = json_decode(
            Pulse::values(
                'pulse_active_session_' . $this->provider,
                ['result']
            )->first()?->value ?: '[]',
            true,
            JSON_THROW_ON_ERROR
        );
    }

    /**
     * @return array
     */
    public function pulseGraph(): array
    {
        return $this->remember(fn() => Pulse::graph(
            ['login_hit', 'api_hit'],
            'max',
            $this->periodAsInterval(),
        ));
    }

    /**
     * @return mixed
     */
    public function sessionFilter(): mixed
    {
        return $this->session->filter(
            function ($item, $key) {
                return $key == 'active_session_' . $this->provider;
            }
        );
    }

    /**
     * @return string
     */
    protected function css()
    {
        return __DIR__ . '/../../resources/css/style.css';
    }
}

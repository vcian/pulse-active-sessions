<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header name="Active Sessions" title="Time: {{ number_format($time) }}ms; Run at: {{ $runAt }};">
        <x-slot:icon>
                <x-pulse_active_session::icons.session />
        </x-slot:icon>
        <x-slot:actions>
            <div class="flex flex-grow">
                <div class="w-full flex items-center gap-4">
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 font-medium">
                            <div class="h-0.5 w-3 rounded-full bg-[#9333ea]"></div>
                            Api
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 font-medium">
                            <div class="h-0.5 w-3 rounded-full bg-[#eab308]"></div>
                            Web
                        </div>
                    </div>
                </div>
            </div>
        </x-slot:actions>
    </x-pulse::card-header>
    <x-pulse::scroll :expand="$expand" wire:poll.5s="">
        <div class="h-2 first:h-0"></div>
        @isset($webLoginCount['total'])
        <div class="flex justify-between mt-3 mb-3">
            <div class="">
                <div class="whitespace-nowrap tabular-nums">
                    <span class="session-label web-label text-xs">Web</span>
                    <span class="text-sm">{{ $webLoginCount['total'] != 0 ? round(($webLoginCount['web'] / $webLoginCount['total']) * 100, 2)  : 0}}%</span>
                </div>
                <div class="h-2 first:h-0"></div>
                <div class="whitespace-nowrap tabular-nums">
                    <span class="session-label api-label text-xs">Api</span>
                    <span class="text-sm">{{ $webLoginCount['total'] != 0 ? round(($webLoginCount['api'] / $webLoginCount['total']) * 100, 2) : 0 }}%</span>
                </div>
            </div>
            <div class="">
                <div wire:ignore x-data="storageChartDocker({
                        total: {{ $webLoginCount['total'] }},
                        web: {{ $webLoginCount['web'] }},
                        api: {{ $webLoginCount['api'] }},
                    })">
                    <canvas x-ref="canvas" width="70" height="70" class=""></canvas>
                </div>
            </div>
        </div>
        @endisset
        @if (!count($webLoginCount))
            <x-pulse::no-results />
        @else
            <div class="grid grid-cols-1 @lg:grid-cols-2 @3xl:grid-cols-3 @6xl:grid-cols-4 gap-2">
                <x-pulse::table>
                    <colgroup>
                        <col width="100%" />
                        <col width="0%" />
                        <col width="0%" />
                    </colgroup>
                    <x-pulse::thead>
                        <tr>
                            <x-pulse::th>Platform</x-pulse::th>
                            <x-pulse::th class="text-right">Count</x-pulse::th>
                        </tr>
                    </x-pulse::thead>
                    <tbody>
                        <tr class="h-2 first:h-0"></tr>
                        <tr wire:key="{{ $webLoginCount['web'] }}">
                            <x-pulse::td class="max-w-[1px]">
                                <code class="block text-xs text-gray-900 dark:text-gray-100 truncate" title="">
                                    Web ({{ config('session.driver') }})
                                </code>
                                
                            </x-pulse::td>
                            <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                {{ $webLoginCount['web'] }}
                            </x-pulse::td>
                        </tr>
                        <tr class="h-2 first:h-0"></tr>
                        <tr wire:key="{{ $webLoginCount['web'] }}">
                            <x-pulse::td class="max-w-[1px]">
                                <code class="block text-xs text-gray-900 dark:text-gray-100 truncate" title="">
                                 Api ({{ $webLoginCount['api_driver'] }})
                                </code>
                                
                            </x-pulse::td>
                            <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                {{ $webLoginCount['api'] }}
                            </x-pulse::td>
                        </tr>
                    </tbody>
                </x-pulse::table>
            </div>
        @endif
        <hr class="mt-4 mb-3 border-gray-200 dark:border-gray-700">
        <x-pulse::card-header name="Sessions" title="Time: {{ number_format($time) }}ms; Run at: {{ $runAt }};"
                details="past {{ $this->periodForHumans() }}">
        </x-pulse::card-header>
        @if ($session->isEmpty())
            <x-pulse::no-results />
        @else
            <div class="grid gap-3 mx-px mb-px">
                @foreach ($session as $queue => $readings)
                    <div wire:key="{{ $queue }}">
                        @php
                            $highest = $readings->flatten()->max();
                        @endphp

                        <div class="mt-3 relative">
                            <div
                                class="absolute -left-px -top-2 max-w-fit h-4 flex items-center px-1 text-xs leading-none text-white font-bold bg-purple-500 rounded after:[--triangle-size:4px] after:border-l-purple-500 after:absolute after:right-[calc(-1*var(--triangle-size))] after:top-[calc(50%-var(--triangle-size))] after:border-t-[length:var(--triangle-size)] after:border-b-[length:var(--triangle-size)] after:border-l-[length:var(--triangle-size)] after:border-transparent">
                                {{ number_format($highest) }}
                            </div>
                            <div wire:ignore class="h-14" x-data="sessionChart({
                                queue: '{{ $queue }}',
                                readings: @js($readings),
                                sampleRate: 1,
                            })">
                                <canvas x-ref="canvas"
                                    class="ring-1 ring-gray-900/5 dark:ring-gray-100/10 bg-gray-50 dark:bg-gray-800 rounded-md shadow-sm"></canvas>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-pulse::scroll>
</x-pulse::card>

@script
<script>
let api_driver = "{{ $webLoginCoun['api_driver'] ?? 'sanctum' }}"
Alpine.data('storageChartDocker', (config) => ({
    init() {
        let chart = new Chart(
            this.$refs.canvas,
            {
                type: 'doughnut',
                data: {
                    labels: ['Api', 'Web'],
                    datasets: [
                        {
                            data: [
                                config.api,
                                config.web,
                            ],
                            backgroundColor: [
                                '#9333ea',
                                '#eab308',
                            ],
                            hoverBackgroundColor: [
                                '#9333ea',
                                '#eab308',
                            ],
                            hoverOffset: 4
                        },
                    ],
                },
                options: {
                    borderWidth: 0,
                    plugins: {
                        legend: {
                            display: false,
                            position: 'right',
                        },
                        tooltip: {
                            enabled: true,
                            intersect: false,
                            mode: 'nearest',
                            callbacks: {
                                label: function(item) {
                                    return (item.dataset.data.reduce((a, b) => a + b, 0) != 0)  ? parseFloat(item.parsed / item.dataset.data.reduce((a, b) => a + b, 0) * 100).toFixed(2) + '%' : 0;
                                }
                            },
                            displayColors: false,
                        }
                    },
                },
            }
        )

        Livewire.on('servers-chart-update-session', ({ servers }) => {
            const storage = servers;

            if (chart === undefined) {
                return
            }

            if (storage === undefined && chart) {
                chart.destroy()
                chart = undefined
                return
            }

            chart.data.datasets[0].data = [
                storage.api,
                storage.web,
            ]
            chart.update()
        })
    }
}))

Alpine.data('sessionChart', (config) => ({
    init() {
        let chart = new Chart(
            this.$refs.canvas, {
                type: 'line',
                data: {
                    labels: this.labels(config.readings),
                    datasets: [{
                            label: 'Web Login',
                            borderColor: '#eab308',
                            data: this.scale(config.readings.login_hit),
                            order: 1,
                        },
                        {
                            label: api_driver + ' Login',
                            borderColor: '#9333ea',
                            data: this.scale(config.readings.api_hit),
                            order: 2,
                        }
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    layout: {
                        autoPadding: false,
                        padding: {
                            top: 1,
                        },
                    },
                    datasets: {
                        line: {
                            borderWidth: 2,
                            borderCapStyle: 'round',
                            pointHitRadius: 10,
                            pointStyle: false,
                            tension: 0.2,
                            spanGaps: false,
                            segment: {
                                borderColor: (ctx) => ctx.p0.raw === 0 && ctx.p1.raw === 0 ?
                                    'transparent' : undefined,
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: false,
                        },
                        y: {
                            display: false,
                            min: 0,
                            max: this.highest(config.readings),
                        },
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            mode: 'index',
                            position: 'nearest',
                            intersect: false,
                            callbacks: {
                                beforeBody: (context) => context
                                    .map(item =>
                                        `${item.dataset.label}: ${1 < 1 ? '~' : ''}${item.formattedValue}`
                                    )
                                    .join(', '),
                                label: () => null,
                            },
                        },
                    },
                },
            }
        )

        Livewire.on('session-chart-update', ({
            session
        }) => {
            if (chart === undefined) {
                return
            }
            
            if (session[config.queue] === undefined && chart) {
                chart.destroy()
                chart = undefined
                return
            }

            chart.data.labels = this.labels(session[config.queue])
            chart.options.scales.y.max = this.highest(session[config.queue])
            chart.data.datasets[0].data = this.scale(session[config.queue].login_hit)
            chart.data.datasets[1].data = this.scale(session[config.queue].api_hit)
            chart.update()
        })
    },
    labels(readings) {
        return Object.keys(readings.login_hit)
    },
    scale(data) {
        return Object.values(data).map(value => value * (1 / 1))
    },
    highest(readings) {
        return Math.max(...Object.values(readings).map(dataset => Math.max(...Object.values(
            dataset)))) * (1 / 1)
    }
}))
</script>
@endscript
<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header name="Active Sessions">
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
                    <span class="text-sm">{{ round(($webLoginCount['web'] / $webLoginCount['total']) * 100, 2) }}%</span>
                </div>
                <div class="h-2 first:h-0"></div>
                <div class="whitespace-nowrap tabular-nums">
                    <span class="session-label api-label text-xs">Api</span>
                    <span class="text-sm">{{ round(($webLoginCount['api'] / $webLoginCount['total']) * 100, 2) }}%</span>
                </div>
            </div>
            <div class="">
                <div  x-data="storageChartDocker({
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
                                    Web
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
    </x-pulse::scroll>
</x-pulse::card>

@script
<script>
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
                                    return  parseFloat(item.parsed / item.dataset.data.reduce((a, b) => a + b, 0) * 100).toFixed(2) + '%';
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
</script>
@endscript
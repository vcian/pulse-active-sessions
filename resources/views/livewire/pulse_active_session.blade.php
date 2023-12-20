<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header name="Pulse Active Sessions">
        <x-slot:icon>
                <x-pulse_active_session::icons.session />
        </x-slot:icon>
    </x-pulse::card-header>
    <x-pulse::scroll :expand="$expand" wire:poll.5s="">
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
                            <x-pulse::th>Guard</x-pulse::th>
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

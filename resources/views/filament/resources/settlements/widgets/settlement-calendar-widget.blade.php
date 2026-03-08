<x-filament-widgets::widget>
    <div class="space-y-6">
        <x-filament::section class="overflow-hidden border-none shadow-xl ring-1 ring-gray-950/5 dark:ring-white/10">
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-primary-100 dark:bg-primary-900/40 rounded-lg">
                        <x-filament::icon icon="heroicon-o-calendar" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                    </div>
                    <span class="text-xl font-black tracking-tight text-gray-900 dark:text-white uppercase">Laporan Harian</span>
                </div>
            </x-slot>

            <x-slot name="headerEnd">
                <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-800 p-1 rounded-xl border border-gray-200 dark:border-gray-700">
                    <button wire:click="prevMonth" class="p-2 hover:bg-white dark:hover:bg-gray-700 rounded-lg transition-all shadow-sm group">
                        <x-filament::icon icon="heroicon-m-chevron-left" class="w-4 h-4 text-gray-600 dark:text-gray-400 group-hover:text-primary-500" />
                    </button>
                    
                    <div class="px-4 py-1.5 text-xs font-black text-gray-900 dark:text-white bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                        {{ strtoupper($monthName) }} {{ $year }}
                    </div>

                    <button wire:click="nextMonth" class="p-2 hover:bg-white dark:hover:bg-gray-700 rounded-lg transition-all shadow-sm group">
                        <x-filament::icon icon="heroicon-m-chevron-right" class="w-4 h-4 text-gray-600 dark:text-gray-400 group-hover:text-primary-500" />
                    </button>
                </div>
            </x-slot>

            <!-- Calendar Container -->
            <div class="mt-4 overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-800 shadow-inner bg-gray-50/50 dark:bg-gray-900/50 p-4 sm:p-6">
                <!-- Day Names -->
                <div class="grid grid-cols-7 gap-2 mb-4">
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                        <div class="text-center text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest py-1">
                            {{ $day }}
                        </div>
                    @endforeach
                </div>

                <!-- Calendar Content -->
                <div class="grid grid-cols-7 gap-2 sm:gap-4">
                    @for($i = 0; $i < $firstDayOfMonth; $i++)
                        <div class="aspect-square opacity-20">
                             <div class="w-full h-full rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700"></div>
                        </div>
                    @endfor

                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $dateStr = sprintf('%04d-%02d-%02d', $year, $currentMonth, $day);
                            $data = $settlements[$dateStr] ?? null;
                            $total = $data['total'] ?? 0;
                            $count = $data['cashier_count'] ?? 0;
                            $isToday = $dateStr === now()->format('Y-m-d');
                            $isSelected = $dateStr === $selectedDate;
                        @endphp
                        <button 
                            wire:click="selectDay('{{ $dateStr }}')"
                            class="relative aspect-square group transition-all duration-300 transform active:scale-95
                                {{ $isSelected ? 'z-20' : '' }}
                            "
                        >
                            <!-- Background & Border -->
                            <div class="absolute inset-0 rounded-2xl border-2 transition-all duration-300
                                {{ $isToday ? 'border-primary-500 shadow-lg shadow-primary-500/20' : 'border-transparent shadow-sm' }}
                                {{ $isSelected ? 'bg-primary-500 border-primary-500' : 'bg-white dark:bg-gray-800 hover:border-primary-400/50' }}
                            "></div>

                            <!-- Content -->
                            <div class="relative w-full h-full p-2 flex flex-col justify-between">
                                <span class="text-xs font-black 
                                    {{ $isSelected ? 'text-white' : ($isToday ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 dark:text-gray-500') }}
                                ">
                                    {{ sprintf('%02d', $day) }}
                                </span>

                                @if($total > 0)
                                    <div class="flex flex-col items-center">
                                        <div class="flex -space-x-1 mb-1">
                                            @for($c=0; $c < min($count, 3); $c++)
                                                <div class="w-3 h-3 rounded-full border border-white dark:border-gray-800 {{ $isSelected ? 'bg-white' : 'bg-primary-400' }} shadow-xs"></div>
                                            @endfor
                                        </div>
                                        <div class="text-[9px] sm:text-[11px] font-black leading-none
                                            {{ $isSelected ? 'text-white' : 'text-gray-900 dark:text-gray-100' }}
                                        ">
                                            {{ number_format($total/1000, 0) }}k
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Hover Tooltip Shorthand -->
                            @if($total > 0 && !$isSelected)
                                <div class="absolute -top-1 -right-1 flex h-4 w-4">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-4 w-4 bg-primary-500 text-[8px] font-black text-white items-center justify-center">
                                        {{ $count }}
                                    </span>
                                </div>
                            @endif
                        </button>
                    @endfor
                </div>
            </div>
        </x-filament::section>

        <!-- Aesthetic Detail View -->
        @if($selectedDayData)
            <div x-data="{ open: true }" x-show="open" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" class="relative group">
                <!-- Decorative Glow -->
                <div class="absolute -inset-1 bg-gradient-to-r from-primary-500 to-primary-600 rounded-3xl blur opacity-10 group-hover:opacity-20 transition duration-1000"></div>
                
                <div class="relative bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden">
                    <div class="flex flex-col lg:flex-row divide-y lg:divide-y-0 lg:divide-x divide-gray-100 dark:divide-gray-800">
                        
                        <!-- Side Info -->
                        <div class="lg:w-1/3 p-8 bg-gray-50/50 dark:bg-gray-800/30">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="w-12 h-12 bg-primary-500 rounded-2xl flex items-center justify-center shadow-lg shadow-primary-500/30 transform -rotate-3 group-hover:rotate-0 transition-transform duration-500">
                                    <x-filament::icon icon="heroicon-o-presentation-chart-line" class="w-6 h-6 text-white" />
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight italic">Daily Audit</h3>
                                    <p class="text-xs font-bold text-primary-500 uppercase tracking-widest">{{ \Carbon\Carbon::parse($selectedDate)->format('D, d M Y') }}</p>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                                    <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 text-center">Grand Total</span>
                                    <span class="block text-3xl font-black text-primary-600 dark:text-primary-400 text-center leading-none tracking-tighter">
                                        Rp {{ number_format($selectedDayData->sum('total_sales'), 0, ',', '.') }}
                                    </span>
                                </div>

                                <div class="flex justify-between items-center px-2">
                                    <div class="text-center">
                                        <span class="block text-[8px] font-black text-gray-400 uppercase tracking-widest">Shifts</span>
                                        <span class="text-lg font-black dark:text-white">{{ $selectedDayData->count() }}</span>
                                    </div>
                                    <div class="w-px h-8 bg-gray-200 dark:bg-gray-700"></div>
                                    <div class="text-center text-right">
                                        <span class="block text-[8px] font-black text-gray-400 uppercase tracking-widest text-right">Average</span>
                                        <span class="text-lg font-black dark:text-white">
                                            @php $avg = $selectedDayData->count() > 0 ? $selectedDayData->sum('total_sales') / $selectedDayData->count() : 0; @endphp
                                            {{ number_format($avg/1000, 1) }}k
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- List Items -->
                        <div class="lg:w-2/3 p-8">
                            <div class="flex items-center justify-between mb-6">
                                <h4 class="text-sm font-black text-gray-400 uppercase tracking-widest">Cashier Breakdown</h4>
                                <button @click="open = false" class="p-2 bg-gray-100 dark:bg-gray-800 hover:bg-red-50 dark:hover:bg-red-900/40 rounded-xl transition-all group/close">
                                    <x-filament::icon icon="heroicon-m-x-mark" class="w-4 h-4 text-gray-400 group-hover/close:text-red-500" />
                                </button>
                            </div>

                            <div class="grid gap-4 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                                @foreach($selectedDayData as $item)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-transparent hover:border-primary-500/30 hover:bg-white dark:hover:bg-gray-800 transition-all duration-300 group/item shadow-sm">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center font-black text-xs shadow-md shadow-primary-500/20 group-hover/item:scale-110 transition-transform">
                                                {{ substr($item['cashier'], 0, 1) }}
                                            </div>
                                            <div>
                                                <span class="block text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $item['cashier'] }}</span>
                                                <span class="block text-[10px] font-bold text-gray-400">Transaction Completed</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-6">
                                            <div class="text-right">
                                                <span class="block text-sm font-black text-gray-900 dark:text-white italic tracking-tighter">Rp {{ number_format($item['total_sales'], 0, ',', '.') }}</span>
                                            </div>
                                            <a href="{{ route('filament.admin.resources.settlements.view', $item['id']) }}" class="flex items-center justify-center w-8 h-8 rounded-xl bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 hover:bg-primary-500 hover:text-white transition-all transform hover:rotate-12">
                                                <x-filament::icon icon="heroicon-m-arrow-top-right-on-square" class="w-4 h-4" />
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; }
    </style>
</x-filament-widgets::widget>

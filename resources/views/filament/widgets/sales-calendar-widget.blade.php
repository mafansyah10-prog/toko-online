<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-4">
            <!-- View Mode Tabs -->
            <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700 pb-3">
                <button 
                    wire:click="setViewMode('daily')"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $viewMode === 'daily' ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
                >
                    📅 Harian
                </button>
                <button 
                    wire:click="setViewMode('monthly')"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $viewMode === 'monthly' ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
                >
                    📆 Bulanan
                </button>
                <button 
                    wire:click="setViewMode('yearly')"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $viewMode === 'yearly' ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
                >
                    📊 Tahunan
                </button>
            </div>

            <!-- Date Picker based on mode -->
            <div class="flex flex-wrap items-end gap-4">
                @if($viewMode === 'daily')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Tanggal</label>
                        <input 
                            type="date" 
                            wire:model.live="selectedDate"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        >
                    </div>
                @elseif($viewMode === 'monthly')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Bulan</label>
                        <input 
                            type="month" 
                            wire:model.live="selectedMonth"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        >
                    </div>
                @else
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Tahun</label>
                        <select 
                            wire:model.live="selectedYear"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        >
                            @foreach($this->getYearOptions() as $year => $label)
                                <option value="{{ $year }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            <!-- Sales Result -->
            @php
                $data = $this->getSalesData();
            @endphp
            <div class="mt-4 p-6 bg-gradient-to-r from-primary-500 to-primary-600 rounded-xl text-white">
                <div class="text-sm opacity-80 mb-1">{{ $data['label'] }}</div>
                <div class="text-3xl font-bold">Rp {{ number_format($data['sales'], 0, ',', '.') }}</div>
                <div class="text-sm opacity-80 mt-2">{{ $data['orders'] }} pesanan terbayar</div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

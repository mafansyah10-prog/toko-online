<x-filament-panels::page>
    <div class="flex flex-col items-center justify-center space-y-4">
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
            <div class="visible-print text-center">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(300)->generate(url('/')) !!}
            </div>
        </div>
        
        <div class="text-center">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Scan to Visit Website</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                <a href="{{ url('/') }}" target="_blank" class="hover:underline text-primary-600">
                    {{ url('/') }}
                </a>
            </p>
        </div>
    </div>
</x-filament-panels::page>

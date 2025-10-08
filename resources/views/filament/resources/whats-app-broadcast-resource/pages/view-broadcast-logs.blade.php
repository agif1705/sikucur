<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-800/25 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-500 dark:bg-blue-600 rounded-lg">
                        <x-heroicon-o-users class="h-6 w-6 text-white" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Penerima</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $record->total_recipients }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-800/25 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-500 dark:bg-green-600 rounded-lg">
                        <x-heroicon-o-check-circle class="h-6 w-6 text-white" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Berhasil Dikirim</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $record->total_sent }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-800/25 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-red-500 dark:bg-red-600 rounded-lg">
                        <x-heroicon-o-x-circle class="h-6 w-6 text-white" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Gagal Dikirim</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $record->total_failed }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-800/25 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-500 dark:bg-yellow-600 rounded-lg">
                        <x-heroicon-o-chart-bar class="h-6 w-6 text-white" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Tingkat Keberhasilan</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $record->total_recipients > 0 ? round(($record->total_sent / $record->total_recipients) * 100, 1) : 0 }}%
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Broadcast Info --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-800/25 p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Informasi Broadcast</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Judul</p>
                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $record->title }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($record->status === 'completed') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                        @elseif($record->status === 'failed') bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                        @elseif($record->status === 'sending') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                        @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300
                        @endif">
                        {{ match($record->status) {
                            'draft' => 'Draft',
                            'sending' => 'Mengirim',
                            'completed' => 'Selesai',
                            'failed' => 'Gagal',
                            default => $record->status
                        } }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Pengirim</p>
                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $record->sender->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Dikirim Pada</p>
                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $record->sent_at ? $record->sent_at->format('d/m/Y H:i:s') : '-' }}</p>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Pesan</p>
                <div class="mt-2 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="text-sm whitespace-pre-wrap text-gray-900 dark:text-gray-100">{{ $record->message }}</p>
                </div>
            </div>
        </div>

        {{-- Logs Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-800/25">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Detail Pengiriman</h3>
            </div>
            <div class="p-6">
                {{ $this->table }}
            </div>
        </div>
    </div>
</x-filament-panels::page>
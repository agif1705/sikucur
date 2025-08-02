<x-filament-panels::page>
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Bulan {{ $this->bulan }} {{ $this->year }}</h1>
    </div>

    {{ $this->table }}
</x-filament-panels::page>

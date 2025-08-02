@props([
    'active' => false,
    'activeClasses' => 'text-blue-600 border-blue-600',
    'inactiveClasses' => 'text-gray-500 hover:text-gray-700',
])
<li>
    {{-- @dump(request()->routeIs('home')) --}}
    <a wire:navigate
        {{ $attributes->class([
            'inline-flex items-center px-1 ml-4 pt-1 border-b-2 text-sm font-medium transition',
            $activeClasses => $active,
            $inactiveClasses => !$active,
        ]) }}>
        {{ Str::ucfirst($slot) }}
    </a>
</li>

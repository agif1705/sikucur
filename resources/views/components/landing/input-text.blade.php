@props([
    'type' => '#',
    'model' => '#',
])
@if ($type == 'textarea')
    <label class="fieldset-label mt-2 mb-3">{{ $slot }}</label>
    <textarea wire:model="{{ $model }}" class="textarea mb-3" name="{{ $slot }}"
        placeholder="{{ 'Masukan ' . $slot }}" cols="30" rows="10"></textarea>
@else
    <label class="fieldset-label mt-2 mb-3">{{ Str::ucfirst($slot) }}</label>
    <input wire:model="{{ $model }}" type="{{ $type }}" class="input" name="{{ $slot }}"
        placeholder="{{ 'Masukan ' . $slot }}" />
@endif

<div class="fi-ta-search-field mt-2 min-w-36 sm:mt-0">
    <label for="ppp-secret-remote-ont-port" class="fi-sr-only">
        Port Remote ONT
    </label>

    <x-filament::input.wrapper prefix="Port">
        <x-filament::input
            id="ppp-secret-remote-ont-port"
            type="number"
            min="1"
            max="65535"
            wire:model.live.debounce.500ms="remoteOntPort"
        />
    </x-filament::input.wrapper>
</div>

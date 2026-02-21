<x-filament::button
    type="button"
    color="primary"
    wire:click="generate"
    wire:loading.attr="disabled"
>
    <x-slot name="loadingIndicator">
        <x-filament::loading-indicator class="h-5 w-5" />
    </x-slot>
    {{ __('Generate') }}
</x-filament::button>

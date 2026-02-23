<div class="flex items-center gap-2 mb-2">
    <x-filament::link
        color="gray"
        tag="button"
        type="button"
        wire:click="selectAllLanguages"
        size="sm"
    >
        Select all
    </x-filament::link>
    <span class="text-gray-400 dark:text-gray-500 text-sm">·</span>
    <x-filament::link
        color="gray"
        tag="button"
        type="button"
        wire:click="clearLanguages"
        size="sm"
    >
        Clear
    </x-filament::link>
</div>

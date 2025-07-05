<x-filament::page>
    {{ $this->form }}

    <div class="mt-6">
        
         <x-filament::button wire:click="generate" color="success" icon="heroicon-o-bolt">
            Generate Images
        </x-filament::button>
    </div>
</x-filament::page>

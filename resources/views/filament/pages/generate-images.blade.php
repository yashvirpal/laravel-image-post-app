<x-filament::page>
    {{ $this->form }}

    <div class="mt-6">
        
         <x-filament::button wire:click="generate" color="success" icon="heroicon-o-bolt">
            Generate Images
        </x-filament::button>
    </div>
</x-filament::page>
{{-- @if ($errors->any())
    <div class="text-sm text-red-600 mb-4">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif --}}

<x-filament-panels::page>

    @php
    $record = $this->getRecord();
    $images = $record->images ?? [];
    $frontImage = $record->front_national_id ?? null;
    $backImage = $record->back_national_id ?? null;
    @endphp

    <div class="container mx-auto">
        <h1 class="text-3xl font-bold mb-4">Images</h1>

        <div class="mb-8">
            @if ($frontImage)
            <h2 class="text-lg font-bold mb-2">Front Image</h2>
            <div class="flex items-center justify-center mb-4">
                <img src="{{ $frontImage }}" alt="Cover Image" class="cursor-pointer max-w-full rounded-lg transition-transform hover:scale-110 cursor-zoom-in">
            </div>
            <hr class="mb-4 border-t border-gray-300">
            @else
            <p>No Front Image available.</p>
            @endif
        </div>

        <div>
            @if ($backImage)
            <h2 class="text-lg font-bold mb-2">Back Image</h2>
            <div class="flex items-center justify-center mb-4">
                <img src="{{ $backImage }}" alt="Cover Image" class="cursor-pointer max-w-full rounded-lg transition-transform hover:scale-110 cursor-zoom-in">
            </div>
            <hr class="mb-4 border-t border-gray-300">
            @else
            <p>No Back Image available.</p>
            @endif
        </div>


    </div>

</x-filament-panels::page>
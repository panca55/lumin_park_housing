<x-filament::widget>
    <x-filament::card>
        <div class="flex flex-col gap-3">
            <h2 class="text-lg font-bold">
                Landing Page
            </h2>

            <p class="text-sm text-gray-600">
                Akses website utama Lumin Park
            </p>

            <x-filament::button
                tag="a"
                href="{{ url('/') }}"
                icon="heroicon-o-home"
                color="primary"
            >
                Buka Landing Page
            </x-filament::button>
        </div>
    </x-filament::card>
</x-filament::widget>

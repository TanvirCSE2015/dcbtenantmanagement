<x-filament-panels::page>
    <x-filament::section
        heading="অনুসন্ধান"
        icon="heroicon-o-funnel" icon-color="primary"
        collapsible collapsed
    >

        {{ $this->form }}

    </x-filament::section>


    {{-- Table --}}
    <x-filament::section
    heading="বসবাসকারীদের তালিকা"
        icon="heroicon-m-user-group" icon-color="info">

        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>

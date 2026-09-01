<x-filament-panels::page>
     <x-filament::section
        heading="ফ্ল্যাট অনুসন্ধান"
        icon="heroicon-o-funnel" icon-color="primary"
        collapsible collapsed
    >

        {{ $this->form }}

    </x-filament::section>


    {{-- Table --}}
   <x-filament::section
        heading="ফ্ল্যাটসমূহের তালিকা"
        icon="heroicon-m-building-office-2" icon-color="info">

        {{ $this->table }}

    </x-filament::section>
</x-filament-panels::page>

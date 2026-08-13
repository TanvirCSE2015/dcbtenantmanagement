<?php

namespace App\Filament\Resources\Flats\Pages;

use App\Filament\Resources\Flats\FlatResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditFlat extends EditRecord
{
    protected static string $resource = FlatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->icon(Heroicon::Trash),
        ];
    }
}

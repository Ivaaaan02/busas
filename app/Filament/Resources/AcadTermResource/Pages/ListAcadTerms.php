<?php

namespace App\Filament\Resources\AcadTermResource\Pages;

use App\Filament\Resources\AcadTermResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAcadTerms extends ListRecords
{
    protected static string $resource = AcadTermResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

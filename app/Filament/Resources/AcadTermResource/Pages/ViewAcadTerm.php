<?php

namespace App\Filament\Resources\AcadTermResource\Pages;

use App\Filament\Resources\AcadTermResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAcadTerm extends ViewRecord
{
    protected static string $resource = AcadTermResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

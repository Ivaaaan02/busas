<?php

namespace App\Filament\Resources\AcadYearResource\Pages;

use App\Filament\Resources\AcadYearResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAcadYear extends ViewRecord
{
    protected static string $resource = AcadYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

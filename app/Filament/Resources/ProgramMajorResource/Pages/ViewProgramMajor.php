<?php

namespace App\Filament\Resources\ProgramMajorResource\Pages;

use App\Filament\Resources\ProgramMajorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProgramMajor extends ViewRecord
{
    protected static string $resource = ProgramMajorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

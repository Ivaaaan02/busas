<?php

namespace App\Filament\Resources\CurriculumResource\Pages;

use App\Filament\Resources\CurriculumResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCurriculum extends ViewRecord
{
    protected static string $resource = CurriculumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

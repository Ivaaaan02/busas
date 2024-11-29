<?php

namespace App\Filament\Resources\CurriculumResource\Pages;

use App\Filament\Resources\CurriculumResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCurriculum extends CreateRecord
{
    protected static string $resource = CurriculumResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}

<?php

namespace App\Filament\Resources\AcadTermResource\Pages;

use App\Filament\Resources\AcadTermResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAcadTerm extends CreateRecord
{
    protected static string $resource = AcadTermResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}

<?php

namespace App\Filament\Resources\AcadYearResource\Pages;

use App\Filament\Resources\AcadYearResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcadYear extends EditRecord
{
    protected static string $resource = AcadYearResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

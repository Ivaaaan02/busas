<?php

namespace App\Filament\Resources\AcadTermResource\Pages;

use App\Filament\Resources\AcadTermResource;
use App\Models\AcadYear;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcadTerm extends EditRecord
{
    protected static string $resource = AcadTermResource::class;

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

<?php

namespace App\Filament\Resources\ProgramMajorResource\Pages;

use App\Filament\Resources\ProgramMajorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProgramMajor extends EditRecord
{
    protected static string $resource = ProgramMajorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

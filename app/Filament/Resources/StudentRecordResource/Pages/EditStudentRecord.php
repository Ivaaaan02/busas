<?php

namespace App\Filament\Resources\StudentRecordResource\Pages;

use App\Filament\Resources\StudentRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentRecord extends EditRecord
{
    protected static string $resource = StudentRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

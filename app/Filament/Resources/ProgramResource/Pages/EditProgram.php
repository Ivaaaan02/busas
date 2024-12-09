<?php

namespace App\Filament\Resources\ProgramResource\Pages;

use App\Filament\Resources\ProgramResource;
use App\Models\College;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProgram extends EditRecord
{
    protected static string $resource = ProgramResource::class;

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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['campus_id'] = College::where('id', $data['college_id'])->first()->campus_id;

        return $data;
    }
}

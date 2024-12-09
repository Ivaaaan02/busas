<?php

namespace App\Filament\Resources\CurriculumResource\Pages;

use App\Filament\Resources\CurriculumResource;
use App\Models\College;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCurriculum extends EditRecord
{
    protected static string $resource = CurriculumResource::class;

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

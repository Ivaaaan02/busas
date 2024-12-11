<?php

namespace App\Filament\Resources\CurriculumResource\Pages;

use App\Filament\Resources\CurriculumResource;
use App\Models\AcadTerm;
use App\Models\AcadYear;
use App\Models\Program;
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
        $data['campus_id'] = Program::where('id', $data['program_id'])->first()->campus_id;
        $data['college_id'] = Program::where('id', $data['program_id'])->first()->college_id;
        $data['acad_year_id'] = AcadTerm::where('id', $data['acad_term_id'])->first()->acad_year_id;
        return $data;
    }
}

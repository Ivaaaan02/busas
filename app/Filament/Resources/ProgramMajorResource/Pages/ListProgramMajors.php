<?php

namespace App\Filament\Resources\ProgramMajorResource\Pages;

use App\Filament\Resources\ProgramMajorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProgramMajors extends ListRecords
{
    protected static string $resource = ProgramMajorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

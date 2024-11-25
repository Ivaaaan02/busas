<?php

namespace App\Filament\Resources\CollegeResource\Pages;

use App\Filament\Resources\CollegeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCollege extends ViewRecord
{
    protected static string $resource = CollegeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurriculumResource\Pages;
use App\Filament\Resources\CurriculumResource\RelationManagers;
use App\Models\Curriculum;
use App\Models\AcadYear;
use App\Models\AcadTerm;
use App\Models\Campus;
use App\Models\College;
use App\Models\ProgramMajor;
use App\Models\Program;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class CurriculumResource extends Resource
{
    protected static ?string $model = Curriculum::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    protected static ?string $navigationLabel = 'Curriculum';

    protected static ?string $modelLabel = 'Curriculum';

    protected static ?string $navigationGroup = 'Academic Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Curriculum Information')
                ->description("Please put the curriculum's details here.")
                ->schema([
                    Forms\Components\Select::make('acad_year_id')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->live()
                        ->label('Academic Year')
                        ->options(AcadYear::pluck('year', 'id'))
                        ->searchable(),
                    Forms\Components\Select::make('acad_term_id')
                        ->label('Academic Term')
                        ->options(fn (Get $get): Collection => AcadTerm::query()
                            ->where('acad_year_id', $get('acad_year_id'))
                            ->pluck('acad_term', 'id'))
                        ->required()
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('campus_id')
                        ->label('Campus')
                        ->options(Campus::all()->pluck('campus_name', 'id'))
                        ->reactive()
                        ->afterStateUpdated(function (Set $set) {
                            $set('college_id', null);
                            $set('program_id', null);
                            $set('program_major_id', null);
                        })
                        ->required(),
                    Forms\Components\Select::make('college_id')
                        ->label('College')
                        ->visible(fn (Get $get) => Campus::query()->where([
                            'id' => $get('campus_id'),
                            'isSatelliteCampus' => 0
                        ])->exists())
                        ->options(fn (Get $get): Collection => College::where('campus_id', $get('campus_id'))->pluck('college_name', 'id'))
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('program_id')
                        ->label('Program')
                        ->options(fn (Get $get): Collection => Program::query()
                            ->where('college_id', $get('college_id'))
                            ->orWhere('campus_id', $get('campus_id'))
                            ->pluck('program_name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('program_major_id')
                        ->label('Program Major')
                        ->options(fn (Get $get): Collection => ProgramMajor::query()
                            ->where('program_id', $get('program_id'))
                            ->pluck('program_major_name', 'id'))
                        ->searchable()
                        ->preload(),
                    Forms\Components\TextInput::make('curriculum_name')
                        ->required()
                        ->maxLength(255),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('acadterm.acad_term')
                    ->label('Academic Term')
                    ->sortable(),
                Tables\Columns\TextColumn::make('program.program_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('programmajor.program_major_name')
                    ->label('Program Major')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('curriculum_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_by')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_by')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCurricula::route('/'),
            'create' => Pages\CreateCurriculum::route('/create'),
            'view' => Pages\ViewCurriculum::route('/{record}'),
            'edit' => Pages\EditCurriculum::route('/{record}/edit'),
        ];
    }
}

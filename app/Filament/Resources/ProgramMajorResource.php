<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramMajorResource\Pages;
use App\Models\ProgramMajor;
use App\Models\Campus;
use App\Models\College;
use App\Models\Program;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Collection;

class ProgramMajorResource extends Resource
{
    protected static ?string $model = ProgramMajor::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Program Major';

    protected static ?string $modelLabel = 'Program Major';

    protected static ?string $navigationGroup = 'Program Management';

    protected static ?int $navigationSort = 2;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Program Major Information')
                    ->description("Please put the program major's details here.")
                    ->schema([
                        Select::make('campus_id')
                            ->label('Campus')
                            ->options(Campus::all()->pluck('campus_name', 'id'))
                            ->reactive()
                            ->afterStateUpdated(function (Set $set) {
                                $set('college_id', null);
                                $set('program_id', null);
                            })
                            ->required(),
                        Select::make('college_id')
                            ->label('College')
                            ->options(fn (Get $get): Collection => College::where('campus_id', $get('campus_id'))->pluck('college_name', 'id'))
                            ->searchable()
                            ->preload(),
                        Select::make('program_id')
                            ->label('Program')
                            ->options(fn (Get $get): Collection => Program::query()
                                ->where('college_id', $get('college_id'))
                                ->orWhere('campus_id', $get('campus_id'))
                                ->pluck('program_name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        TextInput::make('program_major_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('program_major_abbreviation')
                            ->required()
                            ->maxLength(20),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('program.program_name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('program_major_name')
                    ->searchable(),
                TextColumn::make('program_major_abbreviation')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_by')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_by')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('program.program_name')
            
            ->filters([
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define any relations here if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProgramMajors::route('/'),
            'create' => Pages\CreateProgramMajor::route('/create'),
            'view' => Pages\ViewProgramMajor::route('/{record}'),
            'edit' => Pages\EditProgramMajor::route('/{record}/edit'),
        ];
    }
}


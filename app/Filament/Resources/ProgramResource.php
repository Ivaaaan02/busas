<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramResource\Pages;
use App\Filament\Resources\ProgramResource\RelationManagers;
use App\Models\Campus;
use App\Models\College;
use App\Models\Program;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Collection;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'Program';

    protected static ?string $modelLabel = 'Program';

    protected static ?string $navigationGroup = 'Program Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Program Information')
                    ->description("Please put the program's details here.")
                    ->schema([
                                Select::make('campus_id')
                                    ->label('Campus')
                                    ->options(Campus::all()->pluck('campus_name', 'id'))
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn (callable $set) => $set('college_id', null)),
                                Select::make('college_id')
                                    ->label('College')
                                    ->options(function (callable $get) {
                                        $campusId = $get('campus_id');
                                        if ($campusId) {
                                            return College::where('campus_id', $campusId)->pluck('college_name', 'id');
                                        }
                                        return [];
                                    })
                                    ->required()
                                    ->reactive(),
                        TextInput::make('program_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('program_abbreviation')
                            ->required()
                            ->maxLength(20),
                    ])->columns(2),
                Section::make('Program Major Information')
                    ->description("Please put the program major's details here.")
                    ->schema([
                        Repeater::make('program_majors')
                            ->relationship('ProgramMajor')
                            ->schema([
                                TextInput::make('program_major_name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('program_major_abbreviation')
                                    ->required()
                                    ->maxLength(20),
                                ])
                    ])
                ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Campus.campus_name')
                    ->sortable(),
                TextColumn::make('College.college_name')
                    ->sortable(),
                TextColumn::make('program_name')
                    ->searchable(),
                TextColumn::make('program_abbreviation')
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
            ])->defaultSort('Campus.campus_name')
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
            'index' => Pages\ListPrograms::route('/'),
            // 'create' => Pages\CreateProgram::route('/create'),
            'view' => Pages\ViewProgram::route('/{record}'),
            'edit' => Pages\EditProgram::route('/{record}/edit'),
        ];
    }
}

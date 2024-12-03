<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramResource\Pages;
use App\Filament\Resources\ProgramResource\RelationManagers;
use App\Models\Program;
use App\Models\College;
use App\Models\Campus;
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


class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Program';

    protected static ?string $modelLabel = 'Program';

    protected static ?string $navigationGroup = 'Program Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Program Information')
                    ->description("Please put the program's details here.")
                    ->schema([
                        Forms\Components\Select::make('campus_id')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->relationship(name: 'Campus', titleAttribute: 'campus_name')
                            ->afterStateUpdated(function(Set $set){
                                $set('college_id', null);
                            }),
                        Forms\Components\Select::make('college_id')
                            ->label('College')
                            ->visible(fn($record, $get) => Campus::query()->where([
                                'id' => $get('campus_id'),
                                'isSatelliteCampus' => 0
                                ])->exists()
                            )              
                            ->options(fn (Get $get): Collection => College::query()
                                ->where('campus_id', $get('campus_id'))
                                ->pluck('college_name', 'id'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('program_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('program_abbreviation')
                            ->required()
                            ->maxLength(20),
                    ])->columns(2)
                ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('Campus.campus_name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('College.college_name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('program_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('program_abbreviation')
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

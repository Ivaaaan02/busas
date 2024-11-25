<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CollegeResource\Pages;
use App\Filament\Resources\CollegeResource\RelationManagers;
use App\Models\College;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Components\Select;
use Filament\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CollegeResource extends Resource
{
    protected static ?string $model = College::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationLabel = 'College';

    protected static ?string $modelLabel = 'College';

    protected static ?string $navigationGroup = 'Campus Management';

    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('College Information')
                ->description("Please put the college's details here.")
                ->schema([
                    Forms\Components\Select::make('campus_id')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->relationship(name: 'Campus', titleAttribute: 'campus_name'),
                    Forms\Components\TextInput::make('college_name')
                        ->required()
                        ->maxLength(255),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('Campus.campus_name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('college_name')
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
            'index' => Pages\ListColleges::route('/'),
            'create' => Pages\CreateCollege::route('/create'),
            'view' => Pages\ViewCollege::route('/{record}'),
            'edit' => Pages\EditCollege::route('/{record}/edit'),
        ];
    }
}

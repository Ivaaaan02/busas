<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcadTermResource\Pages;
use App\Filament\Resources\AcadTermResource\RelationManagers;
use App\Models\AcadTerm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AcadTermResource extends Resource
{
    protected static ?string $model = AcadTerm::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    protected static ?string $navigationLabel = 'Academic Term';

    protected static ?string $modelLabel = 'Academic Term';

    protected static ?string $navigationGroup = 'Academic Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Academic Term Information')
                    ->description("Please put the academic term's details here.")
                    ->schema([
                        Forms\Components\Select::make('acad_year_id')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship(name: 'AcadYear', titleAttribute: 'year'),
                        Forms\Components\Select::make('acad_term')
                            ->required()
                            ->options([
                                '1st Semester' => '1st Semester',
                                '2nd Semester' => '2nd Semester',
                                'Summer' => 'Summer',
                                'Mid Year' => 'Mid Year',
                            ])
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('AcadYear.year')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('acad_term')
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
            'index' => Pages\ListAcadTerms::route('/'),
            'create' => Pages\CreateAcadTerm::route('/create'),
            'view' => Pages\ViewAcadTerm::route('/{record}'),
            'edit' => Pages\EditAcadTerm::route('/{record}/edit'),
        ];
    }
}
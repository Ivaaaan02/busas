<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CollegeResource\Pages;
use App\Filament\Resources\CollegeResource\RelationManagers;
use App\Models\College;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CollegeResource extends Resource
{
    protected static ?string $model = College::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'College';

    protected static ?string $modelLabel = 'College';

    protected static ?string $navigationGroup = 'Campus Management';

    protected static ?int $navigationSort = 2;

    protected static bool $shouldRegisterNavigation = false;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('College Information')
                ->description("Please put the college's details here.")
                ->schema([
                    Select::make('campus_id')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->relationship(name: 'Campus', titleAttribute: 'campus_name'),
                    TextInput::make('college_name')
                        ->required()
                        ->maxLength(255),
                    Select::make('college_address')
                        ->required()
                        ->options([
                            'Legazpi City' => 'Legazpi City',
                            'Daraga, Albay' => 'Daraga, Albay',
                            'Guinobatan, Albay' => 'Guinobatan, Albay',
                            'Polangui, Albay' => 'Polangui, Albay',
                            'Tabaco City' => 'Tabaco City',
                            'Gubat, Sorsogon' => 'Gubat, Sorsogon',
                        ])
                        ->columnSpanFull(),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Campus.campus_name')
                    ->sortable(),
                TextColumn::make('college_name')
                    ->searchable(),
                TextColumn::make('college_address')
                    ->sortable()
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
            'index' => Pages\ListColleges::route('/'),
            // 'create' => Pages\CreateCollege::route('/create'),
            'view' => Pages\ViewCollege::route('/{record}'),
            'edit' => Pages\EditCollege::route('/{record}/edit'),
        ];
    }
}

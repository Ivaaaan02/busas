<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampusResource\Pages;
use App\Filament\Resources\CampusResource\RelationManagers;
use App\Models\Campus;
use App\Models\College;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CampusResource extends Resource
{
    protected static ?string $model = Campus::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationLabel = 'Campus';

    protected static ?string $modelLabel = 'Campus';

    protected static ?string $navigationGroup = 'Campus Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Campus Information')
                ->description("Please put the campus's details here.")
                ->schema([
                    Forms\Components\TextInput::make('campus_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Toggle::make('isSatelliteCampus')
                        ->required()
                        ->live(),
                ])->columns(2),
                // Forms\Components\Section::make('College Information')
                // ->description("Please put the college's details here.")
                // ->schema([
                //     Forms\Components\Repeater::make('college_id')
                //         ->relationship('colleges')
                //         ->schema([
                //             Forms\Components\TextInput::make('college_name')
                //                 ->required()
                //                 ->reactive()
                //                 ->afterStateUpdated(function (callable $set, $state, $context){
                //                     if($context->isSatelliteCampus){
                //                         $set('college_name', $state);
                //                     }
                //                 }),
                //             Forms\Components\TextInput::make('college_address')
                //                 ->required(),
                //         ])
                // ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('campus_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('isSatelliteCampus')
                    ->label('Satellite Campus')
                    ->boolean()
                    ->sortable(),
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
            'index' => Pages\ListCampuses::route('/'),
            // 'create' => Pages\CreateCampus::route('/create'),
            'view' => Pages\ViewCampus::route('/{record}'),
            'edit' => Pages\EditCampus::route('/{record}/edit'),
        ];
    }
}

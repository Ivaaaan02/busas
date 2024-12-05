<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampusResource\Pages;
use App\Filament\Resources\CampusResource\RelationManagers;
use App\Models\Campus;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
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
                Section::make('Campus Information')
                ->description("Please put the campus's details here.")
                ->schema([
                    TextInput::make('campus_name')
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function (callable $set, callable $get){
                            if($get('isSatelliteCampus')){
                                $set('colleges', collect($get('colleges'))->map(function ($college) use ($get){
                                    return array_merge($college, ['college_name' =>$get('campus_name')]);
                                })->toArray());
                            }
                        }),
                    Toggle::make('isSatelliteCampus')
                        ->required()
                        ->reactive()
                        ->live()
                        ->afterStateUpdated(function (callable $set, callable $get, $state) {
                            if ($state) {
                                $colleges = collect($get('colleges'))->map(function ($college) use ($get) {
                                    $college['college_name'] = $get('campus_name');
                                    return $college;
                                });
                                $set('colleges', $colleges->toArray());
                            } else {
                                $colleges = collect($get('colleges'))->map(function ($college) {
                                    unset($college['college_name']);
                                    return $college;
                                });
                                $set('colleges', $colleges->toArray());
                            }
                        }),
                ])->columns(2),
                Section::make('College Information')
                ->description("Please put the college's details here.")
                ->schema([
                    Repeater::make('colleges')
                        ->relationship('College')
                        ->schema([
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
                            ])
                        ->columnSpanFull(),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('campus_name')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('isSatelliteCampus')
                    ->label('Satellite Campus')
                    ->boolean()
                    ->sortable(),
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

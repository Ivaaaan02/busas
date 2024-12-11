<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampusResource\Pages;
use App\Filament\Resources\CampusResource\RelationManagers;
use App\Models\Campus;
use App\Models\User;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;

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
                        ->label('Campus Name')
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
                        ->label('Satellite Campus')
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
                                ->label('College Name')
                                ->required()
                                ->maxLength(255),
                            Select::make('college_address')
                                ->label('College Address')
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
                    ->label('Campus Name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),
                TextColumn::make('isSatelliteCampus')
                    ->label('Satellite Campus')
                    ->badge()
                    ->formatStateUsing(function ($state, $record){
                        if($record->campus_name === 'Main Campus'){
                            return 'Main Campus';
                        }
                        elseif ($record->campus_name === 'Daraga Campus'){
                            return 'Daraga Campus';
                        }
                        elseif ($record->campus_name === 'East Campus'){
                            return 'East Campus';
                        }
                            
                        return $state ? 'Satellite Campus' : 'Satellite Campus';
                    })
                    ->color(function ($record, $state) {
                        if ($record->campus_name === 'Main Campus')
                            return Color::Green;
                        elseif ($record->campus_name === 'Daraga Campus')
                            return Color::Amber;
                        elseif ($record->campus_name === 'East Campus')
                            return Color::Blue;
                        else
                            return $state ? Color::Red : Color::Amber;
                    })
                    ->sortable(),
                TextColumn::make('College.college_name')
                    ->label('College Name')
                    ->searchable()
                    ->sortable()
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->limitList(3)
                    ->expandableLimitedList(),
                TextColumn::make('College.college_address')
                    ->label('College Address')
                    ->searchable()
                    ->sortable()
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->limitList(3)
                    ->expandableLimitedList(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip()
                    ->icon('heroicon-m-clock')
                    ->iconColor('primary')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user.name')
                    ->label('Created By')
                    ->badge()
                    ->color(Color::Amber)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_by')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                SelectFilter::make('isSatelliteCampus')
                    ->label('Satellite Campus')
                    ->options([
                        '0' => 'No',
                        '1' => 'Yes',
                    ]),
                SelectFilter::make('College')
                    ->relationship('College', 'college_name')
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
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

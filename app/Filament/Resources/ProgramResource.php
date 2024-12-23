<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramResource\Pages;
use App\Filament\Resources\ProgramResource\RelationManagers;
use App\Models\Campus;
use App\Models\College;
use App\Models\Program;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;


class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Program';

    protected static ?string $modelLabel = 'Program';

    protected static ?string $navigationGroup = 'Program Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Program Information')
                            ->icon('heroicon-o-book-open')
                            ->schema([
                                Fieldset::make('Campus Details')
                                    ->schema([
                                        Select::make('campus_id')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->label('Campus Name')
                                            ->options(Campus::pluck('campus_name', 'id'))
                                                ->afterStateUpdated(function (Set $set, Get $get) {
                                                    $set('college_id', null);
                                                    $set('program_id', null);
                                                    $set('program_major_id', null);
                                                }),
                                        Select::make('college_id')
                                            ->label('College')
                                            ->options(fn (Get $get): Collection => College::where('campus_id', $get('campus_id'))->pluck('college_name', 'id'))
                                            ->searchable()
                                            ->preload(),
                                    ]),
                                    Fieldset::make('Program Details')
                                        ->schema([
                                            TextInput::make('program_name')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('program_abbreviation')
                                            ->required()
                                            ->maxLength(20),
                                        ]),
                            ]),
                            Tab::make('Program Major Information')
                                ->icon('heroicon-o-book-open')
                                ->schema([
                                    Repeater::make('program_majors')
                                        ->relationship('ProgramMajor')
                                        ->label('')
                                        ->schema([
                                            TextInput::make('program_major_name')
                                                ->required()
                                                ->maxLength(255),
                                            TextInput::make('program_major_abbreviation')
                                                ->required()
                                                ->maxLength(20),
                                            ])
                                ]),
                        
                    ])->columnSpanFull(),
                ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('College.college_name')
            // ->groups([
            //     'Campus.campus_name',
            //     'College.college_name',
            // ])
            ->columns([
                TextColumn::make('Campus.campus_name')
                    ->label('Campus Name')
                    ->badge()
                    ->sortable()
                    ->color(function ($record, $state) {
                        if ($record->Campus->campus_name === 'Main Campus')
                            return Color::Emerald;
                        elseif ($record->Campus->campus_name === 'Daraga Campus')
                            return Color::Amber;
                        elseif ($record->CAMPUS->campus_name === 'East Campus')
                            return Color::Cyan;
                        else
                            return Color::Rose;
                    }),
                TextColumn::make('program_name')
                    ->label('Program Name')
                    ->numeric()
                    ->sortable()
                    ->wrap()
                    ->lineClamp(2)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                 
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    }),
                TextColumn::make('program_abbreviation')
                    ->label('Program Abbreviation')
                    ->sortable()
                    ->limit(40)
                    ->default('N/A')
                    ->badge()
                    ->color(Color::Orange),
                TextColumn::make('ProgramMajor.program_major_name')
                    ->label('Program Major Name')
                    ->searchable()
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->default('N/A'),
                TextColumn::make('ProgramMajor.program_major_abbreviation')
                    ->label('Program Major Abbreviation')    
                    ->searchable()
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->default('N/A'),
                TextColumn::make('created_at')
                    ->label('Created At') 
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->dateTimeTooltip()
                    ->icon('heroicon-m-clock')
                    ->iconColor('primary')
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
                    ->icon('heroicon-m-user')
                    ->badge()
                    ->color(Color::Orange) 
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_by')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), 
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
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
            'index' => Pages\ListPrograms::route('/'),
            // 'create' => Pages\CreateProgram::route('/create'),
            'view' => Pages\ViewProgram::route('/{record}'),
            'edit' => Pages\EditProgram::route('/{record}/edit'),
        ];
    }
}

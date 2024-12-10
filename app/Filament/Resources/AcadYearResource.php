<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcadYearResource\Pages;
use App\Filament\Resources\AcadYearResource\RelationManagers;
use App\Models\AcadYear;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Support\Colors\Color;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Filters\RangeFilter;
use Filament\Support\Enums\FontWeight;

class AcadYearResource extends Resource
{
    protected static ?string $model = AcadYear::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Academic Year';

    protected static ?string $modelLabel = 'Academic Year';

    protected static ?string $navigationGroup = 'Academic Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Academic Year Information')
                    ->description("Please put the academic year's details here.")
                    ->schema([
                        TextInput::make('year')
                            ->label('Academic Year')
                            ->required()
                            ->maxLength(11),
                        DatePicker::make('start_date')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        DatePicker::make('end_date')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                    ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('year')
                    ->label('Academic Year')
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->color(Color::Gray),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable()
                    ->icon('heroicon-m-calendar')
                    ->iconColor(Color::Green),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable()
                    ->icon('heroicon-m-calendar')
                    ->iconColor(Color::Red),
                TextColumn::make('AcadTerm.acad_term')
                    ->label('Academic Term')
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
                    ->color('primary')
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
            'index' => Pages\ListAcadYears::route('/'),
            // 'create' => Pages\CreateAcadYear::route('/create'),
            'view' => Pages\ViewAcadYear::route('/{record}'),
            'edit' => Pages\EditAcadYear::route('/{record}/edit'),
        ];
    }
}

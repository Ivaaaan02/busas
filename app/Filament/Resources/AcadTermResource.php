<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcadTermResource\Pages;
use App\Filament\Resources\AcadTermResource\RelationManagers;
use App\Models\AcadYear;
use App\Models\AcadTerm;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
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
                Section::make('Academic Term Information')
                    ->description("Please put the academic term's details here.")
                    ->schema([
                        Select::make('acad_year_id')
                            ->label('Academic Year')
                            ->relationship('AcadYear', 'year') 
                            ->reactive()
                            ->required()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                $academicYear = AcadYear::find($get('acad_year_id'));
                                if ($academicYear) {
                                    $year = substr($academicYear->year, -4); 
                                    $options = [
                                        '1st Semester' => '1st Semester',
                                        '2nd Semester' => '2nd Semester',
                                        "Summer $year" => "Summer $year",
                                        "Mid Year $year" => "Mid Year $year",
                                    ];
                                    $set('acad_term_options', $options); 
                                }
                            }),

                        Select::make('acad_term')
                            ->label('Academic Term')
                            ->options(fn (callable $get) => $get('acad_term_options') ?? [
                                '1st Semester' => '1st Semester',
                                '2nd Semester' => '2nd Semester',
                                'Summer' => 'Summer',
                                'Mid Year' => 'Mid Year', 
                            ])
                            ->required(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('AcadYear.year')
                    ->label('Academic Year')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('acad_term')
                    ->label('Academic Term')
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
            // 'create' => Pages\CreateAcadTerm::route('/create'),
            'view' => Pages\ViewAcadTerm::route('/{record}'),
            'edit' => Pages\EditAcadTerm::route('/{record}/edit'),
        ];
    }
}

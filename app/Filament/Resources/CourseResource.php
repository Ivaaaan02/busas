<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Models\Course;
use App\Models\Campus;
use App\Models\College;
use App\Models\ProgramMajor;
use App\Models\Program;
use App\Models\Curriculum;
use App\Models\AcadYear;
use App\Models\AcadTerm;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Course';

    protected static ?string $modelLabel = 'Course';

    protected static ?string $navigationGroup = 'Academic Management';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Curriculum Information')
                ->description("Please select the curriculum.")
                ->schema([
                    Select::make('curriculum_id')
                    ->label('Curriculum')
                    ->relationship('curriculum',  'curriculum_name')
                    ->required()
                    ->searchable()
                    ->preload(),
                ]),
                Section::make('Course Information')
                ->description("Please put the course's details here.")
                ->schema([
                    Repeater::make('course')
                        ->schema([
                            TextInput::make('descriptive_title')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('course_code')
                                ->required()
                                ->maxLength(20),
                            TextInput::make('course_unit')
                                ->required()
                                ->maxLength(5),
                        ])->columns(2)->columnSpanFull()
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('Curriculum.curriculum_name')
            ->columns([
                // TextColumn::make('curriculum.curriculum_name')
                //     ->label('Curriculum')
                //     ->weight(FontWeight::Medium)
                //     ->limit(40)
                //     ->tooltip(function (TextColumn $column): ?string {
                //         $state = $column->getState();
                 
                //         if (strlen($state) <= $column->getCharacterLimit()) {
                //             return null;
                //         }
                //         return $state;
                //     })
                //     ->sortable(),
                TextColumn::make('descriptive_title')
                    ->label('Descriptive Title')
                    ->searchable(),
                TextColumn::make('course_code')
                    ->searchable()
                    ->badge()
                    ->icon('heroicon-m-book-open')
                    ->color(Color::Cyan),
                TextColumn::make('course_unit')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Created At') 
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->dateTimeTooltip()
                    ->icon('heroicon-m-clock')
                    ->iconColor(Color::Emerald)
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
            'index' => Pages\ListCourses::route('/'),
            //'create' => Pages\CreateCourse::route('/create'),
            'view' => Pages\ViewCourse::route('/{record}'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}

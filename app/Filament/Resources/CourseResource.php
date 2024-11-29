<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Models\Course;
use App\Models\Campus;
use App\Models\College;
use App\Models\ProgramMajor;
use App\Models\Program;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    protected static ?string $navigationLabel = 'Course';

    protected static ?string $modelLabel = 'Course';

    protected static ?string $navigationGroup = 'Academic Management';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Curriculum Information')
                ->description("Please put the curriculum's details here.")
                ->schema([
                Forms\Components\Select::make('campus_id')
                    ->label('Campus')
                    ->options(Campus::all()->pluck('campus_name', 'id'))
                    ->reactive()
                    ->afterStateUpdated(function (Set $set) {
                        $set('college_id', null);
                        $set('program_id', null);
                        $set('program_major_id', null);
                    })
                    ->required(),
                Forms\Components\Select::make('college_id')
                    ->label('College')
                    ->visible(fn (Get $get) => Campus::query()->where([
                        'id' => $get('campus_id'),
                        'isSatelliteCampus' => 0
                    ])->exists())
                    ->options(fn (Get $get): Collection => College::where('campus_id', $get('campus_id'))->pluck('college_name', 'id'))
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('program_id')
                    ->label('Program')
                    ->options(fn (Get $get): Collection => Program::query()
                        ->where('college_id', $get('college_id'))
                        ->orWhere('campus_id', $get('campus_id'))
                        ->pluck('program_name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('program_major_id')
                    ->label('Program Major')
                    ->options(fn (Get $get): Collection => ProgramMajor::query()
                        ->where('program_id', $get('program_id'))
                        ->pluck('program_major_name', 'id'))
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('descriptive_title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('course_code')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('course_unit')
                    ->required()
                    ->maxLength(5),
                ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('program.program_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('programmajor.program_major_name')
                    ->label('Program Major')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('descriptive_title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('course_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('course_unit')
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
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'view' => Pages\ViewCourse::route('/{record}'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}

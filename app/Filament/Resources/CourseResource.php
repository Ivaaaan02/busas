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
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                Section::make('Course Information')
                ->description("Please put the course's details here.")
                ->schema([


                    // Select::make('acad_year_id')
                    // ->label('Academic Year')
                    // ->options(AcadYear::all()->pluck('year', 'id'))
                    // ->reactive()
                    // ->afterStateUpdated(function (Set $set) {
                    //     $set('acad_term_id', null);
                    // })
                    // ->required(),

                    // Select::make('acad_term_id')
                    // ->label('Academic Term')
                    // ->options(fn (Get $get): Collection => AcadTerm::query()
                    //     ->where('acad_year_id', $get('acad_year_id'))
                    //     ->pluck('acad_term', 'id'))
                    // ->required()
                    // ->searchable()
                    // ->preload(),

                    // Select::make('campus_id')
                    // ->label('Campus')
                    // ->options(Campus::all()->pluck('campus_name', 'id'))
                    // ->reactive()
                    // ->afterStateUpdated(function (Set $set) {
                    //     $set('college_id', null);
                    // })
                    // ->required(),

                    // Select::make('college_id')
                    // ->label('College')
                    // ->options(fn (Get $get): Collection => College::query()
                    //     ->where('campus_id', $get('campus_id'))
                    //     ->pluck('college_name', 'id'))
                    // ->required()
                    // ->searchable()
                    // ->preload(),

                    // Select::make('program_id')
                    // ->label('Program')
                    // ->options(fn (Get $get): Collection => Program::query()
                    //     ->where('campus_id', $get('campus_id'))
                    //     ->orWhere('college_id', $get('college_id'))
                    //     ->pluck('program_name', 'id'))
                    // ->required()
                    // ->searchable()
                    // ->preload(),

                    // Select::make('program_major_id')
                    // ->label('ProgramMajor')
                    // ->options(fn (Get $get): Collection => ProgramMajor::query()
                    //     ->Where('program_id', $get('program_id'))
                    //     ->pluck('program_major_name', 'id'))
                    // ->searchable()
                    // ->preload(),

                    Select::make('curriculum_id')
                        ->label('Curriculum')
                        ->options(fn (Get $get): Collection => Curriculum::query()
                            ->where('program_id', $get('program_id'))
                            ->orWhere('program_major_id', $get('program_major_id'))
                            ->pluck('curriculum_name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload(),

                    TextInput::make('descriptive_title')
                        ->required()
                        ->maxLength(255),
                   TextInput::make('course_code')
                        ->required()
                        ->maxLength(20),
                    TextInput::make('course_unit')
                        ->required()
                        ->maxLength(5),
                    ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('acadterm.acad_term')
                    ->label('Academic Term')
                    ->numeric()
                    ->sortable(),
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
            //'create' => Pages\CreateCourse::route('/create'),
            'view' => Pages\ViewCourse::route('/{record}'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}

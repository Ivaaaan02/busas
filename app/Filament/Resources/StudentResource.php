<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\AcadTerm;
use App\Models\Campus;
use App\Models\College;
use App\Models\Course;
use App\Models\CourseStudentRecord;
use App\Models\Curriculum;
use App\Models\Program;
use App\Models\ProgramMajor;
use App\Models\StudentRecord;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Collection;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationLabel = 'Student';

    protected static ?string $modelLabel = 'Student';

    protected static ?string $navigationGroup = 'Student Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Student Information')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Fieldset::make('Student Name')
                                    ->schema([
                                        TextInput::make('last_name')
                                            ->required()
                                            ->maxLength(255)
                                            ->default('-'),
                                        TextInput::make('first_name')
                                            ->required()
                                            ->maxLength(255)
                                            ->default('-'),
                                        TextInput::make('middle_name')
                                            ->required()
                                            ->maxLength(255)
                                            ->default('-'),
                                        TextInput::make('suffix')
                                            ->maxLength(10),
                                ])->columns(2),
                                Fieldset::make("Student's Personal Information")
                                    ->schema([
                                        TextInput::make('address')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('place_of_birth')
                                            ->maxLength(255),
                                        DatePicker::make('date_of_birth')
                                            ->native(false),
                                        Radio::make('sex')
                                            ->required()
                                            ->options([
                                                'M' => 'Male',
                                                'F' => 'Female'
                                            ])
                                            ->inline()
                                            ->inlineLabel(false),
                                    ]),
                                Fieldset::make("Student's Campus")
                                    ->schema([
                                        Select::make('campus_id')
                                            ->label('Campus')
                                            ->options(Campus::all()->pluck('campus_name', 'id'))
                                            ->reactive()
                                            ->afterStateUpdated(function (callable $set) {
                                                $set('college_id', null);
                                                $set('program_id', null);
                                                $set('program_major_id', null);
                                            })
                                            ->required(),

                                        Select::make('college_id')
                                            ->label('College')
                                            ->options(fn (callable $get) => 
                                                College::where('campus_id', $get('campus_id'))->pluck('college_name', 'id')
                                            )
                                            ->reactive()
                                            ->afterStateUpdated(function (callable $set) {
                                                $set('program_id', null);
                                                $set('program_major_id', null);
                                            })
                                            ->required(),
                                    ]),
                                Fieldset::make("Student's Program")
                                    ->schema([
                                        Select::make('program_id')
                                            ->label('Program')
                                            ->options(fn (callable $get) => 
                                                Program::where('college_id', $get('college_id'))->pluck('program_name', 'id')
                                            )
                                            ->reactive()
                                            ->afterStateUpdated(function (callable $set) {
                                                $set('program_major_id', null);
                                            })
                                            ->required(),
                    
                                        Select::make('program_major_id')
                                            ->label('Program Major')
                                            ->options(fn (callable $get) => 
                                                ProgramMajor::where('program_id', $get('program_id'))->pluck('program_major_name', 'id')
                                            ),
                                    ]),
                                ]),
                        Tab::make('Student Registration Information')
                            ->icon('heroicon-o-inbox-stack')
                            ->schema([
                                Fieldset::make("Student's Registration Information")
                                    ->relationship('StudentRegistrationInfo')
                                    ->schema([
                                        TextInput::make('last_date_attended')
                                            ->required()
                                            ->numeric()
                                            ->minValue(1969)
                                            ->minLength(4)
                                            ->maxLength(4),
                                        TextInput::make('last_school_attended')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('date_semester_admitted')
                                            ->required()
                                            ->maxLength(100),
                                        Select::make('category')
                                            ->required()
                                            ->label('Category')
                                            ->options([
                                                'Senior High School Graduate' => 'Senior High School Graduate',
                                                'High School Graduate' => 'High School Graduate',
                                                'Transferee' => 'Transferee',
                                            ]),
                                        TextInput::make('nstp_no')
                                            ->required()
                                            ->maxLength(100),
                                    ]),
                                    
                            ]),
                        Tab::make('Student Graduation Information')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                Repeater::make('student_graduation_infos')
                                    ->label('')
                                    ->relationship('StudentGraduationInfo')
                                    ->schema([
                                        DatePicker::make('graduation_date')
                                            ->native(false),
                                        TextInput::make('board_approval')
                                            ->required()
                                            ->maxLength(255),
                                        Select::make('degree_attained')
                                            ->required()
                                            ->options([
                                                "Bachelor's degree" => "Bachelor's degree",
                                                "Master's degree" => "Master's degree",
                                                'Doctorate degree' => 'Doctorate degree',
                                            ]),
                                        TextInput::make('date_attended')
                                            ->required()
                                            ->maxLength(255),
                                        Select::make('latin_honor')
                                            ->label('Latin Honor')
                                            ->options([
                                                'Suma Cum Laude' => 'Suma Cum Laude',
                                                'Magna Cum Laude' => 'Magna Cum Laude',
                                                'Cum Laude' => 'Cum Laude',
                                                'Academic Distinction' => 'Academic Distinction',
                                                'Outstanding Graduate' => 'Outstanding Graduate',
                                                'Most Outstanding Graduate' => 'Most Outstanding Graduate',
                                            ]),
                                        TextInput::make('gwa')
                                            ->required()
                                            ->numeric()
                                            ->rules('numeric|regex:/^\d{1,1}(\.\d{1,4})?$/'),
                                    ])
                                    ->columns(2)
                                    ->addActionLabel('Add Graduation Information'),
                            ]),
                            Tab::make('Student Records Information')
                            ->icon('heroicon-o-book-open')
                            ->schema([
                                Repeater::make('student_records')
                                    ->label('')
                                    ->relationship('StudentRecord')
                                    ->schema([
                                        Select::make('curriculum_id')
                                            ->label('Curriculum')
                                            ->relationship('curriculum',  'curriculum_name')
                                            ->required()
                                            ->searchable()
                                            ->preload(),
                                        Select::make('curriculum_id')
                                            ->label('Curriculum'),
                                        // Select::make('course_id')
                                        //     ->label('Course')
                                        //     ->options(fn (Get $get): Collection => Course::where('curriculum_id', $get('curriculum_id'))->pluck('descriptive_title', 'id'))
                                        //     ->searchable()
                                        //     ->preload(),
                                        TextInput::make('final_grade')
                                            ->required()
                                            ->maxLength(3),
                                        TextInput::make('removal_rating')
                                            ->required()
                                            ->numeric(),

                                ])
                                ->columns(2)
                                ->addActionLabel("Add Student's Record"),
]),
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                    TextColumn::make('full_name')
                        ->label('Student Name')
                        ->searchable()
                        ->weight(FontWeight::Medium),
                    TextColumn::make('date_of_birth')
                        ->date()
                        ->sortable()
                        ->icon('heroicon-m-calendar')
                        ->iconColor(Color::Emerald),
                    TextColumn::make('Campus.campus_name')
                        ->sortable()
                        ->searchable()
                        ->badge(),
                    TextColumn::make('Campus.campus_address')
                    ->label('Campus Address')
                        ->sortable()
                        ->searchable()
                        ->icon('heroicon-m-map-pin')
                        ->iconColor(Color::Red),
                    TextColumn::make('StudentGraduationInfo.date_attended')
                        ->label('Date Attended')
                        ->sortable()
                        ->searchable(),
                    TextColumn::make('StudentGraduationInfo.degree_attained')
                        ->label('Degree Attained')
                        ->sortable()
                        ->searchable()
                        ->badge()
                        ->color(Color::Red),
                    TextColumn::make('program.program_name')
                        ->sortable()
                        ->searchable(),
                    TextColumn::make('program_major.program_major_name')
                        ->sortable()
                        ->searchable()
                        ->badge()
                        ->color(Color::Orange)
                        ->default('N/A'),
                    TextColumn::make('StudentGraduationInfo.graduation_date')
                        ->label('Graduation Date')
                        ->date()
                        ->icon('heroicon-m-calendar')
                        ->iconColor(Color::Emerald)
                        ->sortable()
                        ->searchable(),
                    TextColumn::make('StudentGraduationInfo.board_approval')
                        ->label('Board Approval')
                        ->sortable()
                        ->searchable(),
                    TextColumn::make('StudentGraduationInfo.latin_honor')
                        ->label('Honor')
                        ->icon('heroicon-m-academic-cap')
                        ->badge()
                        ->color(Color::Sky)
                        ->sortable()
                        ->searchable(),
                    TextColumn::make('place_of_birth')
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),
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
                    TextColumn::make('user.name')
                        ->numeric()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('user.name')
                        ->numeric()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])

            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(), 
                ])
                ->link()
                ->label('Actions'),
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'view' => Pages\ViewStudent::route('/{record}'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}

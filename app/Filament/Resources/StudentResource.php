<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Campus;
use App\Models\College;
use App\Models\Program;
use App\Models\ProgramMajor;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
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
                Section::make('Student Information')
                    ->description("Please put the student's details here.")
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
                        TextInput::make('nstp_no')
                            ->required()
                            ->maxLength(100),
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
                    ])->columns(3),
                Section::make('Student Registration Information')
                        ->description("Please put the student's details here.")
                        ->schema([
                            Repeater::make('stundent_registration_infos')
                            ->relationship('StudentRegistrationInfo')
                            ->schema([
                                TextInput::make('last_date_attended')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1969)
                                    ->minLength(4)
                                    ->maxLength(4),
                                Select::make('category')
                                    ->required()
                                    ->label('Category')
                                    ->options([
                                        'Senior High School Graduate' => 'Senior High School Graduate',
                                        'High School Graduate' => 'High School Graduate',
                                        'Transferee' => 'Transferee',
                                    ]),
                                TextInput::make('last_school_attended')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('date_semester_admitted')
                                    ->required()
                                    ->maxLength(100),
                                ])->columns(2),
                                ]),

                        Section::make('Student Graduation Information')
                        ->description("Please put the student's details here.")
                        ->schema([
                            Repeater::make('student_graduation_infos')
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
                            
                                ])->columns(2),
                        ])
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
                        // ->color(function ($record, $state) {
                        //     if ($record->Campus->campus_name === 'Main Campus')
                        //         return Color::Emerald;
                        //     elseif ($record->Campus->campus_name === 'Daraga Campus')
                        //         return Color::Amber;
                        //     elseif ($record->CAMPUS->campus_name === 'East Campus')
                        //         return Color::Cyan;
                        //     else
                        //         return Color::Rose;
                        // }),
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
                        ->color(function ($record, $state) {
                            if ($record->degree_attained === "Bachelor's degree")
                                return Color::Yellow;
                            elseif ($record->degree_attained === "Master's degree")
                                return Color::Blue;
                            elseif ($record->degree_attained === "Doctorate degree")
                                return Color::Blue;
                            else
                                return Color::Rose;
                        }),
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'view' => Pages\ViewStudent::route('/{record}'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

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
                        TextInput::make('sex')
                            ->required()
                            ->maxLength(1),
                        TextInput::make('address')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('place_of_birth')
                            ->maxLength(255),
                        DatePicker::make('date_of_birth')
                            ->native(false),
                    ])->columns(2),
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
                                        'Shiftee' => 'Shiftee',
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
                            Repeater::make('stundent_graduation_infos')
                            ->relationship('StudentGraduationInfo')
                            ->schema([
                                DatePicker::make('graduation_date')
                                    ->native(false),
                                TextInput::make('board_approval')
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
                                TextInput::make('nstp_no')
                                    ->required()
                                    ->maxLength(100),
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
                TextColumn::make('last_name')
                    ->searchable(),
                TextColumn::make('first_name')
                    ->searchable(),
                TextColumn::make('middle_name')
                    ->searchable(),
                TextColumn::make('suffix')
                    ->searchable(),
                TextColumn::make('sex')
                    ->searchable(),
                TextColumn::make('address')
                    ->searchable(),
                TextColumn::make('place_of_birth')
                    ->searchable(),
                TextColumn::make('date_of_birth')
                    ->date()
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'view' => Pages\ViewStudent::route('/{record}'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}

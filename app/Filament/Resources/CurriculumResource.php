<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurriculumResource\Pages;
use App\Filament\Resources\CurriculumResource\RelationManagers;
use App\Models\Curriculum;
use App\Models\AcadYear;
use App\Models\AcadTerm;
use App\Models\Campus;
use App\Models\College;
use App\Models\ProgramMajor;
use App\Models\Program;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class CurriculumResource extends Resource
{
    protected static ?string $model = Curriculum::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Curriculum';

    protected static ?string $modelLabel = 'Curriculum';

    protected static ?string $navigationGroup = 'Academic Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Curriculum Information')
                            ->schema([
                                Fieldset::make('Academic Term Details')
                                ->schema([
                                    Select::make('acad_year_id')
                                        ->required()
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->label('Academic Year')
                                        ->options(AcadYear::pluck('year', 'id'))
                                        ->afterStateUpdated(fn (Set $set, Get $get) => self::updateCurriculumName($set, $get)),
                                    Select::make('acad_term_id')
                                        ->label('Academic Term')
                                        ->options(fn (Get $get): Collection => AcadTerm::query()
                                            ->where('acad_year_id', $get('acad_year_id'))
                                            ->pluck('acad_term', 'id'))
                                        ->required()
                                        ->searchable()
                                        ->preload()
                                        ->afterStateUpdated(fn (Set $set, Get $get) => self::updateCurriculumName($set, $get)),
                                ]),
                                Fieldset::make('Program Details')
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
                                                self::updateCurriculumName($set, $get);
                                            }),
                                        Select::make('college_id')
                                            ->label('College')
                                            ->options(fn (Get $get): Collection => College::where('campus_id', $get('campus_id'))->pluck('college_name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->afterStateUpdated(fn (Set $set, Get $get) => self::updateCurriculumName($set, $get)),
                                        Select::make('program_id')
                                            ->label('Program')
                                            ->options(fn (Get $get): Collection => Program::query()
                                                ->where('college_id', $get('college_id'))
                                                ->orWhere('campus_id', $get('campus_id'))
                                                ->pluck('program_name', 'id'))
                                            ->required()
                                            ->searchable() 
                                            ->preload()
                                            ->afterStateUpdated(fn (Set $set, Get $get) => self::updateCurriculumName($set, $get)),
                                        Select::make('program_major_id')
                                            ->label('Program Major')
                                            ->options(fn (Get $get): Collection => ProgramMajor::query()
                                                ->where('program_id', $get('program_id'))
                                                ->pluck('program_major_name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->afterStateUpdated(fn (Set $set, Get $get) => self::updateCurriculumName($set, $get)),
                                ]),
                                Fieldset::make('Curriculum Details')
                                ->schema([
                                    TextInput::make('curriculum_name')
                                        ->required()
                                        ->maxLength(255)
                                        ->disabled()
                                        ->unique(),
                                    Hidden::make('curriculum_name')
                                ]),            
                            ]),
                            Tab::make('Course Information')
                            ->schema([
                                 Repeater::make('courses')
                            ->relationship('Course')
                            ->label('')
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
                                ])
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    protected static function updateCurriculumName(Set $set, Get $get)
    {
        $acadTerm = AcadTerm::find($get('acad_term_id'));
        $campus = Campus::find($get('campus_id'));
        $college = College::find($get('college_id'));
        $program = Program::find($get('program_id'));
        $programMajor = ProgramMajor::find($get('program_major_id'));

        $curriculumName = ($acadTerm ? $acadTerm->acad_term : '') . ' ' . 
                          ($campus ? $campus->campus_name : '');

        if ($campus && !$campus->isSatelliteCampus) {
            $curriculumName .= ' ' . ($college ? $college->college_name : '');
        }

        $curriculumName .= ' ' . 
                           ($program ? $program->program_name : '') . ' ' . 
                           ($programMajor ? $programMajor->program_major_name : '');

        $set('curriculum_name', $curriculumName);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('curriculum_name')
            ->columns([
                TextColumn::make('acadterm.acad_term')
                    ->label('Academic Term')
                    ->sortable()
                    ->weight(FontWeight::Medium),
                TextColumn::make('program.program_name')
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
                TextColumn::make('programmajor.program_major_name')
                    ->label('Program Major')
                    ->sortable()
                    ->limit(40)
                    ->default('N/A')
                    ->badge()
                    ->color(Color::Orange),
                // TextColumn::make('curriculum_name')
                //     ->searchable()
                //     ->label('Curriculum Name')
                //     ->limit(40)
                //     ->tooltip(function (TextColumn $column): ?string {
                //         $state = $column->getState();
                 
                //         if (strlen($state) <= $column->getCharacterLimit()) {
                //             return null;
                //         }
                //         return $state;
                //     }),
                TextColumn::make('Course.descriptive_title')
                    ->label('Descriptive Title')
                    ->searchable()
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->limitList(3)
                    ->expandableLimitedList(),
                TextColumn::make('Course.course_code')
                    ->searchable()
                    // ->badge()
                    // ->icon('heroicon-m-book-open')
                    // ->color(Color::Cyan)
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->limitList(3)
                    ->expandableLimitedList(),
                TextColumn::make('Course.course_unit')
                    ->searchable()
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->limitList(3)
                    ->expandableLimitedList(),
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
                    ->badge()
                    ->icon('heroicon-m-user')
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
            'index' => Pages\ListCurricula::route('/'),
            'create' => Pages\CreateCurriculum::route('/create'),
            'view' => Pages\ViewCurriculum::route('/{record}'),
            'edit' => Pages\EditCurriculum::route('/{record}/edit'),
        ];
    }
}

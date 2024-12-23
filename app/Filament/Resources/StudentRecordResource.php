<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentRecordResource\Pages;
use App\Filament\Resources\StudentRecordResource\RelationManagers;
use App\Models\StudentRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentRecordResource extends Resource
{
    protected static ?string $model = StudentRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('student_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('curriculum_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('acad_term_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('final_grade')
                    ->required()
                    ->maxLength(3),
                Forms\Components\TextInput::make('removal_rating')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('created_by')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('updated_by')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('curriculum_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('acad_term_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('final_grade')
                    ->searchable(),
                Tables\Columns\TextColumn::make('removal_rating')
                    ->numeric()
                    ->sortable(),
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_by')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListStudentRecords::route('/'),
            'create' => Pages\CreateStudentRecord::route('/create'),
            'view' => Pages\ViewStudentRecord::route('/{record}'),
            'edit' => Pages\EditStudentRecord::route('/{record}/edit'),
        ];
    }
}

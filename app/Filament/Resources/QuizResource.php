<?php

namespace App\Filament\Resources;

use App\Models\Quiz;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuizResource extends Resource
{
    protected static ?string $model = Quiz::class;
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?string $navigationGroup = 'Courses';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('course_id')
                    ->relationship('course', 'title')
                    ->required()
                    ->searchable(),

                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description'),

                Forms\Components\Repeater::make('questions')
                    ->relationship('questions')
                    ->schema([
                        Forms\Components\Textarea::make('question')->required(),
                        Forms\Components\TextInput::make('option_a')->label('A')->required(),
                        Forms\Components\TextInput::make('option_b')->label('B')->required(),
                        Forms\Components\TextInput::make('option_c')->label('C'),
                        Forms\Components\TextInput::make('option_d')->label('D'),
                        Forms\Components\Select::make('correct_answer')
                            ->options(['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'])
                            ->required(),
                    ])
                    ->columns(2)
                    ->defaultItems(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('course.title')->label('Course'),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('questions_count')->counts('questions')->label('Questions'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Filament\Resources\QuizResource\Pages\ListQuizzes::route('/'),
            'create' => \App\Filament\Resources\QuizResource\Pages\CreateQuiz::route('/create'),
            'edit'   => \App\Filament\Resources\QuizResource\Pages\EditQuiz::route('/{record}/edit'),
        ];
    }
}

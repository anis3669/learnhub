<?php

namespace App\Filament\Resources;

use App\Models\Lesson;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LessonResource extends Resource
{
    protected static ?string $model = Lesson::class;
    protected static ?string $navigationIcon = 'heroicon-o-play-circle';
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

                Forms\Components\FileUpload::make('video')
                    ->label('Video Lecture')
                    ->acceptedFileTypes(['video/mp4', 'video/mov', 'video/avi'])
                    ->directory('videos')
                    ->maxSize(512000) // 500 MB
                    ->required(),

                Forms\Components\TextInput::make('duration')
                    ->numeric()
                    ->suffix(' minutes')
                    ->default(10),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('course.title')->label('Course'),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('duration')->suffix(' min'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Filament\Resources\LessonResource\Pages\ListLessons::route('/'),
            'create' => \App\Filament\Resources\LessonResource\Pages\CreateLesson::route('/create'),
            'edit'   => \App\Filament\Resources\LessonResource\Pages\EditLesson::route('/{record}/edit'),
        ];
    }
}
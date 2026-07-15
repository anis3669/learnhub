<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages\CreateCourse;
use App\Filament\Resources\CourseResource\Pages\EditCourse;
use App\Filament\Resources\CourseResource\Pages\ListCourses;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Courses';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->rows(3),

                Forms\Components\FileUpload::make('thumbnail')
                    ->image()
                    ->directory('thumbnails')
                    ->columnSpanFull(),

                Forms\Components\Select::make('teacher_id')
                    ->label('Teacher')
                    ->relationship('teacher', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('category')
                    ->label('Category')
                    ->options([
                        'Programming' => 'Programming',
                        'Web Development' => 'Web Development',
                        'Computer Science' => 'Computer Science',
                        'AI & ML' => 'AI & ML',
                        'Data Science' => 'Data Science',
                        'Mobile Development' => 'Mobile Development',
                        'General' => 'General',
                        'Premium' => 'Premium',
                    ])
                    ->required(),

                Forms\Components\Select::make('level')
                    ->label('Level')
                    ->options([
                        'Introduction' => 'Introduction',
                        'Beginner' => 'Beginner',
                        'Intermediate' => 'Intermediate',
                        'Advanced' => 'Advanced',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('price')
                    ->label('Price (Rs.)')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->default(0)
                    ->required(),

                Forms\Components\TextInput::make('duration_hours')
                    ->label('Duration (hours)')
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail'),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category')->badge()->color(fn (string $state): string => match ($state) {
                    'Premium' => 'purple',
                    default => 'gray',
                }),
                Tables\Columns\TextColumn::make('level')->badge()->color(fn (string $state): string => match ($state) {
                    'Beginner' => 'success',
                    'Intermediate' => 'warning',
                    'Advanced' => 'danger',
                    default => 'gray',
                }),
                Tables\Columns\TextColumn::make('price')->label('Price (Rs.)')->money('NPR'),
                Tables\Columns\TextColumn::make('teacher.name')->label('Teacher'),
                Tables\Columns\TextColumn::make('lessons_count')->counts('lessons')->label('Lessons'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCourses::route('/'),
            'create' => CreateCourse::route('/create'),
            'edit' => EditCourse::route('/{record}/edit'),
        ];
    }
}

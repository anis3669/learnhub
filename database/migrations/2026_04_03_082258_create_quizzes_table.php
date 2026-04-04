<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('quizzes')) {
            Schema::create('quizzes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained()->onDelete('cascade');
                $table->foreignId('lesson_id')->nullable()->constrained()->nullOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->integer('time_limit_minutes')->default(30);
                $table->integer('passing_score')->default(60);
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });

            return;
        }

        Schema::table('quizzes', function (Blueprint $table) {
            if (! Schema::hasColumn('quizzes', 'course_id')) {
                $table->unsignedBigInteger('course_id')->nullable()->after('id');
                $table->index('course_id');
            }
            if (! Schema::hasColumn('quizzes', 'lesson_id')) {
                $table->unsignedBigInteger('lesson_id')->nullable()->after('course_id');
                $table->index('lesson_id');
            }
            if (! Schema::hasColumn('quizzes', 'title')) {
                $table->string('title')->default('Untitled')->after('lesson_id');
            }
            if (! Schema::hasColumn('quizzes', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (! Schema::hasColumn('quizzes', 'time_limit_minutes')) {
                $table->integer('time_limit_minutes')->default(30)->after('description');
            }
            if (! Schema::hasColumn('quizzes', 'passing_score')) {
                $table->integer('passing_score')->default(60)->after('time_limit_minutes');
            }
            if (! Schema::hasColumn('quizzes', 'is_published')) {
                $table->boolean('is_published')->default(true)->after('passing_score');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};

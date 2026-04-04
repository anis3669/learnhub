<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('courses')) {
            Schema::create('courses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
                $table->string('title');
                $table->text('description');
                $table->string('thumbnail')->nullable();
                $table->string('category')->default('General');
                $table->string('level')->default('Beginner');
                $table->boolean('is_published')->default(false);
                $table->integer('duration_hours')->default(0);
                $table->timestamps();
            });

            return;
        }

        Schema::table('courses', function (Blueprint $table) {
            if (! Schema::hasColumn('courses', 'teacher_id')) {
                $table->unsignedBigInteger('teacher_id')->nullable()->after('id');
                $table->index('teacher_id');
            }
            if (! Schema::hasColumn('courses', 'title')) {
                $table->string('title')->default('Untitled')->after('teacher_id');
            }
            if (! Schema::hasColumn('courses', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (! Schema::hasColumn('courses', 'thumbnail')) {
                $table->string('thumbnail')->nullable()->after('description');
            }
            if (! Schema::hasColumn('courses', 'category')) {
                $table->string('category')->default('General')->after('thumbnail');
            }
            if (! Schema::hasColumn('courses', 'level')) {
                $table->string('level')->default('Beginner')->after('category');
            }
            if (! Schema::hasColumn('courses', 'is_published')) {
                $table->boolean('is_published')->default(false)->after('level');
            }
            if (! Schema::hasColumn('courses', 'duration_hours')) {
                $table->integer('duration_hours')->default(0)->after('is_published');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};

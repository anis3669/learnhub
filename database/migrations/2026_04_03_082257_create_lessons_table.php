<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('lessons')) {
            Schema::create('lessons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('video_url')->nullable();
                $table->text('content')->nullable();
                $table->integer('duration_minutes')->default(0);
                $table->integer('order')->default(0);
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });

            return;
        }

        Schema::table('lessons', function (Blueprint $table) {
            if (! Schema::hasColumn('lessons', 'course_id')) {
                $table->unsignedBigInteger('course_id')->nullable()->after('id');
                $table->index('course_id');
            }
            if (! Schema::hasColumn('lessons', 'title')) {
                $table->string('title')->default('Untitled')->after('course_id');
            }
            if (! Schema::hasColumn('lessons', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (! Schema::hasColumn('lessons', 'video_url')) {
                $table->string('video_url')->nullable()->after('description');
            }
            if (! Schema::hasColumn('lessons', 'content')) {
                $table->text('content')->nullable()->after('video_url');
            }
            if (! Schema::hasColumn('lessons', 'duration_minutes')) {
                $table->integer('duration_minutes')->default(0)->after('content');
            }
            if (! Schema::hasColumn('lessons', 'order')) {
                $table->integer('order')->default(0)->after('duration_minutes');
            }
            if (! Schema::hasColumn('lessons', 'is_published')) {
                $table->boolean('is_published')->default(true)->after('order');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};

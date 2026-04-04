<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('questions')) {
            Schema::create('questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
                $table->text('question_text');
                $table->string('type')->default('multiple_choice');
                $table->integer('points')->default(10);
                $table->integer('order')->default(0);
                $table->timestamps();
            });

            return;
        }

        Schema::table('questions', function (Blueprint $table) {
            if (! Schema::hasColumn('questions', 'quiz_id')) {
                $table->unsignedBigInteger('quiz_id')->nullable()->after('id');
                $table->index('quiz_id');
            }
            if (! Schema::hasColumn('questions', 'question_text')) {
                $table->text('question_text')->nullable()->after('quiz_id');
            }
            if (! Schema::hasColumn('questions', 'type')) {
                $table->string('type')->default('multiple_choice')->after('question_text');
            }
            if (! Schema::hasColumn('questions', 'points')) {
                $table->integer('points')->default(10)->after('type');
            }
            if (! Schema::hasColumn('questions', 'order')) {
                $table->integer('order')->default(0)->after('points');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};

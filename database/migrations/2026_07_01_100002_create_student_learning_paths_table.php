<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_learning_paths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('path_type')->default('no_coding');
            $table->string('unlocked_level')->default('Introduction');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_learning_paths');
    }
};

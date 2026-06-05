<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->boolean('prerequisites_met')->default(false)->change();
        });

        DB::statement("UPDATE enrollments SET prerequisites_met = true");
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->boolean('prerequisites_met')->default(true)->change();
        });
    }
};

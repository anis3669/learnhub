<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_payments', function (Blueprint $table) {
            // Forged-callback protection relies on pidx being a non-null, unique
            // reference. Drop any legacy NULL pidx rows first, then tighten the column.
            if (Schema::hasColumn('course_payments', 'pidx')) {
                DB::table('course_payments')
                    ->whereNull('pidx')
                    ->delete();

                $table->string('pidx')->nullable(false)->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('course_payments', function (Blueprint $table) {
            if (Schema::hasColumn('course_payments', 'pidx')) {
                $table->string('pidx')->nullable()->change();
            }
        });
    }
};

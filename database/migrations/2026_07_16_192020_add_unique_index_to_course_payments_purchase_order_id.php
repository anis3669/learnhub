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
            if (! Schema::hasColumn('course_payments', 'purchase_order_id')) {
                return;
            }

            $duplicates = DB::table('course_payments')
                ->select('purchase_order_id', DB::raw('COUNT(*) as count'))
                ->groupBy('purchase_order_id')
                ->having('count', '>', 1)
                ->get();

            if ($duplicates->isNotEmpty()) {
                foreach ($duplicates as $duplicate) {
                    DB::table('course_payments')
                        ->where('purchase_order_id', $duplicate->purchase_order_id)
                        ->where('status', '!=', 'pending')
                        ->update(['purchase_order_id' => null]);
                }
            }

            $table->unique('purchase_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('course_payments', function (Blueprint $table) {
            $table->dropUnique('course_payments_purchase_order_id_unique');
        });
    }
};

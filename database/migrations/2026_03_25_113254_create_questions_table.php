<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Kept as a historical migration; the full schema is created later.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op rollback because this migration no longer creates the table.
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE accounts MODIFY COLUMN type ENUM('cash', 'bank', 'ewallet', 'credit_card', 'savings') NOT NULL DEFAULT 'cash'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE accounts MODIFY COLUMN type ENUM('cash', 'bank', 'ewallet', 'credit_card') NOT NULL DEFAULT 'cash'");
    }
};

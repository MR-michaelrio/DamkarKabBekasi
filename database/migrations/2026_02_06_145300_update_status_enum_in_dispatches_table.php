<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            // MySQL-specific enum modification
            DB::statement("ALTER TABLE dispatches CHANGE COLUMN status status ENUM('pending', 'assigned', 'on_route', 'completed') DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE dispatches CHANGE COLUMN status status ENUM('pending', 'on_route', 'completed') DEFAULT 'pending'");
        }
    }
};

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
            DB::statement("ALTER TABLE dispatches CHANGE COLUMN status status ENUM('pending', 'assigned', 'enroute_pickup', 'on_scene', 'enroute_destination', 'arrived_destination', 'enroute_return', 'arrived_return', 'completed') DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE dispatches CHANGE COLUMN status status ENUM('pending', 'assigned', 'enroute_pickup', 'on_scene', 'enroute_destination', 'arrived_destination', 'enroute_return', 'completed') DEFAULT 'pending'");
        }
    }
};

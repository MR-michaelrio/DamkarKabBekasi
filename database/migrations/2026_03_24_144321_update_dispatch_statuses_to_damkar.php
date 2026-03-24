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
        // 1. Map existing statuses to new ones
        DB::table('dispatches')->where('status', 'assigned')->update(['status' => 'pending']);
        DB::table('dispatches')->where('status', 'enroute_pickup')->update(['status' => 'on_the_way_scene']);
        DB::table('dispatches')->whereIn('status', ['enroute_destination', 'arrived_destination', 'enroute_return', 'arrived_return'])->update(['status' => 'on_the_way_kantor_pos']);

        // 2. Update the ENUM definition
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE dispatches CHANGE COLUMN status status ENUM('pending', 'on_the_way_scene', 'on_scene', 'on_the_way_kantor_pos', 'completed', 'cancelled') DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE dispatches CHANGE COLUMN status status ENUM('pending', 'assigned', 'enroute_pickup', 'on_scene', 'enroute_destination', 'arrived_destination', 'enroute_return', 'arrived_return', 'completed', 'cancelled') DEFAULT 'pending'");
        }
    }
};

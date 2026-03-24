<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Jika kolom lama ada → rename
        if (Schema::hasColumn('dispatches', 'pickup_location')) {
            Schema::table('dispatches', function (Blueprint $table) {
                $table->renameColumn('pickup_location', 'pickup_address');
            });
        }

        // Jika kolom baru belum ada → buat
        if (!Schema::hasColumn('dispatches', 'pickup_address')) {
            Schema::table('dispatches', function (Blueprint $table) {
                $table->string('pickup_address')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispatches', function (Blueprint $table) {
           if (Schema::hasColumn('dispatches', 'pickup_address')) {
                $table->renameColumn('pickup_address', 'pickup_location');
            }

            // $table->renameColumn('pickup_address', 'pickup_location');
        });
    }
};

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
        Schema::table('ambulances', function (Blueprint $table) {
            if (!Schema::hasColumn('ambulances', 'fcm_token')) {
                $table->text('fcm_token')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ambulances', function (Blueprint $table) {
            $table->dropColumn('fcm_token');
        });
    }
};

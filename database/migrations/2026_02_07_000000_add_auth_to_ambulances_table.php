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
            if (!Schema::hasColumn('ambulances', 'username')) {
                $table->string('username')->unique()->nullable()->after('plate_number');
            }
            if (!Schema::hasColumn('ambulances', 'password')) {
                $table->string('password')->nullable()->after('username');
            }
            if (!Schema::hasColumn('ambulances', 'remember_token')) {
                $table->rememberToken()->after('password');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ambulances', function (Blueprint $table) {
            if (Schema::hasColumn('ambulances', 'username')) {
                $table->dropColumn('username');
            }
            if (Schema::hasColumn('ambulances', 'password')) {
                $table->dropColumn('password');
            }
            if (Schema::hasColumn('ambulances', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
        });
    }
};

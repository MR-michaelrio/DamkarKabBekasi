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
        Schema::table('patient_requests', function (Blueprint $table) {
            $table->string('trip_type')->default('one_way')->after('destination'); // one_way, round_trip
            $table->text('return_address')->nullable()->after('trip_type');
        });

        Schema::table('dispatches', function (Blueprint $table) {
            $table->string('trip_type')->default('one_way')->after('destination'); // one_way, round_trip
            $table->text('return_address')->nullable()->after('trip_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_requests', function (Blueprint $table) {
            $table->dropColumn(['trip_type', 'return_address']);
        });

        Schema::table('dispatches', function (Blueprint $table) {
            $table->dropColumn(['trip_type', 'return_address']);
        });
    }
};

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
        Schema::table('dispatches', function (Blueprint $table) {
            $table->string('blok')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('nomor')->nullable();
        });

        Schema::table('patient_requests', function (Blueprint $table) {
            $table->string('blok')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('nomor')->nullable();
        });

        // Update Enum for patient_condition
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE dispatches MODIFY COLUMN patient_condition ENUM('emergency', 'kontrol', 'jenazah', 'pasien_pulang', 'kebakaran', 'rescue')");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE patient_requests MODIFY COLUMN patient_condition ENUM('emergency', 'kontrol', 'pasien_pulang', 'kebakaran', 'rescue')");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispatches', function (Blueprint $table) {
            $table->dropColumn(['blok', 'rt', 'rw', 'kelurahan', 'kecamatan', 'nomor']);
        });

        Schema::table('patient_requests', function (Blueprint $table) {
            $table->dropColumn(['blok', 'rt', 'rw', 'kelurahan', 'kecamatan', 'nomor']);
        });

        // Revert Enum
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE dispatches MODIFY COLUMN patient_condition ENUM('emergency', 'kontrol', 'jenazah', 'pasien_pulang')");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE patient_requests MODIFY COLUMN patient_condition ENUM('emergency', 'kontrol', 'pasien_pulang')");
        }
    }
};

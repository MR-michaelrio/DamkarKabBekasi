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
        DB::statement("ALTER TABLE patient_requests MODIFY COLUMN service_type ENUM('kebakaran', 'rescue', 'ambulance', 'jenazah')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE patient_requests MODIFY COLUMN service_type ENUM('ambulance', 'jenazah')");
    }
};

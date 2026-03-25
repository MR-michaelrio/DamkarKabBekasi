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
            // Add missing timestamps column that are used in DriverDashboardController
            if (!Schema::hasColumn('dispatches', 'pickup_at')) {
                $table->dateTime('pickup_at')->nullable()->after('assigned_at');
            }
            if (!Schema::hasColumn('dispatches', 'hospital_at')) {
                $table->dateTime('hospital_at')->nullable()->after('pickup_at');
            }
            if (!Schema::hasColumn('dispatches', 'completed_at')) {
                $table->dateTime('completed_at')->nullable()->after('hospital_at');
            }

            // Consistency fix for patient_phone if it was named 'phone'
            if (Schema::hasColumn('dispatches', 'phone') && !Schema::hasColumn('dispatches', 'patient_phone')) {
                // We use renameColumn if possible, but for SQLite compatibility in some Laravel versions 
                // it might be tricky. Better to just add it if missing or leave as is if the model is updated.
                // However, the model explicitly uses 'patient_phone'.
                // Let's check driver
                if (DB::getDriverName() !== 'sqlite') {
                    $table->renameColumn('phone', 'patient_phone');
                } else {
                    // For SQLite, we might need a separate migration or just add the column
                    $table->string('patient_phone')->nullable()->after('patient_condition');
                }
            } else if (!Schema::hasColumn('dispatches', 'patient_phone')) {
                $table->string('patient_phone')->nullable()->after('patient_condition');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispatches', function (Blueprint $table) {
            $table->dropColumn(['pickup_at', 'hospital_at', 'completed_at']);
            // Refrain from dropping patient_phone if it was renamed to avoid data loss in down()
        });
    }
};

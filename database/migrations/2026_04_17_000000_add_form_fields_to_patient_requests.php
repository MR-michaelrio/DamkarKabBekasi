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
            // Add pickup_time if not exists
            if (!Schema::hasColumn('patient_requests', 'pickup_time')) {
                $table->time('pickup_time')->nullable();
            }

            // C. DETAIL KEJADIAN
            if (!Schema::hasColumn('patient_requests', 'event_description')) {
                $table->text('event_description')->nullable();
            }
            if (!Schema::hasColumn('patient_requests', 'building_type')) {
                $table->string('building_type')->nullable();
            }
            if (!Schema::hasColumn('patient_requests', 'fire_cause')) {
                $table->string('fire_cause')->nullable();
            }
            if (!Schema::hasColumn('patient_requests', 'affected_area')) {
                $table->string('affected_area')->nullable();
            }

            // D. DATA PEMILIK
            if (!Schema::hasColumn('patient_requests', 'owner_name')) {
                $table->string('owner_name')->nullable();
            }
            if (!Schema::hasColumn('patient_requests', 'owner_age')) {
                $table->string('owner_age')->nullable();
            }
            if (!Schema::hasColumn('patient_requests', 'owner_phone')) {
                $table->string('owner_phone')->nullable();
            }
            if (!Schema::hasColumn('patient_requests', 'owner_profession')) {
                $table->string('owner_profession')->nullable();
            }

            // F. DATA KETUA RT/RW
            if (!Schema::hasColumn('patient_requests', 'community_leader_name')) {
                $table->string('community_leader_name')->nullable();
            }
            if (!Schema::hasColumn('patient_requests', 'community_leader_phone')) {
                $table->string('community_leader_phone')->nullable();
            }

            // G. OPERASIONAL PEMADAM
            if (!Schema::hasColumn('patient_requests', 'unit_assistance')) {
                $table->text('unit_assistance')->nullable();
            }

            // H. PENGGUNAAN PERALATAN
            if (!Schema::hasColumn('patient_requests', 'time_finished')) {
                $table->time('time_finished')->nullable();
            }
            if (!Schema::hasColumn('patient_requests', 'scba_usage')) {
                $table->integer('scba_usage')->nullable();
            }
            if (!Schema::hasColumn('patient_requests', 'apar_usage')) {
                $table->integer('apar_usage')->nullable();
            }

            // I. DATA KORBAN
            if (!Schema::hasColumn('patient_requests', 'injured_count')) {
                $table->integer('injured_count')->nullable();
            }
            if (!Schema::hasColumn('patient_requests', 'fatalities_count')) {
                $table->integer('fatalities_count')->nullable();
            }
            if (!Schema::hasColumn('patient_requests', 'displaced_count')) {
                $table->integer('displaced_count')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_requests', function (Blueprint $table) {
            $table->dropColumn([
                'pickup_time',
                'event_description',
                'building_type',
                'fire_cause',
                'affected_area',
                'owner_name',
                'owner_age',
                'owner_phone',
                'owner_profession',
                'community_leader_name',
                'community_leader_phone',
                'unit_assistance',
                'time_finished',
                'scba_usage',
                'apar_usage',
                'injured_count',
                'fatalities_count',
                'displaced_count',
            ]);
        });
    }
};

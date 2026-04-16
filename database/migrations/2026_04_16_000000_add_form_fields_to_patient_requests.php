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
            // C. DETAIL KEJADIAN
            if (!Schema::hasColumn('patient_requests', 'event_description')) {
                $table->longText('event_description')->nullable()->comment('Kronologi Kejadian');
            }
            if (!Schema::hasColumn('patient_requests', 'building_type')) {
                $table->string('building_type')->nullable()->comment('Jenis Bangunan yang Terbakar');
            }
            if (!Schema::hasColumn('patient_requests', 'fire_cause')) {
                $table->string('fire_cause')->nullable()->comment('Penyebab Kebakaran');
            }
            if (!Schema::hasColumn('patient_requests', 'affected_area')) {
                $table->string('affected_area')->nullable()->comment('Luas Area Terdampak');
            }

            // D. DATA PEMILIK
            if (!Schema::hasColumn('patient_requests', 'owner_name')) {
                $table->string('owner_name')->nullable()->comment('Nama Pemilik');
            }
            if (!Schema::hasColumn('patient_requests', 'owner_age')) {
                $table->string('owner_age')->nullable()->comment('Umur Pemilik');
            }
            if (!Schema::hasColumn('patient_requests', 'owner_phone')) {
                $table->string('owner_phone')->nullable()->comment('No. Telepon Pemilik');
            }
            if (!Schema::hasColumn('patient_requests', 'owner_profession')) {
                $table->string('owner_profession')->nullable()->comment('Pekerjaan Pemilik');
            }

            // F. DATA KETUA RT/RW
            if (!Schema::hasColumn('patient_requests', 'community_leader_name')) {
                $table->string('community_leader_name')->nullable()->comment('Nama Ketua RT/RW');
            }
            if (!Schema::hasColumn('patient_requests', 'community_leader_phone')) {
                $table->string('community_leader_phone')->nullable()->comment('No. Telepon Ketua RT/RW');
            }

            // G. OPERASIONAL PEMADAM
            if (!Schema::hasColumn('patient_requests', 'unit_assistance')) {
                $table->string('unit_assistance')->nullable()->comment('Bantuan Unit Mobil');
            }

            // A. INFORMASI KEJADIAN - Waktu Selesai Penanganan (hanya dispatcher)
            if (!Schema::hasColumn('patient_requests', 'time_finished')) {
                $table->time('time_finished')->nullable()->comment('Waktu Selesai Penanganan');
            }

            // H. PENGGUNAAN PERALATAN (hanya dispatcher)
            if (!Schema::hasColumn('patient_requests', 'scba_usage')) {
                $table->integer('scba_usage')->nullable()->default(0)->comment('Penggunaan SCBA - Tabung');
            }
            if (!Schema::hasColumn('patient_requests', 'apar_usage')) {
                $table->integer('apar_usage')->nullable()->default(0)->comment('Penggunaan APAR - Tabung');
            }

            // I. DATA KORBAN (hanya dispatcher)
            if (!Schema::hasColumn('patient_requests', 'injured_count')) {
                $table->integer('injured_count')->nullable()->default(0)->comment('Korban Luka-luka');
            }
            if (!Schema::hasColumn('patient_requests', 'fatalities_count')) {
                $table->integer('fatalities_count')->nullable()->default(0)->comment('Korban Jiwa');
            }
            if (!Schema::hasColumn('patient_requests', 'displaced_count')) {
                $table->integer('displaced_count')->nullable()->default(0)->comment('Korban Terdampak');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_requests', function (Blueprint $table) {
            $columns = [
                'event_description', 'building_type', 'fire_cause', 'affected_area',
                'owner_name', 'owner_age', 'owner_phone', 'owner_profession',
                'community_leader_name', 'community_leader_phone',
                'unit_assistance', 'time_finished',
                'scba_usage', 'apar_usage',
                'injured_count', 'fatalities_count', 'displaced_count'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('patient_requests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

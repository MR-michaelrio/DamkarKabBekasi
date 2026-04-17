<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PatientRequest;
use Carbon\Carbon;

class InsertIncidentReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data dari form laporan masyarakat
        $data = [
            'patient_name' => 'test 00',                          // 12. Nama Pelapor
            'service_type' => 'kebakaran',                        // Jenis Kejadian
            'request_date' => Carbon::now()->format('Y-m-d'),     // Tanggal Kejadian
            'pickup_time' => Carbon::now()->format('H:i'),        // Jam Kejadian
            'phone' => '0821215454',                              // 12. No.Telp Pelapor
            'pickup_address' => 'test yyh',                       // 9. Alamat / 2. Lokasi Kebakaran
            'destination' => null,
            'patient_condition' => 'kebakaran',
            'status' => 'pending',

            // 3. Kronologi
            'event_description' => null,                          // "-"
            
            // 2. Lokasi Kebakaran detail
            'blok' => null,
            'rt' => '21',                                         // Dari "RT 21 / RW 20"
            'rw' => '20',                                         // Dari "RT 21 / RW 20"
            'kelurahan' => 'Cengkareng Timur',                   // 9. Kelurahan/Desa
            'kecamatan' => 'Cengkareng',                         // 9. Kecamatan
            'nomor' => null,

            // 5. Jenis Bangunan yang terbakar
            'building_type' => null,                              // "-"
            
            // 6. Penyebab Kebakaran
            'fire_cause' => null,                                 // "-"
            
            // 7. Luas Area
            'affected_area' => null,                              // "-"

            // 8-10. Data Pemilik
            'owner_name' => null,                                 // 8. Nama Pemilik "-"
            'owner_age' => null,                                  // 9. Umur "-"
            'owner_phone' => null,
            'owner_profession' => null,                           // 10. Pekerjaan "-"

            // 12. Data Ketua RT/RW
            'community_leader_name' => 'RT 21 / RW 20',          // 12. Nama Ketua RT/RW
            'community_leader_phone' => null,                     // 13. No.Telp Ketua RT/RW "-"

            // 16. Bantuan Unit Mobil Pemadam
            'unit_assistance' => null,                            // 16. "-"

            // 17. Penggunaan BA/SCBA dan APAR
            'time_finished' => null,
            'scba_usage' => 0,                                    // 17. Penggunaan BA/SCBA: 0
            'apar_usage' => 0,                                    // 17. Penggunaan APAR: 0

            // 18. Korban Kebakaran
            'injured_count' => 0,                                 // 18. Luka-Luka: 0
            'fatalities_count' => 0,                              // 18. Korban Jiwa: 0
            'displaced_count' => 0,                               // 18. Korban Terdampak: 0
        ];

        // Insert data
        PatientRequest::create($data);

        $this->command->info('✅ Data laporan kebakaran berhasil ditambahkan ke database!');
    }
}

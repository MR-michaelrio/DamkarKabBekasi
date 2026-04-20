<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kejadian Kebakaran</title>
    <style>
        @page {
            margin: 0mm 5mm;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            margin: 0;
            padding: 15px;
            color: #000;
            background-color: #fff;
        }

        .paper {
            /* background-color: #fff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 20mm;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative; */
        }

        .header {
            text-align: center;
            border-bottom: 5px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
            position: relative;
        }

        .header .logo-dinas {
            position: absolute;
            left: 0;
            top: 15;
            width: 100px;
            height: auto;
        }

        .header .logo-damkar {
            position: absolute;
            right: 0;
            top: 15;
            width: 120px;
            height: auto;
        }

        .header h1,
        .header h2,
        .header h3 {
            margin: 0;
            text-transform: uppercase;
        }

        .header h1 {
            font-size: 16pt;
            font-weight: bold;
        }

        .header h2 {
            font-size: 20pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            margin: 2px 0;
            font-size: 9pt;
        }

        .header .bekasi {
            font-weight: bold;
            letter-spacing: 2px;
            font-size: 12pt;
            margin-top: 5px;
        }

        .header-double {
            border-bottom: 4px double #000;
        }

        .top-info {
            width: 100%;
            margin-bottom: 20px;
        }

        .left-info table,
        .right-info table {
            border-collapse: collapse;
            font-size: 10pt;
        }

        .left-info td,
        .right-info td {
            vertical-align: top;
            padding: 1px 5px;
        }

        .content {
            margin-top: 10px;
        }

        .content-intro {
            margin-bottom: 15px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table td {
            vertical-align: top;
            padding: 2px 5px;
        }

        .label-col {
            width: 30%;
        }

        .colon-col {
            width: 5px;
        }

        .value-col {
            width: calc(70% - 5px);
        }

        .footer-sentence {
            margin-bottom: 30px;
        }

        .signatures {
            width: 100%;
            margin-top: 20px;
        }

        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-name {
            margin-top: 70px;
            font-weight: bold;
            text-decoration: underline;
        }

        .signature-title {
            font-weight: bold;
        }

        .signature-rank {
            font-size: 9pt;
        }

        /* Styles for Page 2 (Berita Acara) */
        .ba-container {
            text-align: center;
            margin-bottom: 15px;
        }

        .ba-title {
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            font-size: 12pt;
            margin-bottom: 2px;
        }

        .ba-number {
            font-weight: bold;
            font-size: 11pt;
        }

        .intro-text {
            margin-bottom: 15px;
            text-align: justify;
            font-size: 10pt;
        }

        .data-table-ba {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10pt;
        }

        .data-table-ba td {
            vertical-align: top;
            padding: 2px 0;
        }

        .num-col-ba {
            width: 30px;
            text-align: left;
        }

        .label-col-ba {
            width: 250px;
        }

        .colon-col-ba {
            width: 15px;
            text-align: center;
        }

        .signature-section-ba {
            width: 100%;
            margin-top: 30px;
            font-size: 10pt;
        }

        .signature-section-ba td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-space-ba {
            height: 60px;
        }

        .signature-name-ba {
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .signature-role-ba {
            font-weight: bold;
            text-transform: uppercase;
        }

        .paper {
            page-break-after: always;
        }

        .paper:last-child {
            page-break-after: avoid;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
                background-color: #fff;
            }

            .paper {
                box-shadow: none;
                margin: 0;
                width: 100%;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <!-- Halaman 1: Laporan Kejadian Kebakaran -->
    <div class="paper">
        <div class="header">
            <img src="<?php echo e(public_path('logo-dinas.png')); ?>" class="logo-dinas" alt="Logo">
            <h1>Pemerintah Kabupaten Bekasi</h1>
            <h2>Dinas Pemadam Kebakaran</h2>
            <p>Jalan Teuku Umar No.1 Cikarang Barat</p>
            <p>Desa Ganda Sari Kecamatan Cikarang Barat Kabupaten Bekasi – Jawa Barat</p>
            <p>(021)-89101527</p>
            <div class="bekasi">B E K A S I</div>
            <img src="<?php echo e(public_path('logo-damkar.png')); ?>" class="logo-damkar" alt="Logo">
        </div>

        <table class="top-info">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <div class="left-info">
                        <table>
                            <tr>
                                <td>Nomor</td>
                                <td>:</td>
                                <td><?php echo e($nomor ?? '- / /Damkar/2025'); ?></td>
                            </tr>
                            <tr>
                                <td>Sifat</td>
                                <td>:</td>
                                <td><?php echo e($sifat ?? 'Penting'); ?></td>
                            </tr>
                            <tr>
                                <td>Hal</td>
                                <td>:</td>
                                <td><span style="font-weight: bold; text-decoration: underline;">Laporan Kejadian
                                        Kebakaran</span></td>
                            </tr>
                            <tr>
                                <td>Lampiran</td>
                                <td>:</td>
                                <td><?php echo e($lampiran ?? '-'); ?></td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <div class="right-info">
                        <table style="margin-left: auto;">
                            <tr>
                                <td><?php echo e($place_date ?? 'Bekasi'); ?></td>
                            </tr>
                            <tr>
                                <td>Kepada</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">Yth. Kepala Dinas Pemadam Kebakaran</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">Kabupaten Bekasi</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">Di Bekasi</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <div class="content">
            <p class="content-intro">Dengan ini kami laporkan kejadian kebakaran di Wilayah Kabupaten Bekasi, Sebagai
                Berikut :</p>

            <table class="data-table">
                <tr>
                    <td class="label-col">1. Hari/Tanggal</td>
                    <td class="colon-col">:</td>
                    <td class="value-col"><?php echo e($day_date ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="label-col">2. Waktu Kejadian</td>
                    <td class="colon-col"></td>
                    <td class="value-col"></td>
                </tr>
                <tr>
                    <td style="padding-left: 22px;">Laporan Kejadian</td>
                    <td>:</td>
                    <td><?php echo e($time_report ?? '-'); ?> WIB</td>
                </tr>
                <tr>
                    <td style="padding-left: 22px;">Unit Berangkat</td>
                    <td>:</td>
                    <td><?php echo e($time_departure ?? '-'); ?> WIB</td>
                </tr>
                <tr>
                    <td style="padding-left: 22px;">Tiba di TKK</td>
                    <td>:</td>
                    <td><?php echo e($time_arrival ?? '-'); ?> WIB</td>
                </tr>
                <tr>
                    <td style="padding-left: 22px;">Selesai Penanganan</td>
                    <td>:</td>
                    <td><?php echo e($time_finished ?? '-'); ?> WIB</td>
                </tr>
                <tr>
                    <td class="label-col">3. Kronologi</td>
                    <td class="colon-col">:</td>
                    <td class="value-col"><?php echo e($chronology ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="label-col">4. Lokasi</td>
                    <td class="colon-col"></td>
                    <td class="value-col"></td>
                </tr>
                <tr>
                    <td style="padding-left: 22px;">Alamat</td>
                    <td>:</td>
                    <td><?php echo e($address ?? '-'); ?></td>
                </tr>
                <tr>
                    <td style="padding-left: 22px;">Kelurahan/Desa</td>
                    <td>:</td>
                    <td><?php echo e($village ?? '-'); ?></td>
                </tr>
                <tr>
                    <td style="padding-left: 22px;">Kecamatan</td>
                    <td>:</td>
                    <td><?php echo e($district ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="label-col">5. Nama Pelapor</td>
                    <td class="colon-col">:</td>
                    <td class="value-col"><?php echo e($reporter_name ?? '-'); ?></td>
                </tr>
                <tr>
                    <td style="padding-left: 22px;">No. Telp Pelapor</td>
                    <td>:</td>
                    <td><?php echo e($reporter_phone ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="label-col">6. Nama Ketua RT/RW</td>
                    <td class="colon-col">:</td>
                    <td class="value-col"><?php echo e($community_leader_name ?? '-'); ?></td>
                </tr>
                <tr>
                    <td style="padding-left: 22px;">No.Telp Ketua RT/RW</td>
                    <td>:</td>
                    <td><?php echo e($community_leader_phone ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="label-col">7. Luas Area</td>
                    <td class="colon-col">:</td>
                    <td class="value-col"><?php echo e($area_size ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="label-col">8. Bangunan yang terbakar</td>
                    <td class="colon-col">:</td>
                    <td class="value-col"><?php echo e($building_type ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="label-col">9. Nama Pemilik</td>
                    <td class="colon-col">:</td>
                    <td class="value-col"><?php echo e($owner_name ?? '-'); ?></td>
                </tr>
                <tr>
                    <td style="padding-left: 22px;">Umur</td>
                    <td>:</td>
                    <td><?php echo e($owner_age ?? '-'); ?></td>
                </tr>
                <tr>
                    <td style="padding-left: 22px;">Telp</td>
                    <td>:</td>
                    <td><?php echo e($owner_phone ?? '-'); ?></td>
                </tr>
                <tr>
                    <td style="padding-left: 22px;">Pekerjaan</td>
                    <td>:</td>
                    <td><?php echo e($owner_occupation ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="label-col">10. Asal Api</td>
                    <td class="colon-col">:</td>
                    <td class="value-col"><?php echo e($fire_origin ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="label-col">11. Pengerahan Unit Mobil</td>
                    <td class="colon-col">:</td>
                    <td class="value-col"><?php echo e($unit_count ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="label-col">12. No. Seri Kendaraan</td>
                    <td class="colon-col">:</td>
                    <td class="value-col"><?php echo e($vehicle_number ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="label-col">13. Bantuan Unit Mobil</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">
                        <?php if(isset($additional_units) && count($additional_units) > 0): ?>
                            <?php $__currentLoopData = $additional_units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div><?php echo e($unit); ?></div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="label-col">14. Penggunaan BA/SCBA</td>
                    <td class="colon-col">:</td>
                    <td class="value-col"><?php echo e($scba_usage ?? '0'); ?> Tabung</td>
                </tr>
                <tr>
                    <td style="padding-left: 29px;">Penggunaan APAR</td>
                    <td>:</td>
                    <td><?php echo e($apar_usage ?? '0'); ?> Tabung</td>
                </tr>
                <tr>
                    <td class="label-col">15. Korban Kebakaran</td>
                    <td class="colon-col"></td>
                    <td class="value-col"></td>
                </tr>
                <tr>
                    <td style="padding-left: 29px;">Luka - Luka</td>
                    <td>:</td>
                    <td><?php echo e($injured ?? '0'); ?> Orang</td>
                </tr>
                <tr>
                    <td style="padding-left: 29px;">Korban Jiwa</td>
                    <td>:</td>
                    <td><?php echo e($fatalities ?? '0'); ?> Orang</td>
                </tr>
                <tr>
                    <td style="padding-left: 29px;">Korban Terdampak</td>
                    <td>:</td>
                    <td><?php echo e($displaced ?? '0'); ?> Orang</td>
                </tr>
            </table>

            <p class="footer-sentence">Demikian Laporan ini dibuat dengan sebenarnya, Demikian agar maklum.</p>
        </div>

        <table class="signatures">
            <tr>
                <td>
                    <p>Mengetahui</p>
                    <p class="signature-title">KEPALA BIDANG PEMADAM DAN PENYELAMATAN</p>
                    <p class="signature-name"><?php echo e($approver_name ?? 'MULYADI HADI SAPUTRA, SE'); ?></p>
                    <p class="signature-rank"><?php echo e($approver_rank ?? 'Pembina – IV/a'); ?></p>
                    <p>NIP. <?php echo e($approver_nip ?? '19740410 200311 1 001'); ?></p>
                </td>
                <td>
                    <p>&nbsp;</p>
                    <p class="signature-title">KEPALA SEKSI PEMADAM DAN <br>INVESTIGASI</p>
                    <p class="signature-name"><?php echo e($officer_name ?? 'AHMAD FAUZI, ST'); ?></p>
                    <p class="signature-rank"><?php echo e($officer_rank ?? 'Penata Tk.I – III/d'); ?></p>
                    <p>NIP. <?php echo e($officer_nip ?? '19751104 200901 1 001'); ?></p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Halaman 2: Berita Acara Kejadian Kebakaran -->
    <div class="paper">
        <div class="header">
            <img src="<?php echo e(public_path('logo-dinas.png')); ?>" class="logo-dinas" alt="Logo">
            <h1>Pemerintah Kabupaten Bekasi</h1>
            <h2>Dinas Pemadam Kebakaran</h2>
            <p>Jalan Teuku Umar No.1 Cikarang Barat</p>
            <p>Desa Ganda Sari Kecamatan Cikarang Barat Kabupaten Bekasi – Jawa Barat</p>
            <p>(021)-89101527</p>
            <div class="bekasi">B E K A S I</div>
            <img src="<?php echo e(public_path('logo-damkar.png')); ?>" class="logo-damkar" alt="Logo">
        </div>

        <div class="ba-container">
            <div class="ba-title">Berita Acara Kejadian Kebakaran</div>
            <div class="ba-number">NOMOR : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; /
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; /DAMKAR 2025</div>
        </div>

        <div class="intro-text">
            Pada Hari ini <?php echo e($ba_hari ?? '..........'); ?> tanggal <?php echo e($ba_tanggal ?? '...'); ?> bulan
            <?php echo e($ba_bulan ?? '..........'); ?> tahun <?php echo e($ba_tahun ?? '2026'); ?> Pukul <?php echo e($ba_pukul ?? '... : ...'); ?> WIB
            WIB, telah terjadi kebakaran di Wilayah Kabupaten Bekasi, Sebagai Berikut
        </div>

        <table class="data-table-ba">
            <tr>
                <td class="num-col-ba">1.</td>
                <td class="label-col-ba">Hari/Tanggal</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_hari_tanggal ?? '-'); ?></td>
            </tr>
            <tr>
                <td class="num-col-ba">2.</td>
                <td class="label-col-ba">Waktu Kejadian</td>
                <td class="colon-col-ba"></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>Laporan Kejadian</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_waktu_laporan ?? '-'); ?> WIB</td>
            </tr>
            <tr>
                <td></td>
                <td>Unit Berangkat</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_waktu_berangkat ?? '-'); ?> WIB</td>
            </tr>
            <tr>
                <td></td>
                <td>Tiba di TKK</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_waktu_tiba ?? '-'); ?> WIB</td>
            </tr>
            <tr>
                <td></td>
                <td>Selesai Penanganan</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_waktu_selesai ?? '-'); ?> WIB</td>
            </tr>
            <tr>
                <td class="num-col-ba">3.</td>
                <td class="label-col-ba">Kronologi</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_kronologi ?? '-'); ?></td>
            </tr>
            <tr>
                <td class="num-col-ba">4.</td>
                <td class="label-col-ba">Lokasi Kebakaran</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_lokasi_kebakaran ?? '-'); ?></td>
            </tr>
            <tr>
                <td class="num-col-ba">5.</td>
                <td class="label-col-ba">Jenis Bangunan yang terbakar</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_jenis_bangunan ?? '-'); ?></td>
            </tr>
            <tr>
                <td class="num-col-ba">6.</td>
                <td class="label-col-ba">Penyebab Kebakaran</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_penyebab ?? '-'); ?></td>
            </tr>
            <tr>
                <td class="num-col-ba">7.</td>
                <td class="label-col-ba">Luas Area</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_luas_area ?? '-'); ?></td>
            </tr>
            <tr>
                <td class="num-col-ba">8.</td>
                <td class="label-col-ba">Nama Pemilik</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_nama_pemilik ?? '-'); ?></td>
            </tr>
            <tr>
                <td class="num-col-ba">9.</td>
                <td class="label-col-ba">Umur</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_umur_pemilik ?? '-'); ?></td>
            </tr>
            <tr>
                <td class="num-col-ba">10.</td>
                <td class="label-col-ba">Pekerjaan</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_pekerjaan_pemilik ?? '-'); ?></td>
            </tr>
            <tr>
                <td class="num-col-ba">11.</td>
                <td class="label-col-ba">Lokasi</td>
                <td class="colon-col-ba">:</td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>Alamat</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_alamat ?? '-'); ?></td>
            </tr>
            <tr>
                <td></td>
                <td>Kelurahan/Desa</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_kelurahan ?? '-'); ?></td>
            </tr>
            <tr>
                <td></td>
                <td>Kecamatan</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_kecamatan ?? '-'); ?></td>
            </tr>
            <tr>
                <td class="num-col-ba">12.</td>
                <td class="label-col-ba">Nama Pelapor</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_nama_pelapor ?? '-'); ?></td>
            </tr>
            <tr>
                <td></td>
                <td>No.Telp Pelapor</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_telp_pelapor ?? '-'); ?></td>
            </tr>
            <tr>
                <td class="num-col-ba">13.</td>
                <td class="label-col-ba">Nama Ketua RT/RW</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_nama_rt_rw ?? '-'); ?></td>
            </tr>
            <tr>
                <td></td>
                <td>No.Telp Ketua RT/RW</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_telp_rt_rw ?? '-'); ?></td>
            </tr>
            <tr>
                <td class="num-col-ba">14.</td>
                <td class="label-col-ba">Unit Mobil Pemadam yang dikerahkan</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_jumlah_unit ?? '-'); ?> Unit</td>
            </tr>
            <tr>
                <td class="num-col-ba">15.</td>
                <td class="label-col-ba">No. Seri Kendaraan</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_no_seri_kendaraan ?? '-'); ?></td>
            </tr>
            <tr>
                <td class="num-col-ba">16.</td>
                <td class="label-col-ba">Bantuan Unit Mobil Pemadam</td>
                <td class="colon-col-ba">:</td>
                <td>
                    <?php if(isset($ba_bantuan_unit) && count($ba_bantuan_unit) > 0): ?>
                        <?php $__currentLoopData = $ba_bantuan_unit; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div><?php echo e($unit); ?></div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td class="num-col-ba">17.</td>
                <td class="label-col-ba">Penggunaan BA/SCBA</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_scba_usage ?? '0'); ?> Tabung</td>
            </tr>
            <tr>
                <td></td>
                <td>Penggunaan APAR</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_apar_usage ?? '0'); ?> Tabung</td>
            </tr>
            <tr>
                <td class="num-col-ba">18.</td>
                <td class="label-col-ba">Korban Kebakaran</td>
                <td class="colon-col-ba">:</td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>Luka – Luka</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_korban_luka ?? '...'); ?> Orang</td>
            </tr>
            <tr>
                <td></td>
                <td>Korban Jiwa</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_korban_jiwa ?? '...'); ?> Orang</td>
            </tr>
            <tr>
                <td></td>
                <td>Korban Terdampak</td>
                <td class="colon-col-ba">:</td>
                <td><?php echo e($ba_korban_terdampak ?? '...'); ?> Orang</td>
            </tr>
        </table>

        <div class="intro-text">
            Demikian Berita Acara Kejadian Kebakaran ini dibuat dengan sebenarnya, Demikian agar maklum.
        </div>

        <table class="signature-section-ba">
            <tr>
                <td></td>
                <td>
                    <?php echo e($ba_tanggal_laporan ?? 'Bekasi'); ?>

                </td>
            </tr>
            <tr>
                <td>
                    <div class="signature-role-ba">KOMANDAN REGU</div>
                    <div class="signature-space-ba"></div>
                    <div class="signature-name-ba"><?php echo e($ba_komandan_regu ?? 'SUKARDI YUSUF'); ?></div>
                    <div>NIP. <?php echo e($ba_nip_danru ?? '198709212025211001'); ?></div>
                </td>
                <td>
                    <div class="signature-role-ba">KOMANDAN PELETON III</div>
                    <div class="signature-space-ba"></div>
                    <div class="signature-name-ba"><?php echo e($ba_komandan_peleton ?? 'JAJANG SUSANTO, SI.P'); ?></div>
                    <div>NIP. <?php echo e($ba_nip_danton ?? '19682007101103'); ?></div>
                </td>
            </tr>
        </table>
    </div>
    <!-- Halaman 3a: Foto Kejadian (Halaman 1 dari 2) -->
    <?php if(isset($photos) && $photos->count() > 0): ?>
    <?php $photosPage1 = $photos->take(4); $photosPage2 = $photos->slice(4); ?>
    <div class="paper">
        <div class="header">
            <img src="<?php echo e(public_path('logo-dinas.png')); ?>" class="logo-dinas" alt="Logo">
            <h1>Pemerintah Kabupaten Bekasi</h1>
            <h2>Dinas Pemadam Kebakaran</h2>
            <p>Jalan Teuku Umar No.1 Cikarang Barat</p>
            <p>Desa Ganda Sari Kecamatan Cikarang Barat Kabupaten Bekasi – Jawa Barat</p>
            <p>(021)-89101527</p>
            <div class="bekasi">B E K A S I</div>
            <img src="<?php echo e(public_path('logo-damkar.png')); ?>" class="logo-damkar" alt="Logo">
        </div>

        <div class="ba-container">
            <div class="ba-title">Dokumentasi Foto Kejadian</div>
            <p style="text-align: center; margin-top: 5px; font-size: 10pt;">Foto yang diambil oleh petugas di lokasi kejadian</p>
        </div>

        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <?php $__currentLoopData = $photosPage1->chunk(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <td style="width: 50%; padding: 10px; vertical-align: top; text-align: center;">
                    <img src="<?php echo e(public_path('storage/' . $item->photo->photo_path)); ?>"
                         style="width: 100%; height: 210px; border: 1px solid #ccc;"
                         alt="Foto Kejadian">
                    <div style="margin-top: 6px; font-size: 9pt; color: #333; text-align: left;">
                        <?php if($item->photo->description): ?>
                            <em><?php echo e($item->photo->description); ?></em><br>
                        <?php endif; ?>
                        <strong>Petugas:</strong> <?php echo e($item->uploader); ?>

                    </div>
                </td>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($row->count() === 1): ?>
                <td style="width: 50%;"></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </table>
    </div>

    <!-- Halaman 3b: Foto Kejadian (Halaman 2 dari 2) -->
    <div class="paper">
        <div class="header">
            <img src="<?php echo e(public_path('logo-dinas.png')); ?>" class="logo-dinas" alt="Logo">
            <h1>Pemerintah Kabupaten Bekasi</h1>
            <h2>Dinas Pemadam Kebakaran</h2>
            <p>Jalan Teuku Umar No.1 Cikarang Barat</p>
            <p>Desa Ganda Sari Kecamatan Cikarang Barat Kabupaten Bekasi – Jawa Barat</p>
            <p>(021)-89101527</p>
            <div class="bekasi">B E K A S I</div>
            <img src="<?php echo e(public_path('logo-damkar.png')); ?>" class="logo-damkar" alt="Logo">
        </div>

        <div class="ba-container">
            <div class="ba-title">Dokumentasi Foto Kejadian</div>
            <p style="text-align: center; margin-top: 5px; font-size: 10pt;">Foto yang diambil oleh petugas di lokasi kejadian (lanjutan)</p>
        </div>

        <?php if($photosPage2->count() > 0): ?>
        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <?php $__currentLoopData = $photosPage2->chunk(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <td style="width: 50%; padding: 10px; vertical-align: top; text-align: center;">
                    <img src="<?php echo e(public_path('storage/' . $item->photo->photo_path)); ?>"
                         style="width: 100%; height: 210px; border: 1px solid #ccc;"
                         alt="Foto Kejadian">
                    <div style="margin-top: 6px; font-size: 9pt; color: #333; text-align: left;">
                        <?php if($item->photo->description): ?>
                            <em><?php echo e($item->photo->description); ?></em><br>
                        <?php endif; ?>
                        <strong>Petugas:</strong> <?php echo e($item->uploader); ?>

                    </div>
                </td>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($row->count() === 1): ?>
                <td style="width: 50%;"></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </table>
        <?php else: ?>
        <p style="margin-top: 30px; text-align: center; font-size: 10pt; color: #666;">
            <em>— Tidak ada foto tambahan —</em>
        </p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Halaman 4: Data Respon Unit Armada -->
    <div class="paper">
        <div class="header">
            <img src="<?php echo e(public_path('logo-dinas.png')); ?>" class="logo-dinas" alt="Logo">
            <h1>Pemerintah Kabupaten Bekasi</h1>
            <h2>Dinas Pemadam Kebakaran</h2>
            <p>Jalan Teuku Umar No.1 Cikarang Barat</p>
            <p>Desa Ganda Sari Kecamatan Cikarang Barat Kabupaten Bekasi – Jawa Barat</p>
            <p>(021)-89101527</p>
            <div class="bekasi">B E K A S I</div>
            <img src="<?php echo e(public_path('logo-damkar.png')); ?>" class="logo-damkar" alt="Logo">
        </div>

        <div class="ba-container">
            <div class="ba-title">Data Respon Unit Armada</div>
            <p style="text-align: center; margin-top: 5px; font-size: 10pt;">Detail waktu respon setiap unit yang dikerahkan ke lokasi kejadian</p>
        </div>

        <style>
            .unit-card {
                margin-top: 18px;
                margin-bottom: 20px;
            }

            .unit-info {
                font-size: 10.5pt;
                line-height: 1.8;
                margin-bottom: 8px;
            }

            .unit-time-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 10pt;
            }

            .unit-time-table th {
                border: 1px solid #000;
                padding: 7px 10px;
                text-align: center;
                font-weight: bold;
                background-color: #f2f2f2;
            }

            .unit-time-table td {
                border: 1px solid #000;
                padding: 7px 10px;
                vertical-align: middle;
            }

            .unit-time-table .col-desc {
                width: 55%;
                text-align: left;
            }

            .unit-time-table .col-time {
                width: 25%;
                text-align: center;
            }

            .unit-time-table .col-dur {
                width: 20%;
                text-align: center;
                font-weight: bold;
            }
        </style>

        <?php $__currentLoopData = $dispatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $otw      = $d->otw_scene_at;
            $tiba     = $d->pickup_at;
            $selesai  = $d->hospital_at;
            $kembali  = $d->completed_at;
            $assigned = $d->assigned_at;

            $durOtw     = ($otw && $tiba)        ? $tiba->diffInMinutes($otw)        : null;
            $durTkp     = ($tiba && $selesai)     ? $selesai->diffInMinutes($tiba)     : null;
            $durKembali = ($selesai && $kembali)  ? $kembali->diffInMinutes($selesai)  : null;

            $statusMap = [
                'pending'                => 'PENDING',
                'on_the_way_scene'       => 'OTW',
                'on_scene'               => 'DI TKP',
                'on_the_way_kantor_pos'  => 'KEMBALI',
                'completed'              => 'COMPLETED',
            ];
            $statusLabel = $statusMap[$d->status] ?? strtoupper($d->status);
        ?>

        <div class="unit-card">
            <div class="unit-info">
                <strong>Unit <?php echo e($index + 1); ?>:</strong> <?php echo e($d->ambulance?->plate_number ?? 'Unit Damkar'); ?> (<?php echo e($d->ambulance?->code ?? '-'); ?>)<br>
                <strong>Petugas:</strong> <?php echo e($d->driver?->name ?? '-'); ?><br>
                <strong>Waktu Penugasan:</strong> <?php echo e($assigned ? $assigned->format('d-m-Y H:i') : '-'); ?><br>
                <strong>Status Unit:</strong> <?php echo e($statusLabel); ?>

            </div>

            <table class="unit-time-table">
                <thead>
                    <tr>
                        <th class="col-desc">Keterangan Waktu</th>
                        <th class="col-time">Jam</th>
                        <th class="col-dur">Durasi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="col-desc">Berangkat (OTW)</td>
                        <td class="col-time"><?php echo e($otw ? $otw->format('H:i:s') : '-'); ?></td>
                        <td class="col-dur" rowspan="2"><?php echo e($durOtw !== null ? $durOtw . ' mnt' : '-'); ?></td>
                    </tr>
                    <tr>
                        <td class="col-desc">Tiba di TKP</td>
                        <td class="col-time"><?php echo e($tiba ? $tiba->format('H:i:s') : '-'); ?></td>
                    </tr>
                    <tr>
                        <td class="col-desc">Selesai TKP (Kembali)</td>
                        <td class="col-time"><?php echo e($selesai ? $selesai->format('H:i:s') : '-'); ?></td>
                        <td class="col-dur"><?php echo e($durTkp !== null ? $durTkp . ' mnt' : '-'); ?></td>
                    </tr>
                    <tr>
                        <td class="col-desc">Sampai di Mako</td>
                        <td class="col-time"><?php echo e($kembali ? $kembali->format('H:i:s') : '-'); ?></td>
                        <td class="col-dur"><?php echo e($durKembali !== null ? $durKembali . ' mnt' : '-'); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</body>

</html><?php /**PATH /Applications/Dev/damkar-dispatch/resources/views/admin/reports/kebakaran_pdf.blade.php ENDPATH**/ ?>
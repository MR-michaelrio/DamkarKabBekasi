<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kejadian Kebakaran</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            margin: 0;
            padding: 40px;
            color: #000;
            background-color: #f0f0f0;
        }

        .paper {
            background-color: #fff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 20mm;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
            top: 0;
            width: 150px;
            height: auto;
        }

        .header .logo-damkar {
            position: absolute;
            right: 0;
            top: 0;
            width: 190px;
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

        .top-info {
            display: flex;
            justify-content: space-between;
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

        @media print {
            body { margin: 0; padding: 0; background-color: #fff; }
            .paper { box-shadow: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
            <img src="https://damkarkabbekasi.my.id/logo-dinas.png" class="logo-dinas" alt="Logo Dinas">
            <h1>Pemerintah Kabupaten Bekasi</h1>
            <h2>Dinas Pemadam Kebakaran</h2>
            <p>Jalan Teuku Umar No.1 Cikarang Barat</p>
            <p>Desa Ganda Sari Kecamatan Cikarang Barat Kabupaten Bekasi – Jawa Barat</p>
            <p>(021)-89101527</p>
            <div class="bekasi">B E K A S I</div>
            <img src="https://damkarkabbekasi.my.id/logo-damkar.png" class="logo-damkar" alt="Logo Damkar">
        </div>

        <div class="top-info">
            <div class="left-info">
                <table>
                    <tr>
                        <td>Nomor</td>
                        <td>:</td>
                        <td>{{ $nomor ?? '- / /Damkar/2025' }}</td>
                    </tr>
                    <tr>
                        <td>Sifat</td>
                        <td>:</td>
                        <td>{{ $sifat ?? 'Penting' }}</td>
                    </tr>
                    <tr>
                        <td>Hal</td>
                        <td>:</td>
                        <td><span style="font-weight: bold; text-decoration: underline;">Laporan Kejadian Kebakaran</span></td>
                    </tr>
                    <tr>
                        <td>Lampiran</td>
                        <td>:</td>
                        <td>{{ $attachment ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="right-info">
                <table>
                    <tr>
                        <td>{{ $place_date ?? 'Bekasi, ' . date('d F Y') }}</td>
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
                        <td>Di Bekasi</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="content">
            <p class="content-intro">Dengan ini kami laporkan kejadian kebakaran di Wilayah Kabupaten Bekasi, Sebagai Berikut :</p>

            <table class="data-table">
                <tr>
                    <td class="label-col">1. Hari/Tanggal</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $day_date ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">2. Waktu Kejadian</td>
                    <td class="colon-col">:</td>
                </tr>
                <tr>
                    <td style="padding-left: 22px;">Laporan Kejadian</td>
                    <td>:</td>
                    <td>{{ $time_report ?? '-' }} WIB</td>
                </tr>
                <tr>
                    <td style="padding-left: 22px;">Unit Berangkat</td>
                    <td>:</td>
                    <td>{{ $time_departure ?? '-' }} WIB</td>
                </tr>
                <tr>
                    <td style="padding-left: 22px;">Tiba di TKK</td>
                    <td>:</td>
                    <td>{{ $time_arrival ?? '-' }} WIB</td>
                </tr>
                <tr>
                    <td style="padding-left: 22px;">Selesai Penanganan</td>
                    <td>:</td>
                    <td>{{ $time_finished ?? '-' }} WIB</td>
                </tr>
                <tr>
                    <td class="label-col">3. Kronologi</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $chronology ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">4. Lokasi</td>
                    <td class="colon-col"></td>
                </tr>
                <tr>
                    <td style="padding-left: 22px;">Alamat</td>
                    <td>:</td>
                    <td>{{ $address ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 22px;">Kelurahan/Desa</td>
                    <td>:</td>
                    <td>{{ $village ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 22px;">Kecamatan</td>
                    <td>:</td>
                    <td>{{ $district ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">5. Nama Pelapor</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $reporter_name ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 22px;">No. Telp Pelapor</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $reporter_phone ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">6. Nama Ketua RT/RW</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $community_leader_name ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 22px;">No.Telp Ketua RT/RW</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $community_leader_phone ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">7. Luas Areal</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $area_size ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">8. Bangunan yang terbakar</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $building_type ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">9. Nama Pemilik</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $owner_name ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 22px;">Umur</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $owner_age ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 22px;">Telp</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $owner_phone ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 22px;">Pekerjaan</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $owner_occupation ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">10. Asal Api</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $fire_origin ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">11. Pengerahan Unit Mobil</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $unit_count ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">12. No. Seri Kendaraan</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $vehicle_number ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">13. Bantuan Unit Mobil</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">
                        @if(isset($additional_units) && count($additional_units) > 0)
                            @foreach($additional_units as $unit)
                                <div>{{ $unit }}</div>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label-col">14. Penggunaan BA/SCBA</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $scba_usage ?? '0' }} Tabung</td>
                </tr>
                <tr>
                    <td style="padding-left: 29px;">Penggunaan APAR</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $apar_usage ?? '0' }} Tabung</td>
                </tr>
                <tr>
                    <td class="label-col">15. Korban Kebakaran</td>
                    <td class="colon-col"></td>
                </tr>
                <tr>
                    <td style="padding-left: 29px;">Luka - Luka</td>
                    <td>:</td>
                    <td>{{ $injured ?? '0' }} Orang</td>
                </tr>
                <tr>
                    <td style="padding-left: 29px;">Korban Jiwa</td>
                    <td>:</td>
                    <td>{{ $fatalities ?? '0' }} Orang</td>
                </tr>
                <tr>
                    <td style="padding-left: 29px;">Korban Terdampak</td>
                    <td>:</td>
                    <td>{{ $displaced ?? '0' }} Orang</td>
                </tr>
            </table>

            <p class="footer-sentence">Demikian Laporan ini dibuat dengan sebenarnya, Demikian agar maklum.</p>
        </div>

        <table class="signatures">
            <tr>
                <td>
                    <p>Mengetahui</p>
                    <p class="signature-title">KEPALA BIDANG PEMADAM DAN PENYELAMATAN</p>
                    <p class="signature-name">{{ $approver_name ?? 'MULYADI HADI SAPUTRA, SE' }}</p>
                    <p class="signature-rank">{{ $approver_rank ?? 'Pembina – IV/a' }}</p>
                    <p>NIP. {{ $approver_nip ?? '19740410 200311 1 001' }}</p>
                </td>
                <td>
                    <p>&nbsp;</p>
                    <p class="signature-title">KEPALA SEKSI PEMADAM DAN INVESTIGASI</p>
                    <p class="signature-name">{{ $officer_name ?? 'AHMAD FAUZI, ST' }}</p>
                    <p class="signature-rank">{{ $officer_rank ?? 'Penata Tk.I – III/d' }}</p>
                    <p>NIP. {{ $officer_nip ?? '19751104 200901 1 001' }}</p>
                </td>
            </tr>
        </table>
</body>
</html>

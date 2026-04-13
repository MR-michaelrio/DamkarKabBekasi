<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Kejadian Kebakaran</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            margin: 0;
            padding: 20px;
            color: #000;
            background-color: #f0f0f0;
        }

        .paper {
            background-color: #fff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 15mm 20mm;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            border-bottom: 4px double #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
            position: relative;
        }

        .header .logo {
            position: absolute;
            left: 0;
            top: 0;
            width: 100px;
            height: auto;
        }

        .header h1 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
            font-weight: bold;
        }

        .header h2 {
            margin: 0;
            font-size: 18pt;
            text-transform: uppercase;
            font-weight: bold;
        }

        .header p {
            margin: 1px 0;
            font-size: 9pt;
        }

        .header .bekasi-text {
            font-weight: bold;
            letter-spacing: 5px;
            font-size: 14pt;
            margin-top: 2px;
            text-transform: uppercase;
        }

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
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table td {
            vertical-align: top;
            padding: 2px 0;
        }

        .num-col {
            width: 30px;
            text-align: left;
        }

        .label-col {
            width: 280px;
        }

        .colon-col {
            width: 15px;
            text-align: center;
        }

        .value-col {
            /* flex grow */
        }

        .sub-label {
            padding-left: 0px;
        }

        .signature-section {
            width: 100%;
            margin-top: 30px;
        }

        .signature-section td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-space {
            height: 70px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .signature-role {
            font-weight: bold;
            text-transform: uppercase;
        }

        @media print {
            body { margin: 0; padding: 0; background-color: #fff; }
            .paper { box-shadow: none; margin: 0; width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="paper">
        <div class="header">
            <img src="{{ asset('logo-dinas.png') }}" class="logo" alt="Logo">
            <h1>Pemerintah Kabupaten Bekasi</h1>
            <h2>Dinas Pemadam Kebakaran</h2>
            <p>Jalan Teuku Umar No.1 Cikarang Barat</p>
            <p>Desa Ganda Sari Kecamatan Cikarang Barat Kabupaten Bekasi – Jawa Barat</p>
            <p>(021)-89101527</p>
            <div class="bekasi-text">B E K A S I</div>
        </div>

        <div class="ba-container">
            <div class="ba-title">Berita Acara Kejadian Kebakaran</div>
            <div class="ba-number">NOMOR : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; / &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; /DAMKAR 2025</div>
        </div>

        <div class="intro-text">
            Pada Hari ini {{ $hari ?? '..........' }} tanggal {{ $tanggal ?? '...' }} bulan {{ $bulan ?? '..........' }} tahun {{ $tahun ?? '2026' }} Pukul {{ $pukul ?? '... : ...' }} WIB WIB, telah terjadi kebakaran di Wilayah Kabupaten Bekasi, Sebagai Berikut
        </div>

        <table class="data-table">
            <tr>
                <td class="num-col">1.</td>
                <td class="label-col">Hari/Tanggal</td>
                <td class="colon-col">:</td>
                <td class="value-col">{{ $hari_tanggal ?? '-' }}</td>
            </tr>
            <tr>
                <td class="num-col">2.</td>
                <td class="label-col">Waktu Kejadian</td>
                <td class="colon-col">:</td>
                <td class="value-col"></td>
            </tr>
            <tr>
                <td></td>
                <td class="sub-label">Laporan Kejadian</td>
                <td class="colon-col">:</td>
                <td>{{ $waktu_laporan ?? '-' }} WIB</td>
            </tr>
            <tr>
                <td></td>
                <td class="sub-label">Unit Berangkat</td>
                <td class="colon-col">:</td>
                <td>{{ $waktu_berangkat ?? '-' }} WIB</td>
            </tr>
            <tr>
                <td></td>
                <td class="sub-label">Tiba di TKK</td>
                <td class="colon-col">:</td>
                <td>{{ $waktu_tiba ?? '-' }} WIB</td>
            </tr>
            <tr>
                <td></td>
                <td class="sub-label">Selesai Penanganan</td>
                <td class="colon-col">:</td>
                <td>{{ $waktu_selesai ?? '-' }} WIB</td>
            </tr>
            <tr>
                <td class="num-col">3.</td>
                <td class="label-col">Kronologi</td>
                <td class="colon-col">:</td>
                <td class="value-col">{{ $kronologi ?? '-' }}</td>
            </tr>
            <tr>
                <td class="num-col">4.</td>
                <td class="label-col">Lokasi Kebakaran</td>
                <td class="colon-col">:</td>
                <td class="value-col">{{ $lokasi_kebakaran ?? '-' }}</td>
            </tr>
            <tr>
                <td class="num-col">5.</td>
                <td class="label-col">Jenis Bangunan yang terbakar</td>
                <td class="colon-col">:</td>
                <td class="value-col">{{ $jenis_bangunan ?? '-' }}</td>
            </tr>
            <tr>
                <td class="num-col">6.</td>
                <td class="label-col">Penyebab Kebakaran</td>
                <td class="colon-col">:</td>
                <td class="value-col">{{ $penyebab ?? '-' }}</td>
            </tr>
            <tr>
                <td class="num-col">7.</td>
                <td class="label-col">Luas Area</td>
                <td class="colon-col">:</td>
                <td class="value-col">{{ $luas_area ?? '-' }}</td>
            </tr>
            <tr>
                <td class="num-col">8.</td>
                <td class="label-col">Nama Pemilik</td>
                <td class="colon-col">:</td>
                <td class="value-col">{{ $nama_pemilik ?? '-' }}</td>
            </tr>
            <tr>
                <td class="num-col">9.</td>
                <td class="label-col">Umur</td>
                <td class="colon-col">:</td>
                <td class="value-col">{{ $umur_pemilik ?? '-' }}</td>
            </tr>
            <tr>
                <td class="num-col">10.</td>
                <td class="label-col">Pekerjaan</td>
                <td class="colon-col">:</td>
                <td class="value-col">{{ $pekerjaan_pemilik ?? '-' }}</td>
            </tr>
            <tr>
                <td class="num-col">11.</td>
                <td class="label-col">Lokasi</td>
                <td class="colon-col">:</td>
                <td class="value-col"></td>
            </tr>
            <tr>
                <td></td>
                <td class="sub-label">Alamat</td>
                <td class="colon-col">:</td>
                <td>{{ $alamat ?? '-' }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="sub-label">Kelurahan/Desa</td>
                <td class="colon-col">:</td>
                <td>{{ $kelurahan ?? '-' }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="sub-label">Kecamatan</td>
                <td class="colon-col">:</td>
                <td>{{ $kecamatan ?? '-' }}</td>
            </tr>
            <tr>
                <td class="num-col">12.</td>
                <td class="label-col">Nama Pelapor</td>
                <td class="colon-col">:</td>
                <td class="value-col">{{ $nama_pelapor ?? '-' }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="sub-label">No.Telp Pelapor</td>
                <td class="colon-col">:</td>
                <td>{{ $telp_pelapor ?? '-' }}</td>
            </tr>
            <tr>
                <td class="num-col">13.</td>
                <td class="label-col">Nama Ketua RT/RW</td>
                <td class="colon-col">:</td>
                <td class="value-col">{{ $nama_rt_rw ?? '-' }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="sub-label">No.Telp Ketua RT/RW</td>
                <td class="colon-col">:</td>
                <td>{{ $telp_rt_rw ?? '-' }}</td>
            </tr>
            <tr>
                <td class="num-col">14.</td>
                <td class="label-col">Unit Mobil Pemadam yang dikerahkan</td>
                <td class="colon-col">:</td>
                <td class="value-col">{{ $jumlah_unit ?? '-' }} Unit</td>
            </tr>
            <tr>
                <td class="num-col">15.</td>
                <td class="label-col">No. Seri Kendaraan</td>
                <td class="colon-col">:</td>
                <td class="value-col">{{ $no_seri_kendaraan ?? '-' }}</td>
            </tr>
            <tr>
                <td class="num-col">16.</td>
                <td class="label-col">Bantuan Unit Mobil Pemadam</td>
                <td class="colon-col">:</td>
                <td class="value-col">
                    @if(isset($bantuan_unit) && count($bantuan_unit) > 0)
                        @foreach($bantuan_unit as $unit)
                            <div>{{ $unit }}</div>
                        @endforeach
                    @else
                        <div>Dari ..........</div>
                        <div>Dari ..........</div>
                        <div>Dari ..........</div>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="num-col">17.</td>
                <td class="label-col">Penggunaan BA/SCBA</td>
                <td class="colon-col">:</td>
                <td class="value-col">- {{ $scba_usage ?? '...' }} Tabung</td>
            </tr>
            <tr>
                <td></td>
                <td class="sub-label">Penggunaan APAR</td>
                <td class="colon-col">:</td>
                <td>- {{ $apar_usage ?? '...' }} Tabung</td>
            </tr>
            <tr>
                <td class="num-col">18.</td>
                <td class="label-col">Korban Kebakaran</td>
                <td class="colon-col">:</td>
                <td class="value-col"></td>
            </tr>
            <tr>
                <td></td>
                <td class="sub-label">Luka – Luka</td>
                <td class="colon-col">:</td>
                <td>{{ $korban_luka ?? '...' }} Orang</td>
            </tr>
            <tr>
                <td></td>
                <td class="sub-label">Korban Jiwa</td>
                <td class="colon-col">:</td>
                <td>{{ $korban_jiwa ?? '...' }} Orang</td>
            </tr>
            <tr>
                <td></td>
                <td class="sub-label">Korban Terdampak</td>
                <td class="colon-col">:</td>
                <td>{{ $korban_terdampak ?? '...' }} Orang</td>
            </tr>
        </table>

        <div class="intro-text">
            Demikian Berita Acara Kejadian Kebakaran ini dibuat dengan sebenarnya, Demikian agar maklum.
        </div>

        <table class="signature-section">
            <tr>
                <td></td>
                <td>
                    Bekasi, {{ $tanggal_laporan ?? '10 Januari 2026' }}
                </td>
            </tr>
            <tr>
                <td>
                    <div class="signature-role">KOMANDAN REGU</div>
                    <div class="signature-space"></div>
                    <div class="signature-name">{{ $komandan_regu ?? 'SUKARDI YUSUF' }}</div>
                    <div>NIP. {{ $nip_danru ?? '198709212025211001' }}</div>
                </td>
                <td>
                    <div class="signature-role">KOMANDAN PELETON III</div>
                    <div class="signature-space"></div>
                    <div class="signature-name">{{ $komandan_peleton ?? 'JAJANG SUSANTO, SI.P' }}</div>
                    <div>NIP. {{ $nip_danton ?? '19682007101103' }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

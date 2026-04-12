<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Kejadian Kebakaran - Damkar Kabupaten Bekasi</title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm 1.8cm 1.8cm 1.8cm;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #000;
        }

        /* ════════════════════════════════
           KOP SURAT
        ════════════════════════════════ */
        .kop-wrap {
            width: 100%;
            border-collapse: collapse;
        }
        .kop-wrap td {
            vertical-align: middle;
            padding: 0;
        }
        .kop-logo {
            width: 72px;
            text-align: center;
        }
        .kop-logo img {
            width: 68px;
            height: 68px;
        }
        .kop-text {
            text-align: center;
            padding: 0 6px;
        }
        .kop-text .t1 { font-size: 10pt; font-weight: normal; }
        .kop-text .t2 { font-size: 18pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .kop-text .t3 { font-size: 8.5pt; margin-top: 2px; }
        .kop-text .t4 { font-size: 9pt; font-weight: bold; letter-spacing: 5px; margin-top: 3px; }
        .kop-spacer { width: 72px; }

        .line-thick { border-top: 3px solid #000; margin-top: 6px; }
        .line-thin  { border-top: 1px solid #000; margin-top: 2px; }

        /* ════════════════════════════════
           LAYOUT SURAT (kiri-kanan)
        ════════════════════════════════ */
        .surat-wrap {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        .surat-wrap td { vertical-align: top; font-size: 10pt; }
        .surat-kiri { width: 48%; padding-right: 8px; }
        .surat-kanan { width: 52%; line-height: 1.7; }

        .meta-tbl { border-collapse: collapse; width: 100%; }
        .meta-tbl td { font-size: 10pt; vertical-align: top; padding: 2px 0; }
        .meta-label { width: 72px; white-space: nowrap; }
        .meta-sep   { width: 10px; }

        /* ════════════════════════════════
           TABEL ISI LAPORAN
        ════════════════════════════════ */
        .isi-tbl {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        .isi-tbl td {
            font-size: 10pt;
            vertical-align: top;
            padding: 2px 2px;
        }
        .col-no     { width: 26px; }
        .col-label  { width: 42%; }
        .col-sep    { width: 10px; }
        .col-val    { }

        .sub-label  { padding-left: 18px !important; font-size: 10pt; }

        /* ════════════════════════════════
           TANDA TANGAN
        ════════════════════════════════ */
        .ttd-tbl {
            width: 100%;
            border-collapse: collapse;
            margin-top: 28px;
        }
        .ttd-tbl td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 10pt;
            padding: 0 6px;
        }
        .ttd-role  { font-weight: bold; font-size: 9.5pt; text-transform: uppercase; line-height: 1.4; }
        .ttd-space { height: 52px; }
        .ttd-name  { font-weight: bold; text-decoration: underline; font-size: 10pt; }
        .ttd-rank  { font-size: 9pt; }
        .ttd-nip   { font-size: 9pt; }

        /* ════════════════════════════════
           BERITA ACARA
        ════════════════════════════════ */
        .ba-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            text-decoration: underline;
            text-transform: uppercase;
            margin-top: 14px;
            margin-bottom: 2px;
        }
        .ba-nomor {
            text-align: center;
            font-size: 10pt;
            margin-bottom: 10px;
        }
        .ba-opening {
            font-size: 10pt;
            text-align: justify;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        /* ════════════════════════════════
           FOTO
        ════════════════════════════════ */
        .foto-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            text-decoration: underline;
            text-transform: uppercase;
            margin-top: 14px;
            margin-bottom: 12px;
        }
        .foto-tbl { width: 100%; border-collapse: collapse; }
        .foto-cell {
            width: 50%;
            padding: 6px 8px;
            vertical-align: top;
            text-align: center;
        }
        .foto-box {
            border: 1px solid #bbb;
            background: #f7f7f7;
            padding: 5px;
        }
        .foto-box img {
            max-width: 100%;
            max-height: 175px;
            display: block;
            margin: 0 auto;
        }
        .foto-num     { font-size: 9.5pt; font-weight: bold; margin-bottom: 4px; }
        .foto-caption { font-size: 8.5pt; color: #444; margin-top: 5px; line-height: 1.4; }
        .foto-empty   { text-align: center; font-size: 10pt; color: #666; padding: 40px 0; }

        /* ════════════════════════════════
           UNIT ARMADA
        ════════════════════════════════ */
        .armada-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            text-decoration: underline;
            text-transform: uppercase;
            margin-top: 14px;
            margin-bottom: 14px;
        }
        .unit-card {
            border: 1px solid #bbb;
            background: #fafafa;
            padding: 10px 12px;
            margin-bottom: 14px;
            page-break-inside: avoid;
        }
        .unit-card-title {
            font-weight: bold;
            font-size: 10.5pt;
            margin-bottom: 6px;
        }
        .unit-info-tbl { border-collapse: collapse; width: auto; }
        .unit-info-tbl td {
            font-size: 10pt;
            vertical-align: top;
            padding: 1.5px 2px;
        }
        .unit-info-label { width: 140px; }

        .waktu-tbl {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        .waktu-tbl th,
        .waktu-tbl td {
            border: 1px solid #aaa;
            padding: 4px 8px;
            font-size: 9.5pt;
        }
        .waktu-tbl th {
            background-color: #e8e8e8;
            font-weight: bold;
            text-align: center;
        }
        .waktu-tbl td:nth-child(2) { text-align: center; }
        .waktu-tbl td:nth-child(3) { text-align: center; }

        /* ════════════════════════════════
           UTILITAS
        ════════════════════════════════ */
        .pagebreak   { page-break-after: always; }
        .avoid-break { page-break-inside: avoid; }
        .text-center { text-align: center; }
        .muted       { color: #666; }

        /* opening paragraph */
        .opening-text {
            font-size: 10pt;
            margin-top: 12px;
            margin-bottom: 6px;
        }
        .closing-text {
            font-size: 10pt;
            margin-top: 12px;
        }
    </style>
</head>
<body>

@php
use Carbon\Carbon;
Carbon::setLocale('id');

$logoPath = 'file://' . public_path('dinas-logo.jpg');

/* ── Tanggal & Waktu ─────────────────────────────────── */
$reqDate     = $incident['request_date']
    ? (is_string($incident['request_date']) ? Carbon::parse($incident['request_date']) : $incident['request_date'])
    : now();

$dayName     = $reqDate->translatedFormat('l');
$dateStr     = $reqDate->translatedFormat('l, j F Y');
$dateOnly    = $reqDate->translatedFormat('j F Y');
$tahun       = $reqDate->year;

$otwAt       = $incident['otw_at']       ? Carbon::parse($incident['otw_at'])       : null;
$arriveAt    = $incident['arrive_at']    ? Carbon::parse($incident['arrive_at'])    : null;
$handledAt   = $incident['handled_at']   ? Carbon::parse($incident['handled_at'])   : null;
$completedAt = $incident['completed_at'] ? Carbon::parse($incident['completed_at']) : null;

$pickupTime      = $incident['pickup_time'] ?? null;
$lapKejadian     = $pickupTime ? substr($pickupTime, 0, 5) : '-';
$otwDisplay      = $otwAt      ? $otwAt->format('H:i')    : '-';
$arriveDisplay   = $arriveAt   ? $arriveAt->format('H:i') : '-';
$handledDisplay  = $handledAt  ? $handledAt->format('H:i'): '-';

/* ── Helper durasi ──────────────────────────────────── */
$dur = fn($a, $b) => ($a && $b) ? number_format(abs($a->diffInSeconds($b)) / 60, 2) : '-';

/* ── Lokasi & Pelapor ───────────────────────────────── */
$address       = $incident['address']       ?? '-';
$kelurahan     = $incident['kelurahan']     ?? '-';
$kecamatan     = $incident['kecamatan']     ?? '-';
$reporterName  = $incident['reporter_name'] ?? '-';
$reporterPhone = $incident['reporter_phone']?? '-';

/* ── Unit ───────────────────────────────────────────── */
$unitCount   = $incident['unit_count']   ?? 1;
$plateNumber = $incident['plate_number'] ?? '-';
$otherPlates = $incident['other_plates'] ?? collect();

/* ── Nomor & Jenis ──────────────────────────────────── */
$nomorSurat  = $incident['nomor'] ?? '___';
$suratDate   = now()->translatedFormat('l, j F Y');
$kondisi     = strtoupper(str_replace('_', ' ', $incident['condition'] ?? 'KEBAKARAN'));
$penyebab    = ($incident['condition'] ?? '') === 'kebakaran'
    ? 'Listrik / Alam / Human Error'
    : ucfirst(str_replace('_', ' ', $incident['condition'] ?? '-'));

$wilayah = 'Kabupaten Bekasi';
if ($kecamatan && $kecamatan !== '-') {
    $wilayah = 'Kecamatan ' . $kecamatan . ', Kabupaten Bekasi';
}
@endphp


{{-- ══════════════════════════════════════════════════════════
     HALAMAN 1 — LAPORAN KEJADIAN KEBAKARAN
══════════════════════════════════════════════════════════ --}}

{{-- KOP --}}
@include('admin.reports.kop', ['logoPath' => $logoPath])

{{-- HEADER SURAT --}}
<table class="surat-wrap">
    <tr>
        <td class="surat-kiri">
            <table class="meta-tbl">
                <tr>
                    <td class="meta-label">Nomor</td>
                    <td class="meta-sep">:</td>
                    <td>{{ $nomorSurat }} &nbsp;/&nbsp;&nbsp;/Damkar/{{ $tahun }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Sifat</td>
                    <td class="meta-sep">:</td>
                    <td>Penting</td>
                </tr>
                <tr>
                    <td class="meta-label">Hal</td>
                    <td class="meta-sep">:</td>
                    <td><strong><u>Laporan Kejadian Kebakaran</u></strong></td>
                </tr>
                <tr>
                    <td class="meta-label">Lampiran</td>
                    <td class="meta-sep">:</td>
                    <td>-</td>
                </tr>
            </table>
        </td>
        <td class="surat-kanan">
            Bekasi, {{ $suratDate }}<br><br>
            Kepada<br>
            Yth. Kepala Dinas Pemadam Kebakaran<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kabupaten Bekasi<br>
            Di-<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bekasi
        </td>
    </tr>
</table>

<p class="opening-text">
    Dengan ini kami laporkan kejadian kebakaran di Wilayah Kabupaten Bekasi, Sebagai Berikut :
</p>

{{-- ISI LAPORAN --}}
<table class="isi-tbl">
    {{-- 1 --}}
    <tr>
        <td class="col-no">1.</td>
        <td class="col-label">Hari/Tanggal</td>
        <td class="col-sep">:</td>
        <td class="col-val">{{ $dateStr }}</td>
    </tr>
    {{-- 2 Waktu --}}
    <tr>
        <td class="col-no">2.</td>
        <td class="col-label">Waktu Kejadian</td>
        <td class="col-sep">:</td>
        <td></td>
    </tr>
    <tr><td></td><td class="sub-label">Laporan Kejadian</td><td class="col-sep">:</td><td>{{ $lapKejadian }} WIB</td></tr>
    <tr><td></td><td class="sub-label">Unit Berangkat</td>  <td class="col-sep">:</td><td>{{ $otwDisplay }} WIB</td></tr>
    <tr><td></td><td class="sub-label">Tiba di TKK</td>     <td class="col-sep">:</td><td>{{ $arriveDisplay }} WIB</td></tr>
    <tr><td></td><td class="sub-label">Selesai Penanganan</td><td class="col-sep">:</td><td>{{ $handledDisplay }} WIB</td></tr>
    {{-- 3 --}}
    <tr>
        <td class="col-no">3.</td>
        <td class="col-label">Kronologi</td>
        <td class="col-sep">:</td>
        <td class="col-val">Laporan dari {{ $reporterName }}; kejadian {{ strtolower($kondisi) }} di {{ $address }}</td>
    </tr>
    {{-- 4 Lokasi --}}
    <tr>
        <td class="col-no">4.</td>
        <td class="col-label">Lokasi</td>
        <td class="col-sep">:</td>
        <td></td>
    </tr>
    <tr><td></td><td class="sub-label">Alamat</td>         <td class="col-sep">:</td><td>{{ $address }}</td></tr>
    <tr><td></td><td class="sub-label">Kelurahan/Desa</td> <td class="col-sep">:</td><td>{{ $kelurahan }}</td></tr>
    <tr><td></td><td class="sub-label">Kecamatan</td>      <td class="col-sep">:</td><td>{{ $kecamatan }}</td></tr>
    {{-- 5 --}}
    <tr>
        <td class="col-no">5.</td>
        <td class="col-label">Nama Pelapor</td>
        <td class="col-sep">:</td>
        <td>{{ $reporterName }}</td>
    </tr>
    <tr><td></td><td class="sub-label">No. Telp Pelapor</td><td class="col-sep">:</td><td>{{ $reporterPhone }}</td></tr>
    {{-- 6 --}}
    <tr>
        <td class="col-no">6.</td>
        <td class="col-label">Nama Ketua RT/RW</td>
        <td class="col-sep">:</td>
        <td>-</td>
    </tr>
    <tr><td></td><td class="sub-label">No.Telp Ketua RT/RW</td><td class="col-sep">:</td><td>-</td></tr>
    {{-- 7 --}}
    <tr>
        <td class="col-no">7.</td>
        <td class="col-label">Luas Areal</td>
        <td class="col-sep">:</td>
        <td>{{ $incident['luas_areal'] ?? '-' }} m&sup2;</td>
    </tr>
    {{-- 8 --}}
    <tr>
        <td class="col-no">8.</td>
        <td class="col-label">Bangunan yang terbakar</td>
        <td class="col-sep">:</td>
        <td>{{ ucfirst(strtolower($kondisi)) }}</td>
    </tr>
    {{-- 9 Pemilik --}}
    <tr>
        <td class="col-no">9.</td>
        <td class="col-label">Nama Pemilik</td>
        <td class="col-sep">:</td>
        <td>{{ $incident['owner_name'] ?? '-' }}</td>
    </tr>
    <tr><td></td><td class="sub-label">Umur</td>    <td class="col-sep">:</td><td>{{ $incident['owner_age']  ?? '-' }}</td></tr>
    <tr><td></td><td class="sub-label">Telp</td>    <td class="col-sep">:</td><td>{{ $incident['owner_phone']?? '-' }}</td></tr>
    <tr><td></td><td class="sub-label">Pekerjaan</td><td class="col-sep">:</td><td>{{ $incident['owner_job'] ?? '-' }}</td></tr>
    {{-- 10 --}}
    <tr>
        <td class="col-no">10.</td>
        <td class="col-label">Asal Api</td>
        <td class="col-sep">:</td>
        <td>{{ $penyebab }}</td>
    </tr>
    {{-- 11 --}}
    <tr>
        <td class="col-no">11.</td>
        <td class="col-label">Pengerahan Unit Mobil Pemadam</td>
        <td class="col-sep">:</td>
        <td>{{ $unitCount }} Unit</td>
    </tr>
    {{-- 12 --}}
    <tr>
        <td class="col-no">12.</td>
        <td class="col-label">No. Seri Kendaraan</td>
        <td class="col-sep">:</td>
        <td>- {{ $plateNumber }}</td>
    </tr>
    {{-- 13 --}}
    <tr>
        <td class="col-no">13.</td>
        <td class="col-label">Bantuan Unit Mobil Pemadam</td>
        <td class="col-sep">:</td>
        <td>
            @forelse($otherPlates as $p)
                Dari: {{ $p }}<br>
            @empty
                Dari: -
            @endforelse
        </td>
    </tr>
    {{-- 14 --}}
    <tr>
        <td class="col-no">14.</td>
        <td class="col-label">Penggunaan BA/SCBA</td>
        <td class="col-sep">:</td>
        <td>{{ $incident['scba'] ?? '-' }} Tabung</td>
    </tr>
    <tr><td></td><td class="sub-label">Penggunaan APAR</td><td class="col-sep">:</td><td>{{ $incident['apar'] ?? '-' }} Tabung</td></tr>
    {{-- 15 Korban --}}
    <tr>
        <td class="col-no">15.</td>
        <td class="col-label">Korban Kebakaran</td>
        <td class="col-sep">:</td>
        <td></td>
    </tr>
    <tr><td></td><td class="sub-label">Luka &ndash; Luka</td>  <td class="col-sep">:</td><td>{{ $incident['korban_luka']     ?? '-' }} Orang</td></tr>
    <tr><td></td><td class="sub-label">Korban Jiwa</td>         <td class="col-sep">:</td><td>{{ $incident['korban_jiwa']     ?? '-' }} Orang</td></tr>
    <tr><td></td><td class="sub-label">Korban Terdampak</td>    <td class="col-sep">:</td><td>{{ $incident['korban_terdampak']?? '-' }} Orang</td></tr>
</table>

<p class="closing-text">Demikian Laporan ini dibuat dengan sebenarnya, Demikian agar maklum.</p>

{{-- TANDA TANGAN HALAMAN 1 --}}
<p style="text-align:center; font-size:10pt; margin-top:16px;">Mengetahui</p>
<table class="ttd-tbl">
    <tr>
        <td>
            <div class="ttd-role">KEPALA BIDANG PEMADAMAN DAN<br>PENYELAMATAN</div>
            <div class="ttd-space"></div>
            <div class="ttd-name">MULYADI HADI SAPUTRA, SE</div>
            <div class="ttd-rank">Pembina &ndash; IV/a</div>
            <div class="ttd-nip">NIP. 19740410 200311 1 001</div>
        </td>
        <td>
            <div class="ttd-role">KEPALA SEKSI PEMADAMAN DAN<br>INVESTIGASI</div>
            <div class="ttd-space"></div>
            <div class="ttd-name">AHMAD FAUZI, ST</div>
            <div class="ttd-rank">Penata Tk.I &ndash; III/d</div>
            <div class="ttd-nip">NIP. 19751104 200901 1 001</div>
        </td>
    </tr>
</table>

<div class="pagebreak"></div>


{{-- ══════════════════════════════════════════════════════════
     HALAMAN 2 — BERITA ACARA KEJADIAN KEBAKARAN
══════════════════════════════════════════════════════════ --}}

@include('pdf.partials.kop', ['logoPath' => $logoPath])

<p class="ba-title">BERITA ACARA KEJADIAN KEBAKARAN</p>
<p class="ba-nomor">NOMOR : &nbsp; / &nbsp; /DAMKAR {{ $tahun }}</p>

<p class="ba-opening">
    Pada Hari ini <strong>{{ $dayName }}</strong> tanggal
    <strong>{{ $reqDate->format('j') }}</strong> bulan
    <strong>{{ $reqDate->translatedFormat('F') }}</strong> tahun
    <strong>{{ $tahun }}</strong> Pukul <strong>{{ $lapKejadian }} WIB</strong>,
    telah terjadi kebakaran di Wilayah <strong>{{ $wilayah }}</strong>,
    Sebagai berikut :
</p>

<table class="isi-tbl">
    <tr><td class="col-no">1.</td><td class="col-label">Hari/Tanggal</td><td class="col-sep">:</td><td>{{ $dateStr }}</td></tr>
    <tr><td class="col-no">2.</td><td class="col-label">Waktu Kejadian</td><td class="col-sep">:</td><td></td></tr>
    <tr><td></td><td class="sub-label">Laporan Kejadian</td> <td class="col-sep">:</td><td>{{ $lapKejadian }} WIB</td></tr>
    <tr><td></td><td class="sub-label">Unit Berangkat</td>   <td class="col-sep">:</td><td>{{ $otwDisplay }} WIB</td></tr>
    <tr><td></td><td class="sub-label">Tiba di TKK</td>      <td class="col-sep">:</td><td>{{ $arriveDisplay }} WIB</td></tr>
    <tr><td></td><td class="sub-label">Selesai Penanganan</td><td class="col-sep">:</td><td>{{ $handledDisplay }} WIB</td></tr>
    <tr>
        <td class="col-no">3.</td>
        <td class="col-label">Kronologi</td>
        <td class="col-sep">:</td>
        <td>Laporan dari {{ $reporterName }}; kejadian {{ strtolower($kondisi) }} di {{ $address }}</td>
    </tr>
    <tr><td class="col-no">4.</td> <td class="col-label">Lokasi Kebakaran</td>          <td class="col-sep">:</td><td>{{ $address }}</td></tr>
    <tr><td class="col-no">5.</td> <td class="col-label">Jenis Bangunan yang terbakar</td><td class="col-sep">:</td><td>{{ ucfirst(strtolower($kondisi)) }}</td></tr>
    <tr><td class="col-no">6.</td> <td class="col-label">Penyebab Kebakaran</td>         <td class="col-sep">:</td><td>{{ $penyebab }}</td></tr>
    <tr><td class="col-no">7.</td> <td class="col-label">Luas Area</td>                  <td class="col-sep">:</td><td>{{ $incident['luas_areal'] ?? '-' }} m&sup2;</td></tr>
    <tr><td class="col-no">8.</td> <td class="col-label">Nama Pemilik</td>               <td class="col-sep">:</td><td>{{ $incident['owner_name'] ?? '-' }}</td></tr>
    <tr><td class="col-no">9.</td> <td class="col-label">Umur</td>                       <td class="col-sep">:</td><td>{{ $incident['owner_age']  ?? '-' }}</td></tr>
    <tr><td class="col-no">10.</td><td class="col-label">Pekerjaan</td>                  <td class="col-sep">:</td><td>{{ $incident['owner_job']  ?? '-' }}</td></tr>
    <tr><td class="col-no">11.</td><td class="col-label">Lokasi</td>                     <td class="col-sep">:</td><td></td></tr>
    <tr><td></td><td class="sub-label">Alamat</td>        <td class="col-sep">:</td><td>{{ $address }}</td></tr>
    <tr><td></td><td class="sub-label">Kelurahan/Desa</td><td class="col-sep">:</td><td>{{ $kelurahan }}</td></tr>
    <tr><td></td><td class="sub-label">Kecamatan</td>     <td class="col-sep">:</td><td>{{ $kecamatan }}</td></tr>
    <tr><td class="col-no">12.</td><td class="col-label">Nama Pelapor</td>               <td class="col-sep">:</td><td>{{ $reporterName }}</td></tr>
    <tr><td></td><td class="sub-label">No.Telp Pelapor</td><td class="col-sep">:</td><td>{{ $reporterPhone }}</td></tr>
    <tr><td class="col-no">13.</td><td class="col-label">Nama Ketua RT/RW</td>           <td class="col-sep">:</td><td>-</td></tr>
    <tr><td></td><td class="sub-label">No.Telp Ketua RT/RW</td><td class="col-sep">:</td><td>-</td></tr>
    <tr><td class="col-no">14.</td><td class="col-label">Unit Mobil Pemadam yang dikerahkan</td><td class="col-sep">:</td><td>{{ $unitCount }} Unit</td></tr>
    <tr><td class="col-no">15.</td><td class="col-label">No. Seri Kendaraan</td>         <td class="col-sep">:</td><td>- {{ $plateNumber }}</td></tr>
    <tr>
        <td class="col-no">16.</td>
        <td class="col-label">Bantuan Unit Mobil Pemadam</td>
        <td class="col-sep">:</td>
        <td>
            @forelse($otherPlates as $p)
                Dari: {{ $p }}<br>
            @empty
                Dari: -
            @endforelse
        </td>
    </tr>
    <tr><td class="col-no">17.</td><td class="col-label">Penggunaan BA/SCBA</td>         <td class="col-sep">:</td><td>{{ $incident['scba'] ?? '-' }} Tabung</td></tr>
    <tr><td></td><td class="sub-label">Penggunaan APAR</td><td class="col-sep">:</td><td>{{ $incident['apar'] ?? '-' }} Tabung</td></tr>
    <tr><td class="col-no">18.</td><td class="col-label">Korban Kebakaran</td>           <td class="col-sep">:</td><td></td></tr>
    <tr><td></td><td class="sub-label">Luka &ndash; Luka</td><td class="col-sep">:</td><td>{{ $incident['korban_luka']     ?? '-' }} Orang</td></tr>
    <tr><td></td><td class="sub-label">Korban Jiwa</td>      <td class="col-sep">:</td><td>{{ $incident['korban_jiwa']     ?? '-' }} Orang</td></tr>
    <tr><td></td><td class="sub-label">Korban Terdampak</td> <td class="col-sep">:</td><td>{{ $incident['korban_terdampak']?? '-' }} Orang</td></tr>
</table>

<p class="closing-text">
    Demikian Berita Acara Kejadian Kebakaran ini dibuat dengan sebenarnya, Demikian agar maklum.
</p>

<table width="100%" style="border-collapse:collapse; font-size:10pt; margin-top:10px;">
    <tr>
        <td width="50%"></td>
        <td width="50%">Bekasi, {{ $dateOnly }}</td>
    </tr>
</table>
<table class="ttd-tbl">
    <tr>
        <td>
            <div class="ttd-role">KOMANDAN REGU</div>
            <div class="ttd-space"></div>
            <div class="ttd-name">SUKARDI YUSUF</div>
            <div class="ttd-nip">NIP. 198709212025211001</div>
        </td>
        <td>
            <div class="ttd-role">KOMANDAN PELETON III</div>
            <div class="ttd-space"></div>
            <div class="ttd-name">JAJANG SUSANTO, S.I.P</div>
            <div class="ttd-nip">NIP. 19682007101103</div>
        </td>
    </tr>
</table>

<div class="pagebreak"></div>


{{-- ══════════════════════════════════════════════════════════
     HALAMAN 3 — DOKUMENTASI FOTO LAPANGAN
══════════════════════════════════════════════════════════ --}}

@include('pdf.partials.kop', ['logoPath' => $logoPath])

<p class="foto-title">DOKUMENTASI FOTO LAPANGAN</p>

@if($photos->isEmpty())
    <div class="foto-empty">
        <p>Tidak ada dokumentasi foto tersedia.</p>
    </div>
@else
    @php $chunks = $photos->chunk(6); @endphp
    @foreach($chunks as $chunkIdx => $chunk)
        @if($chunkIdx > 0)
            <div class="pagebreak"></div>
            @include('pdf.partials.kop', ['logoPath' => $logoPath])
            <p class="foto-title">DOKUMENTASI FOTO LAPANGAN <span style="font-size:10pt;">(Lanjutan)</span></p>
        @endif

        <table class="foto-tbl">
            @foreach($chunk->chunk(2) as $rowIdx => $pair)
            <tr>
                @foreach($pair as $colIdx => $photoObj)
                @php
                    $num    = ($chunkIdx * 6) + ($rowIdx * 2) + $colIdx + 1;
                    $src    = 'file://' . storage_path('app/public/' . $photoObj->photo->photo_path);
                    $time   = $photoObj->photo->created_at
                        ? Carbon::parse($photoObj->photo->created_at)->format('d/m/Y H:i')
                        : '-';
                @endphp
                <td class="foto-cell">
                    <p class="foto-num">Foto {{ $num }}</p>
                    <div class="foto-box avoid-break">
                        <img src="{{ $src }}" alt="Foto {{ $num }}">
                    </div>
                    <p class="foto-caption">
                        Petugas: <strong>{{ $photoObj->uploader }}</strong><br>
                        Waktu: {{ $time }}
                    </p>
                </td>
                @endforeach
                @if($pair->count() < 2)
                    <td class="foto-cell"></td>
                @endif
            </tr>
            @endforeach
        </table>
    @endforeach
@endif

<div class="pagebreak"></div>


{{-- ══════════════════════════════════════════════════════════
     HALAMAN 4 — UNIT ARMADA YANG DITUGASKAN
══════════════════════════════════════════════════════════ --}}

@include('pdf.partials.kop', ['logoPath' => $logoPath])

<p class="armada-title">UNIT ARMADA YANG DITUGASKAN</p>

@forelse($dispatches as $dIdx => $d)
@php
    $dOtw       = $d->otw_scene_at  ? Carbon::parse($d->otw_scene_at)  : null;
    $dArrive    = $d->pickup_at     ? Carbon::parse($d->pickup_at)     : null;
    $dHandled   = $d->hospital_at   ? Carbon::parse($d->hospital_at)   : null;
    $dCompleted = $d->completed_at  ? Carbon::parse($d->completed_at)  : null;

    $dRespon  = $dur($dOtw, $dArrive);
    $dTkp     = $dur($dArrive, $dHandled);
    $dPulang  = $dur($dHandled, $dCompleted);
@endphp

<div class="unit-card">
    <div class="unit-card-title">
        Unit {{ $dIdx + 1 }}: {{ $d->ambulance?->plate_number ?? '(-)' }}
        @if($d->ambulance?->code) &nbsp;&mdash;&nbsp; {{ $d->ambulance->code }} @endif
    </div>

    <table class="unit-info-tbl">
        <tr>
            <td class="unit-info-label">Petugas</td>
            <td style="width:10px;">:</td>
            <td>{{ $d->driver?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="unit-info-label">Waktu Penugasan</td>
            <td>:</td>
            <td>{{ $d->assigned_at ? Carbon::parse($d->assigned_at)->format('d-m-Y H:i') : '-' }}</td>
        </tr>
        <tr>
            <td class="unit-info-label">Status Unit</td>
            <td>:</td>
            <td>{{ strtoupper(str_replace('_', ' ', $d->status)) }}</td>
        </tr>
    </table>

    <table class="waktu-tbl">
        <thead>
            <tr>
                <th style="text-align:left; width:35%;">Keterangan Waktu</th>
                <th style="width:20%;">Jam</th>
                <th style="width:45%;">Durasi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Berangkat (OTW)</td>
                <td>{{ $dOtw     ? $dOtw->format('H:i:s')     : '-' }}</td>
                <td rowspan="2" style="vertical-align:middle; text-align:center;">
                    @if($dRespon !== '-')
                        {{ $dRespon }} Menit <em>(Respon Time)</em>
                    @else &mdash; @endif
                </td>
            </tr>
            <tr>
                <td>Tiba di TKP</td>
                <td>{{ $dArrive  ? $dArrive->format('H:i:s')  : '-' }}</td>
            </tr>
            <tr>
                <td>Selesai TKP (Kembali)</td>
                <td>{{ $dHandled ? $dHandled->format('H:i:s') : '-' }}</td>
                <td style="text-align:center;">
                    @if($dTkp !== '-')
                        {{ $dTkp }} Menit <em>(Di TKP)</em>
                    @else &mdash; @endif
                </td>
            </tr>
            <tr>
                <td>Sampai di Mako</td>
                <td>{{ $dCompleted ? $dCompleted->format('H:i:s') : '-' }}</td>
                <td style="text-align:center;">
                    @if($dPulang !== '-')
                        {{ $dPulang }} Menit <em>(Perjalanan Pulang)</em>
                    @else &mdash; @endif
                </td>
            </tr>
        </tbody>
    </table>
</div>

@empty
    <p class="text-center muted" style="padding:24px 0;">Tidak ada data unit armada.</p>
@endforelse

</body>
</html>
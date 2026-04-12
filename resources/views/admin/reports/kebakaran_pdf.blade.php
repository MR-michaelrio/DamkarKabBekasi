<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Kejadian Kebakaran - Damkar Kabupaten Bekasi</title>
    <style>
        @page { margin: 1.4cm 1.5cm 1.8cm 1.5cm; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.45;
            color: #000;
        }

        /* ── KOP SURAT ── */
        .kop-table { width: 100%; border-collapse: collapse; }
        .kop-table td { vertical-align: middle; padding: 0 4px; }
        .kop-logo-cell { width: 70px; text-align: center; }
        .kop-logo-cell img { width: 62px; height: 62px; }
        .kop-text-cell { text-align: center; }
        .kop-pemerintah { font-size: 10px; font-weight: normal; letter-spacing: 0.5px; }
        .kop-dinas { font-size: 17px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .kop-addr { font-size: 8.5px; margin-top: 2px; }
        .kop-kota { font-size: 12px; font-weight: bold; letter-spacing: 6px; margin-top: 3px; }
        .kop-divider { margin-top: 5px; border-top: 3px solid #000; }
        .kop-divider-thin { border-top: 1px solid #000; margin-top: 2px; }

        /* ── SURAT HEADER ── */
        .surat-meta td { font-size: 10px; vertical-align: top; padding: 2px 2px; }
        .surat-label { width: 70px; }
        .surat-colon { width: 8px; }

        /* ── ISI LAPORAN ── */
        .isi-table { width: 100%; border-collapse: collapse; }
        .isi-table td { font-size: 10px; vertical-align: top; padding: 2.5px 2px; }
        .isi-no { width: 28px; }
        .isi-label { width: 38%; }
        .isi-colon { width: 8px; }
        .isi-value { }
        .isi-sub td { padding: 1.5px 2px 1.5px 16px; font-size: 10px; vertical-align: top; }
        .blank { border-bottom: 1px dotted #333; display: inline-block; min-width: 80px; }

        /* ── SIGNATURES ── */
        .ttd-table { width: 100%; border-collapse: collapse; margin-top: 28px; }
        .ttd-table td { width: 50%; vertical-align: top; text-align: center; font-size: 10px; padding: 0 4px; }
        .ttd-role { font-weight: bold; font-size: 9.5px; text-transform: uppercase; }
        .ttd-space { height: 52px; }
        .ttd-name { font-weight: bold; text-decoration: underline; font-size: 10px; }
        .ttd-rank { font-size: 9px; }
        .ttd-nip { font-size: 9px; }

        /* ── BERITA ACARA ── */
        .ba-title { text-align: center; font-weight: bold; font-size: 12px;
                    text-decoration: underline; text-transform: uppercase; margin-bottom: 2px; }
        .ba-nomor { text-align: center; font-size: 10px; margin-bottom: 10px; }
        .ba-opening { font-size: 10px; text-align: justify; margin-bottom: 10px; line-height: 1.6; }

        /* ── FOTO ── */
        .section-title { text-align: center; font-weight: bold; font-size: 12px;
                         text-decoration: underline; text-transform: uppercase; margin-bottom: 10px; }
        .photo-table { width: 100%; border-collapse: collapse; }
        .photo-cell { width: 50%; padding: 8px; vertical-align: top; text-align: center; }
        .photo-img-wrap { border: 1px solid #bbb; background: #f5f5f5; padding: 4px; }
        .photo-img-wrap img { max-width: 100%; max-height: 170px; }
        .photo-caption { font-size: 8.5px; color: #444; margin-top: 4px; }
        .photo-num { font-size: 9px; font-weight: bold; margin-bottom: 3px; }
        .no-photo { text-align: center; font-size: 10px; color: #666; padding: 30px 0; }

        /* ── ARMADA ── */
        .unit-block { border: 1px solid #bbb; padding: 8px; margin-bottom: 12px; background: #fafafa; }
        .unit-title { font-weight: bold; font-size: 10.5px; margin-bottom: 4px; }
        .unit-info-table td { font-size: 10px; vertical-align: top; padding: 1.5px 2px; }
        .unit-info-label { width: 130px; }
        .time-table { width: 100%; border-collapse: collapse; margin-top: 7px; }
        .time-table th, .time-table td { border: 1px solid #aaa; padding: 4px 6px; font-size: 9.5px; }
        .time-table th { background-color: #e8e8e8; font-weight: bold; text-align: center; }
        .time-table td:nth-child(2) { text-align: center; }
        .time-table td:nth-child(3) { text-align: center; }

        /* ── PAGE BREAK ── */
        .pagebreak { page-break-after: always; }
        .avoid-break { page-break-inside: avoid; }
    </style>
</head>
<body>

@php
use Carbon\Carbon;
Carbon::setLocale('id');

$logoPath = 'file://' . public_path('dinas-logo.jpg');

// ── Parse dates & times ──────────────────────────────────────────
$reqDate = $incident['request_date']
    ? (is_string($incident['request_date']) ? Carbon::parse($incident['request_date']) : $incident['request_date'])
    : now();

$dayName    = $reqDate->translatedFormat('l');
$dateStr    = $reqDate->translatedFormat('l, j F Y');
$dateOnly   = $reqDate->translatedFormat('j F Y');
$tahun      = $reqDate->year;

$otwAt       = $incident['otw_at']       ? Carbon::parse($incident['otw_at'])       : null;
$arriveAt    = $incident['arrive_at']    ? Carbon::parse($incident['arrive_at'])    : null;
$handledAt   = $incident['handled_at']   ? Carbon::parse($incident['handled_at'])   : null;
$completedAt = $incident['completed_at'] ? Carbon::parse($incident['completed_at']) : null;

$pickupTime     = $incident['pickup_time'] ?? null;
$lapowanKejaidan = $pickupTime ? substr($pickupTime, 0, 5) : '-';
$otwDisplay     = $otwAt       ? $otwAt->format('H:i')     : '-';
$arriveDisplay  = $arriveAt    ? $arriveAt->format('H:i')   : '-';
$handledDisplay = $handledAt   ? $handledAt->format('H:i')  : '-';

// ── Duration helper ───────────────────────────────────────────────
$calcDuration = function($start, $end) {
    if (!$start || !$end) return '-';
    $secs = abs($start->diffInSeconds($end));
    return number_format($secs / 60, 2);
};

$responTime    = $calcDuration($otwAt, $arriveAt);
$tkpDuration   = $calcDuration($arriveAt, $handledAt);
$pulangDuration = $calcDuration($handledAt, $completedAt);

// ── Lokasi ─────────────────────────────────────────────────────────
$address   = $incident['address']   ?? '-';
$kelurahan = $incident['kelurahan'] ?? '-';
$kecamatan = $incident['kecamatan'] ?? '-';

// ── Pelapor ────────────────────────────────────────────────────────
$reporterName  = $incident['reporter_name']  ?? '-';
$reporterPhone = $incident['reporter_phone'] ?? '-';

// ── Unit ───────────────────────────────────────────────────────────
$unitCount   = $incident['unit_count'] ?? 1;
$plateNumber = $incident['plate_number'] ?? '-';
$otherPlates = $incident['other_plates'] ?? collect();

// ── Nomor surat ────────────────────────────────────────────────────
$nomorSurat  = $incident['nomor'] ?? '___';
$suratDate   = now()->translatedFormat('l, j F Y');

// ── Wilayah ────────────────────────────────────────────────────────
$wilayah = 'Kabupaten Bekasi';
if ($kecamatan && $kecamatan !== '-') $wilayah = 'Kecamatan ' . $kecamatan . ', ' . $wilayah;

// ── Kondisi / Jenis ────────────────────────────────────────────────
$kondisi = strtoupper(str_replace('_', ' ', $incident['condition'] ?? 'kebakaran'));

// ─── Penyebab (inferred) ─────────────────────────────────────────
$penyebab = ($incident['condition'] ?? '') === 'kebakaran' ? 'Listrik / Alam / Human Error' : ucfirst(str_replace('_', ' ', $incident['condition'] ?? '-'));
@endphp

{{-- ═══════════════════════════════════════════════════════════
     HALAMAN 1 — LAPORAN KEJADIAN KEBAKARAN
     ═══════════════════════════════════════════════════════════ --}}

{{-- KOP SURAT --}}
<table class="kop-table">
    <tr>
        <td class="kop-logo-cell">
            <img src="{{ $logoPath }}" alt="Logo">
        </td>
        <td class="kop-text-cell">
            <div class="kop-pemerintah">PEMERINTAH KABUPATEN BEKASI</div>
            <div class="kop-dinas">DINAS PEMADAM KEBAKARAN</div>
            <div class="kop-addr">Jalan Teuku Umar No.1 Cikarang Barat</div>
            <div class="kop-addr">Desa Ganda Sari Kecamatan Cikarang Barat Kabupaten Bekasi &ndash; Jawa Barat</div>
            <div class="kop-addr">(021)-89101527</div>
            <div class="kop-kota">B E K A S I</div>
        </td>
        <td style="width:70px;"></td>
    </tr>
</table>
<div class="kop-divider"></div>
<div class="kop-divider-thin"></div>

{{-- SURAT META --}}
<table width="100%" style="margin-top: 10px; border-collapse: collapse;">
    <tr>
        <td width="5%"></td>
        <td width="45%" style="vertical-align: top;">
            <table class="surat-meta" style="border-collapse: collapse;">
                <tr>
                    <td class="surat-label">Nomor</td>
                    <td class="surat-colon">:</td>
                    <td>{{ $nomorSurat }} &nbsp;/&nbsp;&nbsp;/Damkar/{{ $tahun }}</td>
                </tr>
                <tr>
                    <td class="surat-label">Sifat</td>
                    <td class="surat-colon">:</td>
                    <td>Penting</td>
                </tr>
                <tr>
                    <td class="surat-label">Hal</td>
                    <td class="surat-colon">:</td>
                    <td><strong><u>Laporan Kejadian Kebakaran</u></strong></td>
                </tr>
                <tr>
                    <td class="surat-label">Lampiran</td>
                    <td class="surat-colon">:</td>
                    <td>-</td>
                </tr>
            </table>
        </td>
        <td width="50%" style="vertical-align: top; font-size: 10px; line-height: 1.7;">
            Bekasi, {{ $suratDate }}<br>
            <br>
            Kepada<br>
            Yth. Kepala Dinas Pemadam Kebakaran<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kabupaten Bekasi<br>
            Di-<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bekasi
        </td>
    </tr>
</table>

<p style="margin-top: 12px; font-size: 10px;">
    Dengan ini kami laporkan kejadian kebakaran di Wilayah Kabupaten Bekasi, Sebagai Berikut :
</p>

{{-- ISI LAPORAN --}}
<table class="isi-table" style="margin-top: 6px;">
    <tr>
        <td class="isi-no" style="vertical-align: top; padding-top: 2px;">1.</td>
        <td class="isi-label">Hari/Tanggal</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">{{ $dateStr }}</td>
    </tr>
    <tr>
        <td class="isi-no" style="vertical-align: top;">2.</td>
        <td class="isi-label">Waktu Kejadian</td>
        <td class="isi-colon">:</td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Laporan Kejadian</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">{{ $lapowanKejaidan }} WIB</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Unit Berangkat</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">{{ $otwDisplay }} WIB</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Tiba di TKK</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">{{ $arriveDisplay }} WIB</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Selesai Penanganan</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">{{ $handledDisplay }} WIB</td>
    </tr>
    <tr>
        <td class="isi-no">3.</td>
        <td class="isi-label">Kronologi</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">Laporan dari {{ $reporterName }}; kejadian {{ strtolower($kondisi) }} di {{ $address }}</td>
    </tr>
    <tr>
        <td class="isi-no">4.</td>
        <td class="isi-label">Lokasi</td>
        <td class="isi-colon">:</td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Alamat</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">{{ $address }}</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Kelurahan/Desa</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">{{ $kelurahan }}</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Kecamatan</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">{{ $kecamatan }}</td>
    </tr>
    <tr>
        <td class="isi-no">5.</td>
        <td class="isi-label">Nama Pelapor</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">{{ $reporterName }}</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">No. Telp Pelapor</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">{{ $reporterPhone }}</td>
    </tr>
    <tr>
        <td class="isi-no">6.</td>
        <td class="isi-label">Nama Ketua RT/RW</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">-</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">No.Telp Ketua RT/RW</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">-</td>
    </tr>
    <tr>
        <td class="isi-no">7.</td>
        <td class="isi-label">Luas Areal</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">- m&sup2;</td>
    </tr>
    <tr>
        <td class="isi-no">8.</td>
        <td class="isi-label">Bangunan yang terbakar</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">{{ $kondisi }}</td>
    </tr>
    <tr>
        <td class="isi-no">9.</td>
        <td class="isi-label">Nama Pemilik</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">-</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Umur</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">-</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Telp</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">-</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Pekerjaan</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">-</td>
    </tr>
    <tr>
        <td class="isi-no">10.</td>
        <td class="isi-label">Asal Api</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">{{ $penyebab }}</td>
    </tr>
    <tr>
        <td class="isi-no">11.</td>
        <td class="isi-label">Pengerahan Unit Mobil Pemadam</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">{{ $unitCount }} Unit</td>
    </tr>
    <tr>
        <td class="isi-no">12.</td>
        <td class="isi-label">No. Seri Kendaraan</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">- {{ $plateNumber }}</td>
    </tr>
    <tr>
        <td class="isi-no">13.</td>
        <td class="isi-label">Bantuan Unit Mobil Pemadam</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">
            @if(!empty($otherPlates) && count($otherPlates) > 0)
                @foreach($otherPlates as $p)
                    Dari: {{ $p }}<br>
                @endforeach
            @else
                Dari: -
            @endif
        </td>
    </tr>
    <tr>
        <td class="isi-no">14.</td>
        <td class="isi-label">Penggunaan BA/SCBA</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">- Tabung</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Penggunaan APAR</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">- Tabung</td>
    </tr>
    <tr>
        <td class="isi-no">15.</td>
        <td class="isi-label">Korban Kebakaran</td>
        <td class="isi-colon">:</td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Luka &ndash; Luka</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">- Orang</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Korban Jiwa</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">- Orang</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Korban Terdampak</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">- Orang</td>
    </tr>
</table>

<p style="margin-top: 12px; font-size: 10px;">
    Demikian Laporan ini dibuat dengan sebenarnya, Demikian agar maklum.
</p>

{{-- TTD HALAMAN 1 --}}
<table class="ttd-table">
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

{{-- ─── PAGE BREAK ─── --}}
<div class="pagebreak"></div>

{{-- ═══════════════════════════════════════════════════════════
     HALAMAN 2 — BERITA ACARA KEJADIAN KEBAKARAN
     ═══════════════════════════════════════════════════════════ --}}

{{-- KOP SURAT --}}
<table class="kop-table">
    <tr>
        <td class="kop-logo-cell"><img src="{{ $logoPath }}" alt="Logo"></td>
        <td class="kop-text-cell">
            <div class="kop-pemerintah">PEMERINTAH KABUPATEN BEKASI</div>
            <div class="kop-dinas">DINAS PEMADAM KEBAKARAN</div>
            <div class="kop-addr">Jalan Teuku Umar No.1 Cikarang Barat</div>
            <div class="kop-addr">Desa Ganda Sari Kecamatan Cikarang Barat Kabupaten Bekasi &ndash; Jawa Barat</div>
            <div class="kop-addr">(021)-89101527</div>
            <div class="kop-kota">B E K A S I</div>
        </td>
        <td style="width:70px;"></td>
    </tr>
</table>
<div class="kop-divider"></div>
<div class="kop-divider-thin"></div>

{{-- JUDUL BA --}}
<div style="margin-top: 12px;">
    <p class="ba-title">BERITA ACARA KEJADIAN KEBAKARAN</p>
    <p class="ba-nomor">NOMOR : &nbsp;&nbsp;&nbsp; / &nbsp;&nbsp;&nbsp; /DAMKAR {{ $tahun }}</p>
</div>

{{-- PARAGRAF PEMBUKA --}}
<p class="ba-opening">
    Pada Hari ini <strong>{{ $dayName }}</strong> tanggal
    <strong>{{ $reqDate->format('j') }}</strong> bulan
    <strong>{{ $reqDate->translatedFormat('F') }}</strong> tahun
    <strong>{{ $tahun }}</strong> Pukul
    <strong>{{ $lapowanKejaidan }} WIB</strong>,
    telah terjadi kebakaran di Wilayah <strong>{{ $wilayah }}</strong>, Sebagai berikut:
</p>

{{-- ISI BERITA ACARA --}}
<table class="isi-table">
    <tr>
        <td class="isi-no">1.</td>
        <td class="isi-label">Hari/Tanggal</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">{{ $dateStr }}</td>
    </tr>
    <tr>
        <td class="isi-no">2.</td>
        <td class="isi-label">Waktu Kejadian</td>
        <td class="isi-colon">:</td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Laporan Kejadian</td>
        <td class="isi-colon">:</td>
        <td>{{ $lapowanKejaidan }} WIB</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Unit Berangkat</td>
        <td class="isi-colon">:</td>
        <td>{{ $otwDisplay }} WIB</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Tiba di TKK</td>
        <td class="isi-colon">:</td>
        <td>{{ $arriveDisplay }} WIB</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Selesai Penanganan</td>
        <td class="isi-colon">:</td>
        <td>{{ $handledDisplay }} WIB</td>
    </tr>
    <tr>
        <td class="isi-no">3.</td>
        <td class="isi-label">Kronologi</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">Laporan dari {{ $reporterName }}; kejadian {{ strtolower($kondisi) }} di {{ $address }}</td>
    </tr>
    <tr>
        <td class="isi-no">4.</td>
        <td class="isi-label">Lokasi Kebakaran</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">{{ $address }}</td>
    </tr>
    <tr>
        <td class="isi-no">5.</td>
        <td class="isi-label">Jenis Bangunan yang terbakar</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">{{ $kondisi }}</td>
    </tr>
    <tr>
        <td class="isi-no">6.</td>
        <td class="isi-label">Penyebab Kebakaran</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">{{ $penyebab }}</td>
    </tr>
    <tr>
        <td class="isi-no">7.</td>
        <td class="isi-label">Luas Area</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">- m&sup2;</td>
    </tr>
    <tr>
        <td class="isi-no">8.</td>
        <td class="isi-label">Nama Pemilik</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">-</td>
    </tr>
    <tr>
        <td class="isi-no">9.</td>
        <td class="isi-label">Umur</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">-</td>
    </tr>
    <tr>
        <td class="isi-no">10.</td>
        <td class="isi-label">Pekerjaan</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">-</td>
    </tr>
    <tr>
        <td class="isi-no">11.</td>
        <td class="isi-label">Lokasi</td>
        <td class="isi-colon">:</td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Alamat</td>
        <td class="isi-colon">:</td>
        <td>{{ $address }}</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Kelurahan/Desa</td>
        <td class="isi-colon">:</td>
        <td>{{ $kelurahan }}</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Kecamatan</td>
        <td class="isi-colon">:</td>
        <td>{{ $kecamatan }}</td>
    </tr>
    <tr>
        <td class="isi-no">12.</td>
        <td class="isi-label">Nama Pelapor</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">{{ $reporterName }}</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">No.Telp Pelapor</td>
        <td class="isi-colon">:</td>
        <td>{{ $reporterPhone }}</td>
    </tr>
    <tr>
        <td class="isi-no">13.</td>
        <td class="isi-label">Nama Ketua RT/RW</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">-</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">No.Telp Ketua RT/RW</td>
        <td class="isi-colon">:</td>
        <td>-</td>
    </tr>
    <tr>
        <td class="isi-no">14.</td>
        <td class="isi-label">Unit Mobil Pemadam yang dikerahkan</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">{{ $unitCount }} Unit</td>
    </tr>
    <tr>
        <td class="isi-no">15.</td>
        <td class="isi-label">No. Seri Kendaraan</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">- {{ $plateNumber }}</td>
    </tr>
    <tr>
        <td class="isi-no">16.</td>
        <td class="isi-label">Bantuan Unit Mobil Pemadam</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">
            @if(!empty($otherPlates) && count($otherPlates) > 0)
                @foreach($otherPlates as $p)
                    Dari: {{ $p }}<br>
                @endforeach
            @else
                Dari: -
            @endif
        </td>
    </tr>
    <tr>
        <td class="isi-no">17.</td>
        <td class="isi-label">Penggunaan BA/SCBA</td>
        <td class="isi-colon">:</td>
        <td class="isi-value">- Tabung</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Penggunaan APAR</td>
        <td class="isi-colon">:</td>
        <td>- Tabung</td>
    </tr>
    <tr>
        <td class="isi-no">18.</td>
        <td class="isi-label">Korban Kebakaran</td>
        <td class="isi-colon">:</td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Luka &ndash; Luka</td>
        <td class="isi-colon">:</td>
        <td>- Orang</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Korban Jiwa</td>
        <td class="isi-colon">:</td>
        <td>- Orang</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 16px; font-size: 10px;">Korban Terdampak</td>
        <td class="isi-colon">:</td>
        <td>- Orang</td>
    </tr>
</table>

<p style="margin-top: 10px; font-size: 10px;">
    Demikian Berita Acara Kejadian Kebakaran ini dibuat dengan sebenarnya, Demikian agar maklum.
</p>

{{-- TTD HALAMAN 2 --}}
<table width="100%" style="margin-top: 10px; border-collapse: collapse; font-size: 10px; text-align: left;">
    <tr>
        <td width="50%">&nbsp;</td>
        <td width="50%" style="text-align: left;">Bekasi, {{ $dateOnly }}</td>
    </tr>
</table>
<table class="ttd-table">
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

{{-- ─── PAGE BREAK ─── --}}
<div class="pagebreak"></div>

{{-- ═══════════════════════════════════════════════════════════
     HALAMAN 3 — DOKUMENTASI FOTO LAPANGAN
     ═══════════════════════════════════════════════════════════ --}}

{{-- KOP SURAT --}}
<table class="kop-table">
    <tr>
        <td class="kop-logo-cell"><img src="{{ $logoPath }}" alt="Logo"></td>
        <td class="kop-text-cell">
            <div class="kop-pemerintah">PEMERINTAH KABUPATEN BEKASI</div>
            <div class="kop-dinas">DINAS PEMADAM KEBAKARAN</div>
            <div class="kop-addr">Jalan Teuku Umar No.1 Cikarang Barat</div>
            <div class="kop-addr">Desa Ganda Sari Kecamatan Cikarang Barat Kabupaten Bekasi &ndash; Jawa Barat</div>
            <div class="kop-addr">(021)-89101527</div>
            <div class="kop-kota">B E K A S I</div>
        </td>
        <td style="width:70px;"></td>
    </tr>
</table>
<div class="kop-divider"></div>
<div class="kop-divider-thin"></div>

<p class="section-title" style="margin-top: 12px;">DOKUMENTASI FOTO LAPANGAN</p>

@if($photos->isEmpty())
    <div class="no-photo">
        <p>Tidak ada dokumentasi foto tersedia.</p>
    </div>
@else
    @php
        $photoChunks = $photos->chunk(6);
    @endphp

    @foreach($photoChunks as $chunkIndex => $chunk)
        @if($chunkIndex > 0)
            <div class="pagebreak"></div>
            {{-- KOP for overflow pages --}}
            <table class="kop-table">
                <tr>
                    <td class="kop-logo-cell"><img src="{{ $logoPath }}" alt="Logo"></td>
                    <td class="kop-text-cell">
                        <div class="kop-pemerintah">PEMERINTAH KABUPATEN BEKASI</div>
                        <div class="kop-dinas">DINAS PEMADAM KEBAKARAN</div>
                        <div class="kop-addr">(021)-89101527 &mdash; Jalan Teuku Umar No.1 Cikarang Barat</div>
                        <div class="kop-kota">B E K A S I</div>
                    </td>
                    <td style="width:70px;"></td>
                </tr>
            </table>
            <div class="kop-divider"></div>
            <div class="kop-divider-thin"></div>
            <p class="section-title" style="margin-top: 8px;">DOKUMENTASI FOTO LAPANGAN (Lanjutan)</p>
        @endif

        @php $pairs = $chunk->chunk(2); @endphp
        <table class="photo-table">
            @foreach($pairs as $pair)
            <tr>
                @foreach($pair as $idx => $photoObj)
                @php
                    $globalNum = ($chunkIndex * 6) + $loop->parent->index * 2 + $idx + 1;
                    $photoSrc  = 'file://' . storage_path('app/public/' . $photoObj->photo->photo_path);
                    $uploadedAt = $photoObj->photo->created_at
                        ? Carbon::parse($photoObj->photo->created_at)->format('d/m/Y H:i')
                        : '-';
                @endphp
                <td class="photo-cell">
                    <p class="photo-num">Foto {{ $globalNum }}</p>
                    <div class="photo-img-wrap avoid-break">
                        <img src="{{ $photoSrc }}" alt="Foto {{ $globalNum }}">
                    </div>
                    <p class="photo-caption">
                        Petugas: <strong>{{ $photoObj->uploader }}</strong><br>
                        Waktu: {{ $uploadedAt }}
                    </p>
                </td>
                @endforeach
                @if($pair->count() < 2)
                <td class="photo-cell"></td>
                @endif
            </tr>
            @endforeach
        </table>
    @endforeach
@endif

{{-- ─── PAGE BREAK ─── --}}
<div class="pagebreak"></div>

{{-- ═══════════════════════════════════════════════════════════
     HALAMAN 4 — UNIT ARMADA YANG DITUGASKAN
     ═══════════════════════════════════════════════════════════ --}}

{{-- KOP SURAT --}}
<table class="kop-table">
    <tr>
        <td class="kop-logo-cell"><img src="{{ $logoPath }}" alt="Logo"></td>
        <td class="kop-text-cell">
            <div class="kop-pemerintah">PEMERINTAH KABUPATEN BEKASI</div>
            <div class="kop-dinas">DINAS PEMADAM KEBAKARAN</div>
            <div class="kop-addr">Jalan Teuku Umar No.1 Cikarang Barat</div>
            <div class="kop-addr">Desa Ganda Sari Kecamatan Cikarang Barat Kabupaten Bekasi &ndash; Jawa Barat</div>
            <div class="kop-addr">(021)-89101527</div>
            <div class="kop-kota">B E K A S I</div>
        </td>
        <td style="width:70px;"></td>
    </tr>
</table>
<div class="kop-divider"></div>
<div class="kop-divider-thin"></div>

<p class="section-title" style="margin-top: 12px;">UNIT ARMADA YANG DITUGASKAN</p>

@forelse($dispatches as $dIdx => $d)
@php
    $dOtwAt       = $d->otw_scene_at   ? Carbon::parse($d->otw_scene_at)   : null;
    $dArriveAt    = $d->pickup_at      ? Carbon::parse($d->pickup_at)      : null;
    $dHandledAt   = $d->hospital_at    ? Carbon::parse($d->hospital_at)    : null;
    $dCompletedAt = $d->completed_at   ? Carbon::parse($d->completed_at)   : null;

    $dResponTime  = ($dOtwAt && $dArriveAt)
        ? number_format(abs($dOtwAt->diffInSeconds($dArriveAt)) / 60, 2)
        : '-';
    $dTkpDur      = ($dArriveAt && $dHandledAt)
        ? number_format(abs($dArriveAt->diffInSeconds($dHandledAt)) / 60, 2)
        : '-';
    $dPulangDur   = ($dHandledAt && $dCompletedAt)
        ? number_format(abs($dHandledAt->diffInSeconds($dCompletedAt)) / 60, 2)
        : '-';
@endphp

<div class="unit-block avoid-break">
    <div class="unit-title">Unit {{ $dIdx + 1 }}: {{ $d->ambulance?->plate_number ?? '(-)' }}
        @if($d->ambulance?->code) ({{ $d->ambulance->code }}) @endif
    </div>

    <table class="unit-info-table" style="border-collapse: collapse; width: auto;">
        <tr>
            <td class="unit-info-label"><strong>Petugas</strong></td>
            <td style="width:8px;">:</td>
            <td>{{ $d->driver?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="unit-info-label"><strong>Waktu Penugasan</strong></td>
            <td>:</td>
            <td>{{ $d->assigned_at ? Carbon::parse($d->assigned_at)->format('d-m-Y H:i') : '-' }}</td>
        </tr>
        <tr>
            <td class="unit-info-label"><strong>Status Unit</strong></td>
            <td>:</td>
            <td>{{ strtoupper(str_replace('_', ' ', $d->status)) }}</td>
        </tr>
    </table>

    <table class="time-table">
        <thead>
            <tr>
                <th style="text-align: left; width: 35%;">Keterangan Waktu</th>
                <th style="width: 20%;">Jam</th>
                <th style="width: 45%;">Durasi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Berangkat (OTW)</td>
                <td>{{ $dOtwAt ? $dOtwAt->format('H:i:s') : '-' }}</td>
                <td rowspan="2" style="vertical-align: middle; text-align: center;">
                    @if($dResponTime !== '-')
                        {{ $dResponTime }} Menit <em>(Respon Time)</em>
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <td>Tiba di TKP</td>
                <td>{{ $dArriveAt ? $dArriveAt->format('H:i:s') : '-' }}</td>
            </tr>
            <tr>
                <td>Selesai TKP (Kembali)</td>
                <td>{{ $dHandledAt ? $dHandledAt->format('H:i:s') : '-' }}</td>
                <td style="text-align: center;">
                    @if($dTkpDur !== '-')
                        {{ $dTkpDur }} Menit <em>(Di TKP)</em>
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <td>Sampai di Mako</td>
                <td>{{ $dCompletedAt ? $dCompletedAt->format('H:i:s') : '-' }}</td>
                <td style="text-align: center;">
                    @if($dPulangDur !== '-')
                        {{ $dPulangDur }} Menit <em>(Perjalanan Pulang)</em>
                    @else
                        -
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</div>

@empty
    <p style="text-align: center; font-size: 10px; color: #666; padding: 20px 0;">
        Tidak ada data unit armada.
    </p>
@endforelse

</body>
</html>

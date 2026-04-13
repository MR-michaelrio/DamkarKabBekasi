<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Kejadian - Damkar Kabupaten Bekasi</title>
    <style>
        @page {
            size: A4;
            margin: 1.8cm 1.8cm 2cm 1.8cm;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #000;
        }

        /* ─── KOP ─────────────────────────────── */
        .kop-wrap { width: 100%; border-collapse: collapse; }
        .kop-wrap td { vertical-align: middle; padding: 0; }
        .kop-logo { width: 80px; text-align: center; }
        .kop-logo img { width: 74px; height: 74px; }
        .kop-text { text-align: center; padding: 0 6px; }
        .kop-text .t1 { font-size: 10pt; font-weight: bold; }
        .kop-text .t2 { font-size: 18pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .kop-text .t3 { font-size: 8pt; margin-top: 1px; }
        .kop-text .t4 { font-size: 9.5pt; font-weight: bold; letter-spacing: 5px; margin-top: 3px; }
        .kop-spacer { width: 80px; }
        .line-thick { border-top: 4px solid #000; margin-top: 6px; }
        .line-thin  { border-top: 1px solid #000; margin-top: 2px; }

        /* ─── JUDUL FORMULIR ─────────────────── */
        .form-title {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 14px 0 14px;
        }

        /* ─── TABEL ISI ──────────────────────── */
        .info-tbl { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .info-tbl td { font-size: 10pt; vertical-align: top; padding: 3px 0; }
        .info-label { width: 220px; }
        .info-sep   { width: 12px; }

        /* ─── SECTION TITLE ──────────────────── */
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #555;
            padding-bottom: 3px;
            margin: 16px 0 8px;
        }

        /* ─── TABEL LOG WAKTU ────────────────── */
        .log-tbl {
            width: 100%;
            border-collapse: collapse;
        }
        .log-tbl th, .log-tbl td {
            border: 1px solid #444;
            padding: 5px 8px;
            font-size: 9.5pt;
        }
        .log-tbl thead th {
            background-color: #d8d8d8;
            font-weight: bold;
            text-align: center;
            font-size: 9pt;
            text-transform: uppercase;
        }
        .log-tbl td.center { text-align: center; }

        /* ─── FOOTER ──────────────────────────── */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #aaa;
            padding-top: 3px;
            font-size: 8pt;
            color: #555;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="footer">
        Dinas Pemadam Kebakaran Kabupaten Bekasi &nbsp;|&nbsp; Dicetak: {{ now()->format('d-m-Y H:i') }}
    </div>

    {{-- KOP SURAT --}}
    @php $logoPath = 'file://' . public_path('dinas-logo.jpg'); @endphp
    <table class="kop-wrap">
        <tr>
            <td class="kop-logo"><img src="{{ $logoPath }}" alt="Logo"></td>
            <td class="kop-text">
                <div class="t1">PEMERINTAH KABUPATEN BEKASI</div>
                <div class="t2">DINAS PEMADAM KEBAKARAN</div>
                <div class="t3">Jalan Teuku Umar No.1 Cikarang Barat, Desa Ganda Sari Kecamatan Cikarang Barat Kabupaten Bekasi &ndash; Jawa Barat</div>
                <div class="t3">(021)-89101527</div>
                <div class="t4">B E K A S I</div>
            </td>
            <td class="kop-spacer"></td>
        </tr>
    </table>
    <div class="line-thick"></div>
    <div class="line-thin"></div>

    <p class="form-title">Formulir Laporan Kejadian</p>

    {{-- ── DETAIL KEJADIAN ─────────────────── --}}
    <table class="info-tbl">
        <tr>
            <td class="info-label">Unit</td>
            <td class="info-sep">:</td>
            <td><strong>{{ $dispatch->ambulance?->plate_number ?? '-' }}</strong>
                &nbsp;&mdash;&nbsp; {{ $dispatch->ambulance?->type ?? '-' }}
                &nbsp;&mdash;&nbsp; {{ $dispatch->driver?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Hari</td>
            <td class="info-sep">:</td>
            <td>{{ $dispatch->request_date ? \Carbon\Carbon::parse($dispatch->request_date)->translatedFormat('l') : '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Tanggal</td>
            <td class="info-sep">:</td>
            <td>{{ $dispatch->request_date?->format('d-m-Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Media Komunikasi</td>
            <td class="info-sep">:</td>
            <td>Telp / WA</td>
        </tr>
        <tr>
            <td class="info-label">Nomor Telepon Yang Dapat Dihubungi</td>
            <td class="info-sep">:</td>
            <td>{{ $dispatch->patient_phone ?? '08….' }}</td>
        </tr>
        <tr>
            <td class="info-label">Laporan Dari</td>
            <td class="info-sep">:</td>
            <td>{{ $dispatch->patient_name ?? '…..' }}</td>
        </tr>
        <tr>
            <td class="info-label">Pada Jam</td>
            <td class="info-sep">:</td>
            <td>{{ $dispatch->pickup_time ?? '….' }} WIB</td>
        </tr>
        <tr>
            <td class="info-label">Jenis Kejadian</td>
            <td class="info-sep">:</td>
            <td>{{ strtoupper(str_replace('_', ' ', $dispatch->patient_condition)) }}</td>
        </tr>
    </table>

    <div class="section-title">Lokasi TKP</div>
    <table class="info-tbl">
        <tr>
            <td class="info-label">Alamat TKP</td>
            <td class="info-sep">:</td>
            <td>{{ $dispatch->pickup_address }}</td>
        </tr>
        <tr>
            <td class="info-label">Blok</td>
            <td class="info-sep">:</td>
            <td>{{ $dispatch->blok ?? '…..' }}</td>
        </tr>
        <tr>
            <td class="info-label">RT / RW</td>
            <td class="info-sep">:</td>
            <td>{{ $dispatch->rt ?? '..' }} / {{ $dispatch->rw ?? '..' }}</td>
        </tr>
        <tr>
            <td class="info-label">Kelurahan</td>
            <td class="info-sep">:</td>
            <td>{{ $dispatch->kelurahan ?? '…..' }}</td>
        </tr>
        <tr>
            <td class="info-label">Kecamatan</td>
            <td class="info-sep">:</td>
            <td>{{ $dispatch->kecamatan ?? '……' }}</td>
        </tr>
        <tr>
            <td class="info-label">Nomor</td>
            <td class="info-sep">:</td>
            <td>{{ $dispatch->nomor ?? '…' }}</td>
        </tr>
    </table>

    {{-- ── LOG WAKTU & RESPON TIME ─────────── --}}
    <div class="section-title">Log Waktu &amp; Respon Time</div>
    @php
        $otwToScene = ($dispatch->otw_scene_at && $dispatch->pickup_at)
            ? $dispatch->otw_scene_at->diffInMinutes($dispatch->pickup_at)
            : '-';
        $atScene = ($dispatch->pickup_at && $dispatch->hospital_at)
            ? $dispatch->pickup_at->diffInMinutes($dispatch->hospital_at)
            : '-';
        $sceneToBase = ($dispatch->hospital_at && $dispatch->completed_at)
            ? $dispatch->hospital_at->diffInMinutes($dispatch->completed_at)
            : '-';
    @endphp
    <table class="log-tbl">
        <thead>
            <tr>
                <th style="text-align:left; width:45%;">Keterangan</th>
                <th style="width:20%;">Jam (WIB)</th>
                <th>Durasi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Berangkat (OTW)</td>
                <td class="center">{{ $dispatch->otw_scene_at ? $dispatch->otw_scene_at->format('H:i:s') : '&hellip;' }}</td>
                <td class="center" rowspan="2" style="vertical-align: middle;">
                    {{ $otwToScene }} Menit <em>(Respon Time)</em>
                </td>
            </tr>
            <tr>
                <td>Tiba di TKP</td>
                <td class="center">{{ $dispatch->pickup_at ? $dispatch->pickup_at->format('H:i:s') : '&hellip;' }}</td>
            </tr>
            <tr>
                <td>Selesai Penanganan (Kembali)</td>
                <td class="center">{{ $dispatch->hospital_at ? $dispatch->hospital_at->format('H:i:s') : '&hellip;' }}</td>
                <td class="center">{{ $atScene }} Menit <em>(Di TKP)</em></td>
            </tr>
            <tr>
                <td>Sampai di Mako (Standby)</td>
                <td class="center">{{ $dispatch->completed_at ? $dispatch->completed_at->format('H:i:s') : '&hellip;' }}</td>
                <td class="center">{{ $sceneToBase }} Menit <em>(Perjalanan Pulang)</em></td>
            </tr>
        </tbody>
    </table>

</body>

</html>
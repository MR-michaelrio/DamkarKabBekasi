<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Unit Damkar Kabupaten Bekasi</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1.5cm 1.8cm 1.8cm 1.8cm;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9.5pt;
            line-height: 1.5;
            color: #000;
        }

        /* ─── KOP ─────────────────────────────── */
        .kop-wrap { width: 100%; border-collapse: collapse; }
        .kop-wrap td { vertical-align: middle; padding: 0; }
        .kop-logo { width: 70px; text-align: center; }
        .kop-logo img { width: 64px; height: 64px; }
        .kop-text { text-align: center; padding: 0 6px; }
        .kop-text .t1 { font-size: 9.5pt; font-weight: bold; }
        .kop-text .t2 { font-size: 16pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .kop-text .t3 { font-size: 7.5pt; margin-top: 1px; }
        .kop-text .t4 { font-size: 9pt; font-weight: bold; letter-spacing: 5px; margin-top: 3px; }
        .kop-spacer { width: 70px; }
        .line-thick { border-top: 4px solid #000; margin-top: 6px; }
        .line-thin  { border-top: 1px solid #000; margin-top: 2px; }

        /* ─── JUDUL ───────────────────────────── */
        .report-title {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 12px 0 2px;
        }
        .report-sub {
            text-align: center;
            font-size: 8.5pt;
            color: #444;
            margin-bottom: 12px;
        }

        /* ─── TABEL ───────────────────────────── */
        table.data-tbl {
            width: 100%;
            border-collapse: collapse;
        }
        table.data-tbl th,
        table.data-tbl td {
            border: 1px solid #555;
            padding: 4px 6px;
            vertical-align: top;
            font-size: 8.5pt;
        }
        table.data-tbl thead th {
            background-color: #d0d0d0;
            font-weight: bold;
            text-align: center;
            font-size: 8pt;
            text-transform: uppercase;
        }
        table.data-tbl tbody tr:nth-child(even) td {
            background-color: #f5f5f5;
        }
        table.data-tbl td.center { text-align: center; }

        /* ─── FOOTER ──────────────────────────── */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #aaa;
            padding-top: 3px;
            font-size: 7.5pt;
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

    <p class="report-title">Laporan Unit Damkar Kabupaten Bekasi</p>
    <p class="report-sub">Dicetak: {{ now()->format('d F Y, H:i') }} WIB</p>

    <table class="data-tbl">
        <thead>
            <tr>
                <th style="width:26px;">No</th>
                <th>Pelapor</th>
                <th>Kondisi</th>
                <th>Alamat Jemput</th>
                <th>Tujuan</th>
                <th>Driver</th>
                <th>Unit Damkar</th>
                <th>Status</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dispatches as $i => $d)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>{{ $d->patient_name }}</td>
                <td>{{ $d->patient_condition }}</td>
                <td>{{ $d->pickup_address }}</td>
                <td>{{ $d->destination ?? '-' }}</td>
                <td>{{ $d->driver->name ?? '-' }}</td>
                <td class="center">{{ $d->ambulance->plate_number ?? '-' }}</td>
                <td class="center">{{ strtoupper(str_replace('_', ' ', $d->status)) }}</td>
                <td class="center">{{ $d->created_at ? $d->created_at->format('d/m/Y H:i') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
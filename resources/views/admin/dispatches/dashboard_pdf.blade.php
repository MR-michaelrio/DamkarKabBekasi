<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
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

        /* ─── JUDUL ───────────────────────────── */
        .report-title {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 14px 0 2px;
        }
        .report-sub {
            text-align: center;
            font-size: 9pt;
            color: #444;
            margin-bottom: 14px;
        }

        /* ─── SECTION TITLE ──────────────────── */
        .section-title {
            background: #e8e8e8;
            padding: 5px 10px;
            font-weight: bold;
            font-size: 9.5pt;
            text-transform: uppercase;
            border-left: 4px solid #c0392b;
            margin-top: 16px;
            margin-bottom: 10px;
        }

        /* ─── ANALITIK PER MOBIL ─────────────── */
        .analytics-tbl { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .analytics-tbl td {
            border: 1px solid #ccc;
            padding: 8px 10px;
            vertical-align: top;
            font-size: 9.5pt;
            background: #fafafa;
            width: 25%;
        }
        .analytics-plate { font-weight: bold; font-size: 10pt; }
        .analytics-count { font-size: 20pt; font-weight: bold; color: #1a1a1a; line-height: 1.2; }
        .analytics-kali  { font-size: 8pt; color: #555; text-transform: uppercase; }
        .analytics-breakdown { font-size: 8pt; color: #444; margin-top: 4px; border-top: 1px solid #ddd; padding-top: 4px; }

        /* ─── TABEL DISPATCH ─────────────────── */
        table.data-tbl { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table.data-tbl th,
        table.data-tbl td {
            border: 1px solid #555;
            padding: 4px 6px;
            vertical-align: top;
            font-size: 9pt;
        }
        table.data-tbl thead th {
            background-color: #d0d0d0;
            font-weight: bold;
            text-align: center;
            font-size: 8.5pt;
            text-transform: uppercase;
        }
        table.data-tbl tbody tr:nth-child(even) td { background-color: #f5f5f5; }
        table.data-tbl td.center { text-align: center; }
        table.data-tbl tfoot td {
            background-color: #ebebeb;
            font-weight: bold;
        }

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
        Dinas Pemadam Kebakaran Kabupaten Bekasi &nbsp;|&nbsp; Dicetak: {{ now()->format('d-m-Y H:i:s') }}
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

    <p class="report-title">{{ $title }}</p>
    <p class="report-sub">
        Periode:
        @if($range === 'today') {{ now()->format('d F Y') }}
        @elseif($range === 'week') {{ now()->startOfWeek()->format('d M') }} &ndash; {{ now()->endOfWeek()->format('d M Y') }}
        @elseif($range === 'month') {{ now()->format('F Y') }}
        @else Semua Waktu
        @endif
    </p>

    {{-- ── ANALITIK PER MOBIL ──────────────── --}}
    <div class="section-title">Analitik Per Armada (Penggunaan)</div>
    @php
        $analyticsChunks = $analytics->chunk(4);
    @endphp
    @foreach($analyticsChunks as $chunk)
    <table class="analytics-tbl">
        <tr>
            @foreach($chunk as $a)
            <td>
                <div class="analytics-plate">{{ $a->plate_number }}</div>
                <div class="analytics-count">{{ $a->dispatches_count }}</div>
                <div class="analytics-kali">Kali Keluar</div>
                @if($a->condition_breakdown->isNotEmpty())
                <div class="analytics-breakdown">
                    @foreach($a->condition_breakdown as $condition => $count)
                    {{ $condition }}: <strong>{{ $count }}</strong><br>
                    @endforeach
                </div>
                @endif
            </td>
            @endforeach
            {{-- fill empty cells if chunk < 4 --}}
            @for($i = $chunk->count(); $i < 4; $i++)
            <td style="background:transparent; border:none;"></td>
            @endfor
        </tr>
    </table>
    @endforeach

    {{-- ── DAFTAR DISPATCH ─────────────────── --}}
    <div class="section-title">Daftar Dispatch</div>
    <table class="data-tbl">
        <thead>
            <tr>
                <th style="width:70px;">Waktu</th>
                <th>Pelapor</th>
                <th style="width:70px;">Armada</th>
                <th>Driver</th>
                <th style="width:46px;">Respon</th>
                <th style="width:40px;">TKP</th>
                <th style="width:46px;">Pulang</th>
                <th style="width:70px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dispatches as $d)
            <tr>
                <td class="center">{{ $d->created_at->format('d/m H:i') }}</td>
                <td>
                    <strong>{{ $d->patient_name }}</strong><br>
                    <small>{{ $d->patient_condition }}</small>
                </td>
                <td class="center">{{ $d->ambulance?->plate_number ?? '-' }}</td>
                <td>{{ $d->driver?->name ?? '-' }}</td>
                @php
                    $otwToScene  = ($d->otw_scene_at && $d->pickup_at)    ? $d->otw_scene_at->diffInMinutes($d->pickup_at)    : '-';
                    $atScene     = ($d->pickup_at    && $d->hospital_at)   ? $d->pickup_at->diffInMinutes($d->hospital_at)     : '-';
                    $sceneToBase = ($d->hospital_at  && $d->completed_at)  ? $d->hospital_at->diffInMinutes($d->completed_at)  : '-';
                @endphp
                <td class="center">{{ $otwToScene !== '-' ? $otwToScene . 'm' : '-' }}</td>
                <td class="center">{{ $atScene     !== '-' ? $atScene     . 'm' : '-' }}</td>
                <td class="center">{{ $sceneToBase !== '-' ? $sceneToBase . 'm' : '-' }}</td>
                <td class="center" style="font-size:8pt; font-weight:bold; text-transform:uppercase;">
                    {{ str_replace('_', ' ', $d->status) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="center">Tidak ada data ditemukan</td>
            </tr>
            @endforelse
        </tbody>
        @php
            $totalResponTime = 0; $countResponTime = 0;
            foreach($dispatches as $d) {
                if ($d->otw_scene_at && $d->pickup_at) {
                    $totalResponTime += $d->otw_scene_at->diffInMinutes($d->pickup_at);
                    $countResponTime++;
                }
            }
            $avgResponTime = $countResponTime > 0 ? round($totalResponTime / $countResponTime, 1) : 0;
        @endphp
        @if($countResponTime > 0)
        <tfoot>
            <tr>
                <td colspan="4" style="text-align:right;">Total &amp; Rata-rata Respon Time:</td>
                <td class="center">{{ $totalResponTime }}m<br><small>Avg: {{ $avgResponTime }}m</small></td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
        @endif
    </table>

</body>

</html>
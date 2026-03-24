<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #ed1c24;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            color: #ed1c24;
            font-size: 20px;
        }

        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 12px;
        }

        .section-title {
            background: #f4f4f4;
            padding: 5px 10px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            border-left: 4px solid #ed1c24;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f9f9f9;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }

        .status {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
        }

        .badge {
            padding: 2px 5px;
            border-radius: 3px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }

        .analytics-grid {
            margin-bottom: 20px;
        }

        .analytics-item {
            display: inline-block;
            width: 30%;
            border: 1px solid #eee;
            padding: 10px;
            margin-right: 10px;
            margin-bottom: 10px;
            vertical-align: top;
        }

        .analytics-count {
            font-size: 18px;
            font-weight: bold;
            color: #2d3748;
        }

        .analytics-label {
            font-size: 9px;
            color: #718096;
            text-transform: uppercase;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>{{ $title }}</h1>
        <p>DAMKAR KABUPATEN BEKASI DISPATCH SYSTEM</p>

        <p>Periode:
            @if($range === 'today') {{ now()->format('d F Y') }}
            @elseif($range === 'week') {{ now()->startOfWeek()->format('d M') }} - {{ now()->endOfweek()->format('d M
            Y') }}
            @elseif($range === 'month') {{ now()->format('F Y') }}
            @else Semua Waktu
            @endif
        </p>
    </div>

    <div class="section-title">📊 ANALITIK PER MOBIL (PENGGUNAAN)</div>
    <div class="analytics-grid">
        @foreach($analytics as $a)
        <div class="analytics-item">
            <div class="analytics-label">{{ $a->plate_number }}</div>
            <div class="analytics-count">{{ $a->dispatches_count }}</div>
            <div class="analytics-label">KALI KELUAR</div>

            @if($a->condition_breakdown->isNotEmpty())
            <div style="margin-top: 5px; border-top: 1px solid #eee; padding-top: 5px;">
                @foreach($a->condition_breakdown as $condition => $count)
                <div style="font-size: 8px; color: #4a5568;">
                    {{ $condition }}: <strong>{{ $count }}</strong>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>



    <div class="section-title">📋 DAFTAR DISPATCH</div>
    <table>
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Pelapor</th>
                <th>Armada</th>
                <th>Driver</th>
                <th>Tujuan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dispatches as $d)
            <tr>
                <td>{{ $d->created_at->format('d/m H:i') }}</td>
                <td>
                    <strong>{{ $d->patient_name }}</strong><br>
                    <small>{{ $d->patient_condition }}</small>
                </td>
                <td>{{ $d->ambulance?->plate_number ?? '-' }}</td>
                <td>{{ $d->driver?->name ?? '-' }}</td>
                <td>{{ $d->destination ?? '-' }}</td>
                <td><span class="status">{{ str_replace('_', ' ', $d->status) }}</span></td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">Tidak ada data ditemukan</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d-m-Y H:i:s') }} | Damkar Kabupaten Bekasi Dispatch

    </div>

</body>

</html>
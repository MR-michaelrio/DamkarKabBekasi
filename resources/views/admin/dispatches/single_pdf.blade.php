<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Kejadian - Damkar Kabupaten Bekasi</title>
    <style>
        @page {
            margin: 1cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h1,
        .header h2,
        .header h3 {
            margin: 0;
            text-transform: uppercase;
        }

        .header h1 {
            font-size: 16px;
        }

        .header h2 {
            font-size: 14px;
        }

        .header h3 {
            font-size: 14px;
            text-decoration: underline;
            margin-top: 10px;
        }

        .content-table {
            width: 100%;
            margin-top: 10px;
        }

        .content-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .label {
            width: 250px;
        }

        .colon {
            width: 10px;
        }

        .footer-section {
            margin-top: 30px;
        }

        .unit-list {
            margin-left: 20px;
        }

        .dots {
            border-bottom: 1px dotted #000;
            display: inline-block;
            min-width: 150px;
            line-height: 1;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>PEMERINTAH KABUPATEN BEKASI</h1>
        <h2>DINAS PEMADAM KEBAKARAN DAN PENYELAMATAN</h2>
        <h3>FORMULIR LAPORAN KEJADIAN</h3>
    </div>

    <table class="content-table">
        <tr>
            <td class="label">Unit</td>
            <td class="colon">:</td>
            <td style="font-weight: bold;">{{ $dispatch->ambulance?->plate_number ?? '-' }} - {{
                $dispatch->ambulance?->type ?? '-' }} - {{ $dispatch->driver?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Hari</td>
            <td class="colon">:</td>
            <td>{{ $dispatch->request_date ? \Carbon\Carbon::parse($dispatch->request_date)->translatedFormat('l') : '-'
                }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="colon">:</td>
            <td>{{ $dispatch->request_date?->format('d-m-Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Media Komunikasi</td>
            <td class="colon">:</td>
            <td>Telp/Wa</td>
        </tr>
        <tr>
            <td class="label">Nomor Telepon Yang Dapat Di Hubungi</td>
            <td class="colon">:</td>
            <td>{{ $dispatch->patient_phone ?? '08….' }}</td>
        </tr>
        <tr>
            <td class="label">Laporan Dari</td>
            <td class="colon">:</td>
            <td>{{ $dispatch->patient_name ?? '…..' }}</td>
        </tr>
        <tr>
            <td class="label">Pada Jam</td>
            <td class="colon">:</td>
            <td>{{ $dispatch->pickup_time ?? '….' }} WIB</td>
        </tr>
        <tr>
            <td class="label">Jenis Kejadian</td>
            <td class="colon">:</td>
            <td>{{ strtoupper(str_replace('_', ' ', $dispatch->patient_condition)) }}</td>
        </tr>
        <tr>
            <td class="label">Alamat TKP</td>
            <td class="colon">:</td>
            <td>{{ $dispatch->pickup_address }}</td>
        </tr>
        <tr>
            <td class="label">Blok</td>
            <td class="colon">:</td>
            <td>{{ $dispatch->blok ?? '…..' }}</td>
        </tr>
        <tr>
            <td class="label">RT</td>
            <td class="colon">:</td>
            <td>{{ $dispatch->rt ?? '..' }}</td>
        </tr>
        <tr>
            <td class="label">RW</td>
            <td class="colon">:</td>
            <td>{{ $dispatch->rw ?? '..' }}</td>
        </tr>
        <tr>
            <td class="label">Kelurahan</td>
            <td class="colon">:</td>
            <td>{{ $dispatch->kelurahan ?? '…..' }}</td>
        </tr>
        <tr>
            <td class="label">Kecamatan</td>
            <td class="colon">:</td>
            <td>{{ $dispatch->kecamatan ?? '……' }}</td>
        </tr>
        <tr>
            <td class="label">Nomor</td>
            <td class="colon">:</td>
            <td>{{ $dispatch->nomor ?? '…' }}</td>
        </tr>
    </table>

    <h3 style="margin-top: 20px;">LOG WAKTU & RESPON TIME</h3>
    <table class="content-table" style="border: 1px solid #000; border-collapse: collapse;">
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
        <tr>
            <td style="border: 1px solid #000; padding: 5px; font-weight: bold;">KETERANGAN</td>
            <td style="border: 1px solid #000; padding: 5px; font-weight: bold; text-align: center;">JAM (WIB)</td>
            <td style="border: 1px solid #000; padding: 5px; font-weight: bold; text-align: center;">DURASI</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px;">Berangkat (OTW)</td>
            <td style="border: 1px solid #000; padding: 5px; text-align: center;">{{ $dispatch->otw_scene_at ?
                $dispatch->otw_scene_at->format('H:i:s') : '…' }}</td>
            <td style="border: 1px solid #000; padding: 5px; text-align: center;" rowspan="2">{{ $otwToScene }} Menit
                (Respon Time)</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px;">Tiba di TKP</td>
            <td style="border: 1px solid #000; padding: 5px; text-align: center;">{{ $dispatch->pickup_at ?
                $dispatch->pickup_at->format('H:i:s') : '…' }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px;">Selesai Penanganan (Kembali)</td>
            <td style="border: 1px solid #000; padding: 5px; text-align: center;">{{ $dispatch->hospital_at ?
                $dispatch->hospital_at->format('H:i:s') : '…' }}</td>
            <td style="border: 1px solid #000; padding: 5px; text-align: center;">{{ $atScene }} Menit (Di TKP)</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px;">Sampai di Mako (Standby)</td>
            <td style="border: 1px solid #000; padding: 5px; text-align: center;">{{ $dispatch->completed_at ?
                $dispatch->completed_at->format('H:i:s') : '…' }}</td>
            <td style="border: 1px solid #000; padding: 5px; text-align: center;">{{ $sceneToBase }} Menit (Perjalanan
                Pulang)</td>
        </tr>
    </table>

    <div class="footer-section">
        <p>Damkar Kabupaten Bekasi - Dispatch System</p>
    </div>

</body>

</html>
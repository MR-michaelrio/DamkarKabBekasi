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
        .header h1, .header h2, .header h3 {
            margin: 0;
            text-transform: uppercase;
        }
        .header h1 { font-size: 16px; }
        .header h2 { font-size: 14px; }
        .header h3 { font-size: 14px; text-decoration: underline; margin-top: 10px; }
        
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
        <h3>FORMULIR LAPORAN TELEPON KEJADIAN</h3>
    </div>

    <table class="content-table">
        <tr>
            <td class="label">Hari</td>
            <td class="colon">:</td>
            <td>{{ \Carbon\Carbon::parse($dispatch->request_date)->translatedFormat('l') }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="colon">:</td>
            <td>{{ $dispatch->request_date?->format('d-m-Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Media Komunikasi</td>
            <td class="colon">:</td>
            <td>Telp</td>
        </tr>
        <tr>
            <td class="label">Hp</td>
            <td class="colon">:</td>
            <td>{{ $dispatch->patient_phone ?? '…..' }}</td>
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
            <td class="label">Nomor Telepon Yang Dapat Di Hubungi</td>
            <td class="colon">:</td>
            <td>{{ $dispatch->patient_phone ?? '08….' }}</td>
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

    <div class="footer-section">
        <p>1. Damkar : 1 unit ({{ $dispatch->ambulance?->plate_number ?? '…..' }})</p>
    </div>

</body>
</html>

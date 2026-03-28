<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Masyarakat - Damkar Kabupaten Bekasi</title>
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
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .dispatch-item {
            margin-bottom: 10px;
            padding: 5px;
            background: #f9f9f9;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>PEMERINTAH KABUPATEN BEKASI</h1>
        <h2>DINAS PEMADAM KEBAKARAN DAN PENYELAMATAN</h2>
        <h3>LAPORAN KEJADIAN MASYARAKAT</h3>
    </div>

    <table class="content-table">
        <tr>
            <td class="label">ID Laporan</td>
            <td class="colon">:</td>
            <td>#{{ $patientRequest->id }}</td>
        </tr>
        <tr>
            <td class="label">Hari / Tanggal Kejadian</td>
            <td class="colon">:</td>
            <td>{{ \Carbon\Carbon::parse($patientRequest->request_date)->translatedFormat('l, d-m-Y') }}</td>
        </tr>
        <tr>
            <td class="label">Jam Kejadian</td>
            <td class="colon">:</td>
            <td>{{ $patientRequest->pickup_time ?? '….' }} WIB</td>
        </tr>
        <tr>
            <td class="label">Pelapor</td>
            <td class="colon">:</td>
            <td>{{ $patientRequest->patient_name }}</td>
        </tr>
        <tr>
            <td class="label">No. Telepon (WA)</td>
            <td class="colon">:</td>
            <td>{{ $patientRequest->phone ?? '…..' }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Kejadian</td>
            <td class="colon">:</td>
            <td>{{ strtoupper($patientRequest->service_type) }}</td>
        </tr>
        <tr>
            <td class="label">Kondisi / Ket. Tambahan</td>
            <td class="colon">:</td>
            <td>{{ strtoupper(str_replace('_', ' ', $patientRequest->patient_condition ?? '-')) }}</td>
        </tr>
        <tr>
            <td class="label">Alamat TKP</td>
            <td class="colon">:</td>
            <td>{{ $patientRequest->pickup_address }}</td>
        </tr>
        <tr>
            <td class="label">Detail Lokasi (Blok/RT/RW)</td>
            <td class="colon">:</td>
            <td>Blok: {{ $patientRequest->blok ?? '-' }}, RT: {{ $patientRequest->rt ?? '-' }}, RW: {{ $patientRequest->rw ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Kelurahan / Kecamatan</td>
            <td class="colon">:</td>
            <td>{{ $patientRequest->kelurahan ?? '-' }} / {{ $patientRequest->kecamatan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Nomor Laporan (Internal)</td>
            <td class="colon">:</td>
            <td>{{ $patientRequest->nomor ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Status Laporan</td>
            <td class="colon">:</td>
            <td>{{ strtoupper($patientRequest->status) }}</td>
        </tr>
    </table>

    <div class="footer-section">
        <h3>UNIT ARMADA YANG DITUGASKAN</h3>
        @if($patientRequest->dispatches->count() > 0)
            @foreach($patientRequest->dispatches as $index => $d)
                <div class="dispatch-item">
                    <strong>Unit {{ $index + 1 }}:</strong> {{ $d->ambulance?->plate_number }} ({{ $d->ambulance?->code }})<br>
                    <strong>Petugas:</strong> {{ $d->driver?->name }}<br>
                    <strong>Waktu Penugasan:</strong> {{ $d->assigned_at?->format('d-m-Y H:i') }}<br>
                    <strong>Status Unit:</strong> {{ strtoupper(str_replace('_', ' ', $d->status)) }}
                </div>
            @endforeach
        @else
            <p>Belum ada unit armada yang ditugaskan untuk laporan ini.</p>
        @endif
    </div>

    <div style="margin-top: 50px; text-align: right;">
        <p>Bekasi, {{ date('d-m-Y') }}<br>Petugas Admin,</p>
        <br><br><br>
        <p>( ____________________ )</p>
    </div>

</body>
</html>

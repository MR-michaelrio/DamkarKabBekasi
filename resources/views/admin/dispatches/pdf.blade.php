<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Unit Damkar Kabupaten Bekasi</title>
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px;
        }

        th {
            background: #eee;
        }
    </style>
</head>

<body>

    <h2>Laporan Unit Damkar Kabupaten Bekasi</h2>

    <p>Tanggal cetak: {{ now()->format('d-m-Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Pasien</th>
                <th>Kondisi</th>
                <th>Jemput</th>
                <th>Tujuan</th>
                <th>Driver</th>
                <th>Unit Damkar</th>

                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dispatches as $i => $d)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $d->patient_name }}</td>
                <td>{{ $d->patient_condition }}</td>
                <td>{{ $d->pickup_address }}</td>
                <td>{{ $d->destination ?? '-' }}</td>
                <td>{{ $d->driver?->name ?? '-' }}</td>
                <td>{{ $d->ambulance?->plate_number ?? '-' }}</td>
                <td>{{ $d->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
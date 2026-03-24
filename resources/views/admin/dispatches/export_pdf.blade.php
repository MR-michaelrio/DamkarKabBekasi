<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Unit Damkar</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #000; padding:6px; }
        th { background:#eee; }
    </style>
</head>
<body>

<h2>Laporan Unit Damkar Bekasi</h2>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Pasien</th>
            <th>Kondisi</th>
            <th>Pickup</th>
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
            <td>{{ $i+1 }}</td>
            <td>{{ $d->patient_name }}</td>
            <td>{{ $d->patient_condition }}</td>
            <td>{{ $d->pickup_address }}</td>
            <td>{{ $d->destination }}</td>
            <td>{{ $d->driver->name ?? '-' }}</td>
            <td>{{ $d->ambulance->plate_number ?? '-' }}</td>
            <td>{{ $d->status }}</td>
            <td>{{ $d->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>

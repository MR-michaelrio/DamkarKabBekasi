<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kejadian Kebakaran - Test</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; }
        .header { border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        td { border: 1px solid #ddd; padding: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Test Data Kajadian Kebakaran</h2>
    </div>

    <h3>Data Kejadian:</h3>
    <table>
        <tr>
            <td><strong>Nomor</strong></td>
            <td>{{ $nomor ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Hari/Tanggal</strong></td>
            <td>{{ $day_date ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Waktu Laporan</strong></td>
            <td>{{ $time_report ?? '-' }} WIB</td>
        </tr>
        <tr>
            <td><strong>Waktu Berangkat</strong></td>
            <td>{{ $time_departure ?? '-' }} WIB</td>
        </tr>
        <tr>
            <td><strong>Waktu Tiba</strong></td>
            <td>{{ $time_arrival ?? '-' }} WIB</td>
        </tr>
        <tr>
            <td><strong>Lokasi</strong></td>
            <td>{{ $address ?? '-' }}, {{ $village ?? '-' }}, {{ $district ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Pelapor</strong></td>
            <td>{{ $reporter_name ?? '-' }} ({{ $reporter_phone ?? '-' }})</td>
        </tr>
        <tr>
            <td><strong>Kronologi</strong></td>
            <td>{{ $chronology ?? '-' }}</td>
        </tr>
    </table>

    <h3>Foto yang Diterima:</h3>
    @if(isset($photos) && $photos->count() > 0)
        <p>Total foto: {{ $photos->count() }}</p>
        <ul>
        @foreach($photos as $item)
            <li>
                Foto: {{ $item->photo->photo_name ?? 'N/A' }}
                | Path: {{ $item->photo->photo_path ?? 'N/A' }}
                | Petugas: {{ $item->uploader ?? 'N/A' }}
            </li>
        @endforeach
        </ul>
    @else
        <p>Tidak ada foto diterima dari controller</p>
    @endif

    <h3>Debug Info:</h3>
    <p>
        $nomor = {{ isset($nomor) ? 'SET' : 'NOT SET' }}<br>
        $day_date = {{ isset($day_date) ? 'SET' : 'NOT SET' }}<br>
        $time_report = {{ isset($time_report) ? 'SET' : 'NOT SET' }}<br>
        $photos = {{ isset($photos) ? 'SET (' . $photos->count() . ')' : 'NOT SET' }}<br>
    </p>
</body>
</html>

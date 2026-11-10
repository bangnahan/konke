<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kehadiran - {{ $event->title }}</title>
    <style>
        @page {
            margin: 1.5cm 1cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.3;
        }
        .header {
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0 0 4px 0;
            color: #0f172a;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0;
            color: #475569;
            font-size: 10px;
        }
        .stats-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .stats-table td {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 6px 10px;
            text-align: center;
        }
        .stats-table .number {
            font-size: 14px;
            font-weight: bold;
            color: #4f46e5;
        }
        .stats-table .label {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 6px 8px;
            font-size: 9px;
            border: 1px solid #1e293b;
        }
        .data-table td {
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
            font-size: 9px;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
        }
        .badge-success { background-color: #d1fae5; color: #065f46; }
        .badge-warning { background-color: #fef3c7; color: #92400e; }
        .badge-danger { background-color: #ffe4e6; color: #9f1239; }
        .footer {
            margin-top: 25px;
            text-align: right;
            font-size: 9px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Kehadiran Peserta</h1>
        <p><strong>Nama Acara:</strong> {{ $event->title }}</p>
        <p><strong>Waktu & Tempat:</strong> {{ $event->event_date ? $event->event_date->format('d F Y') : '-' }} | {{ $event->location ?? '-' }}</p>
        <p><strong>Waktu Unduh Dokumen:</strong> {{ now()->format('d/m/Y H:i:s') }} WIB</p>
    </div>

    <!-- Stats Table Summary -->
    <table class="stats-table">
        <tr>
            <td>
                <div class="number">{{ $totalRegistered }}</div>
                <div class="label">Total Terdaftar</div>
            </td>
            <td>
                <div class="number" style="color: #059669;">{{ $totalCheckedIn }}</div>
                <div class="label">Sudah Check-in</div>
            </td>
            <td>
                <div class="number">{{ $totalHadir }}</div>
                <div class="label">Konfirmasi Hadir</div>
            </td>
            <td>
                <div class="number" style="color: #d97706;">{{ $totalTerlambat }}</div>
                <div class="label">Hadir Terlambat</div>
            </td>
            <td>
                <div class="number" style="color: #e11d48;">{{ $totalTidakHadir }}</div>
                <div class="label">Tidak Hadir</div>
            </td>
        </tr>
    </table>

    <!-- Main Attendees Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px; text-align: center;">No</th>
                <th style="width: 80px;">Token QR</th>
                <th>Nama Lengkap</th>
                <th>Nomor WhatsApp</th>
                <th>Instansi (Wajihah)</th>
                <th>Jabatan (Amanah)</th>
                <th style="width: 70px; text-align: center;">Status</th>
                <th style="width: 70px; text-align: center;">Check-in</th>
                <th style="width: 85px;">Waktu Check-in</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendees as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="font-family: monospace; font-weight: bold;">{{ $item->qr_code_token }}</td>
                    <td><strong>{{ $item->full_name }}</strong></td>
                    <td>{{ $item->phone_number }}</td>
                    <td>{{ $item->institution }}</td>
                    <td>{{ $item->position }}</td>
                    <td style="text-align: center;">
                        @if($item->attendance_status === 'hadir')
                            <span class="badge badge-success">Hadir</span>
                        @elseif($item->attendance_status === 'hadir_terlambat')
                            <span class="badge badge-warning">Terlambat</span>
                        @else
                            <span class="badge badge-danger">Tidak Hadir</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        {{ $item->is_checked_in ? 'Ya' : 'Belum' }}
                    </td>
                    <td>
                        {{ $item->checked_in_at ? $item->checked_in_at->format('d/m/Y H:i') : '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak otomatis oleh Sistem Kehadiran Presensi Digital &bull; Dokumen Sah Panitia Event</p>
    </div>

</body>
</html>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Kehadiran & E-Tiket</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 20px;
            color: #1e293b;
        }
        .container {
            max-width: 580px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .header {
            background: linear-gradient(135deg, #4338ca, #312e81);
            color: #ffffff;
            padding: 30px 24px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 8px 0;
            font-size: 22px;
            font-weight: 800;
        }
        .header p {
            margin: 0;
            font-size: 13px;
            color: #c7d2fe;
        }
        .content {
            padding: 24px;
        }
        .greeting {
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .qr-card {
            background-color: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .qr-card img {
            width: 220px;
            height: 220px;
            display: block;
            margin: 0 auto;
        }
        .token {
            display: inline-block;
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 6px 14px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 16px;
            font-weight: bold;
            color: #4338ca;
            margin-top: 10px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .details-table td {
            padding: 8px 12px;
            font-size: 13px;
            border-bottom: 1px solid #f1f5f9;
        }
        .details-table td.label {
            color: #64748b;
            width: 40%;
        }
        .details-table td.val {
            font-weight: bold;
            color: #0f172a;
        }
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 16px 24px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <p style="text-transform: uppercase; letter-spacing: 1px; font-weight: bold; margin-bottom: 6px;">E-Tiket & Bukti Kehadiran</p>
            <h1>{{ $event->title }}</h1>
            <p>{{ $event->event_date ? $event->event_date->format('l, d F Y') : '-' }} &bull; {{ $event->location ?? '-' }}</p>
            @if($event->maps_url)
                <div style="margin-top: 12px;">
                    <a href="{{ $event->maps_url }}" target="_blank" style="display: inline-block; background-color: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.3); color: #ffffff; text-decoration: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: bold;">
                        📍 Buka Petunjuk Arah Google Maps
                    </a>
                </div>
            @endif
        </div>

        <!-- Body -->
        <div class="content">
            <div class="greeting">
                Yth. <strong>{{ $attendance->full_name }}</strong>,<br>
                Terima kasih telah mengisi formulir konfirmasi kehadiran. Berikut adalah tiket digital Anda yang wajib dibawa saat hadir di lokasi acara untuk proses scan QR Code check-in.
            </div>

            <!-- QR Code Box -->
            <div class="qr-card">
                <img src="{{ $qrImageUrl ?? $qrDataUri }}" alt="QR Code Kehadiran" width="220" height="220" style="width: 220px; height: 220px; display: block; margin: 0 auto; border: 0;">
                <div style="font-size: 11px; color: #64748b; text-transform: uppercase; margin-top: 10px;">Token Check-in Anda:</div>
                <div class="token">{{ $attendance->qr_code_token }}</div>
            </div>

            <!-- Button Link to Online Ticket -->
            <div style="text-align: center; margin: 20px 0;">
                <a href="{{ route('attendance.success', ['slug' => $event->slug, 'uuid' => $attendance->uuid]) }}" 
                   style="display: inline-block; background-color: #4338ca; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: bold; font-size: 13px;">
                    Buka / Simpan E-Tiket di Browser
                </a>
            </div>

            <!-- Detail Data Peserta -->
            <table class="details-table">
                <tr>
                    <td class="label">Nama Lengkap:</td>
                    <td class="val">{{ $attendance->full_name }}</td>
                </tr>
                <tr>
                    <td class="label">Instansi (Wajihah):</td>
                    <td class="val">{{ $attendance->institution }}</td>
                </tr>
                <tr>
                    <td class="label">Jabatan (Amanah):</td>
                    <td class="val">{{ $attendance->position }}</td>
                </tr>
                <tr>
                    <td class="label">Nomor WhatsApp:</td>
                    <td class="val">{{ $attendance->phone_number }}</td>
                </tr>
                <tr>
                    <td class="label">Status Konfirmasi:</td>
                    <td class="val">{{ $attendance->status_label }}</td>
                </tr>
                @if($event->location)
                <tr>
                    <td class="label">Lokasi Acara:</td>
                    <td class="val">
                        {{ $event->location }}
                        @if($event->maps_url)
                            <br><a href="{{ $event->maps_url }}" target="_blank" style="color: #4338ca; text-decoration: underline; font-size: 12px; font-weight: normal;">📍 Klik untuk navigasi Google Maps</a>
                        @endif
                    </td>
                </tr>
                @endif
            </table>

            <p style="font-size: 12px; color: #64748b; line-height: 1.5; text-align: center;">
                Tunjukkan QR code ini langsung dari smartphone Anda ke panitia registrasi di pintu masuk.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            Email ini dikirim secara otomatis oleh <strong>KULWIL RIAU</strong> (hi@jelatix.com) melalui Mailketing Transactional Service v2.
        </div>
    </div>

</body>
</html>


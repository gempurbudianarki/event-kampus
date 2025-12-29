<!DOCTYPE html>
<html>
<head>
    <title>E-Ticket Event Kampus</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 14px;
        }
        .ticket-box {
            border: 2px solid #333;
            padding: 0;
            margin: 0 auto;
            position: relative;
            width: 100%;
            max-width: 800px;
        }
        .header {
            background-color: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 12px;
            opacity: 0.8;
        }
        .content {
            padding: 20px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 8px;
            vertical-align: top;
            border-bottom: 1px solid #eee;
        }
        .label {
            font-weight: bold;
            color: #555;
            width: 140px;
        }
        .event-title {
            color: #4F46E5;
            font-size: 20px;
            margin: 0 0 15px 0;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 10px;
            display: inline-block;
        }
        .qr-section {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background-color: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
        }
        .ticket-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 28px;
            font-weight: bold;
            color: #333;
            letter-spacing: 3px;
            margin: 10px 0;
            display: block;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-confirmed { background-color: #dcfce7; color: #166534; }
        .badge-pending { background-color: #fef9c3; color: #854d0e; }
    </style>
</head>
<body>

    <div class="ticket-box">
        <div class="header">
            <h1>E-TICKET RESMI</h1>
            <p>Harap tunjukkan tiket ini kepada panitia saat registrasi ulang.</p>
        </div>

        <div class="content">
            <h2 class="event-title">{{ $event->title }}</h2>

            <table class="info-table">
                <tr>
                    <td class="label">Nama Peserta</td>
                    <td>: <strong>{{ $user->name }}</strong> 
                        ({{ $user->nim ?? 'NIM Belum Diisi' }})
                    </td>
                </tr>
                <tr>
                    <td class="label">Prodi / Jurusan</td>
                    <td>: {{ $user->prodi ?? '-' }} / {{ $user->jurusan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Jadwal Acara</td>
                    <td>: {{ $event->event_date->format('d F Y') }} <br> 
                        <span style="color: #666;">  Pukul {{ $event->event_date->format('H:i') }} WIB</span>
                    </td>
                </tr>
                <tr>
                    <td class="label">Lokasi</td>
                    <td>: {{ $event->location }}</td>
                </tr>
                <tr>
                    <td class="label">Status Tiket</td>
                    <td>: 
                        <span class="badge badge-{{ $registration->status }}">
                            {{ $registration->status }}
                        </span>
                    </td>
                </tr>
            </table>

            <div class="qr-section">
                <div style="margin-bottom: 5px; color: #64748b; font-size: 12px;">KODE TIKET ANDA:</div>
                <span class="ticket-code">{{ $registration->ticket_code }}</span>
                <p style="margin: 5px 0 0; font-size: 11px; color: #64748b;">
                    Jaga kerahasiaan kode tiket ini.
                </p>
            </div>

            <div class="footer">
                Dicetak pada: {{ now()->format('d M Y H:i') }} WIB <br>
                &copy; {{ date('Y') }} Event Kampus System.
            </div>
        </div>
    </div>

</body>
</html>
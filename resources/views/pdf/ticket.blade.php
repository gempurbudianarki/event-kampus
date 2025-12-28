<!DOCTYPE html>
<html>
<head>
    <title>E-Ticket</title>
    <style>
        body {
            font-family: sans-serif;
            color: #333;
        }
        .ticket-box {
            border: 2px dashed #333;
            padding: 20px;
            margin: 10px;
            position: relative;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #4F46E5; /* Warna Indigo */
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            color: #555;
            width: 120px;
        }
        .qr-placeholder {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            background: #f0f0f0;
            border: 1px solid #ddd;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        .status-badge {
            background-color: #d1fae5;
            color: #065f46;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
        }
    </style>
</head>
<body>

    <div class="ticket-box">
        <div class="header">
            <h1>E-TICKET EVENT KAMPUS</h1>
            <p style="margin-top: 5px;">Bukti Pendaftaran Resmi</p>
        </div>

        <table class="info-table">
            <tr>
                <td colspan="2">
                    <h2 style="margin: 0 0 10px 0;">{{ $event->title }}</h2>
                </td>
            </tr>
            <tr>
                <td class="label">Nama Peserta</td>
                <td>: <strong>{{ $user->name }}</strong> ({{ $user->nim }})</td>
            </tr>
            <tr>
                <td class="label">Waktu Acara</td>
                <td>: {{ \Carbon\Carbon::parse($event->event_date)->format('d F Y, H:i') }} WIB</td>
            </tr>
            <tr>
                <td class="label">Lokasi</td>
                <td>: {{ $event->location }}</td>
            </tr>
            <tr>
                <td class="label">Status Tiket</td>
                <td>: <span class="status-badge">{{ ucfirst($registration->status) }}</span></td>
            </tr>
        </table>

        <div class="qr-placeholder">
            <h3>CODE: #REG-{{ $registration->id }}-{{ $user->nim }}</h3>
            <p>Tunjukkan tiket ini saat registrasi ulang di lokasi.</p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Event Kampus System. Tiket ini sah dan digenerate otomatis oleh sistem.
        </div>
    </div>

</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>E-Ticket: {{ $registration->ticket_code }}</title>
    <style>
        body { font-family: sans-serif; color: #333; }
        .ticket-box { border: 2px dashed #444; padding: 20px; position: relative; }
        .header { border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 20px; }
        .event-title { font-size: 24px; font-weight: bold; color: #1a202c; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px; vertical-align: top; }
        .label { font-weight: bold; color: #666; width: 120px; }
        .qr-area { text-align: center; margin-top: 20px; }
        .footer { font-size: 12px; text-align: center; margin-top: 30px; color: #888; }
        .status-paid { color: green; font-weight: bold; border: 1px solid green; padding: 5px 10px; display: inline-block; }
    </style>
</head>
<body>
    <div class="ticket-box">
        <div class="header">
            <span class="event-title">EVENT TICKET</span>
            <div style="float: right; font-size: 14px; margin-top: 5px;">
                Kode: <strong>{{ $registration->ticket_code }}</strong>
            </div>
        </div>

        <table class="info-table">
            <tr>
                <td class="label">Nama Event</td>
                <td>: <strong>{{ $event->title }}</strong></td>
            </tr>
            <tr>
                <td class="label">Jadwal</td>
                <td>: {{ \Carbon\Carbon::parse($event->event_date)->isoFormat('dddd, D MMMM Y HH:mm') }}</td>
            </tr>
            <tr>
                <td class="label">Lokasi</td>
                <td>: {{ $event->location }}</td>
            </tr>
            <tr>
                <td class="label">Peserta</td>
                <td>: {{ $user->name }} ({{ $user->email }})</td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td>: <span class="status-paid">LUNAS / CONFIRMED</span></td>
            </tr>
        </table>

        <div class="qr-area">
            <img src="data:image/svg+xml;base64, {{ $qrcode }}" alt="QR Code" width="150">
            <p style="margin-top: 5px; font-size: 12px;">Scan QR ini di pintu masuk</p>
        </div>

        <div class="footer">
            Harap membawa identitas diri saat penukaran tiket.<br>
            Tiket ini sah dan diterbitkan oleh Sistem Event Kampus.
        </div>
    </div>
</body>
</html>
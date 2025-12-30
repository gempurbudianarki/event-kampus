<!DOCTYPE html>
<html>
<head>
    <title>E-Ticket Event UBBG</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f3f4f6;
        }
        .ticket-box {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: 1px solid #ddd;
            position: relative;
        }
        .header {
            background-color: #4f46e5; /* Indigo */
            color: white;
            padding: 20px;
            text-align: center;
            border-bottom: 3px dashed #fff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
            opacity: 0.8;
        }
        .content {
            padding: 30px;
            display: table;
            width: 100%;
        }
        .left-section {
            display: table-cell;
            width: 65%;
            vertical-align: top;
            padding-right: 20px;
            border-right: 2px dashed #eee;
        }
        .right-section {
            display: table-cell;
            width: 35%;
            vertical-align: middle;
            text-align: center;
            padding-left: 20px;
        }
        .info-group {
            margin-bottom: 20px;
        }
        .label {
            font-size: 12px;
            color: #888;
            text-transform: uppercase;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .value-large {
            font-size: 24px;
            color: #4f46e5;
            font-weight: 800;
        }
        .ticket-code {
            background: #f3f4f6;
            padding: 10px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: bold;
            color: #333;
            display: inline-block;
            margin-bottom: 10px;
        }
        .footer {
            background-color: #f9fafb;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            background-color: #d1fae5;
            color: #065f46;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <div class="ticket-box">
        <div class="header">
            <h1>E-TICKET EVENT UBBG</h1>
            <p>Tunjukkan tiket ini di meja registrasi ulang</p>
        </div>

        <div class="content">
            <div class="left-section">
                <div class="info-group">
                    <div class="label">Nama Event</div>
                    <div class="value-large">{{ $event->title }}</div>
                </div>

                <div class="info-group" style="display: table; width: 100%;">
                    <div style="display: table-cell; width: 50%;">
                        <div class="label">Tanggal & Waktu</div>
                        <div class="value">{{ $event->event_date->format('d M Y') }}</div>
                        <div class="value" style="font-size: 14px; color: #666;">{{ $event->event_date->format('H:i') }} WIB</div>
                    </div>
                    <div style="display: table-cell; width: 50%;">
                        <div class="label">Lokasi</div>
                        <div class="value">{{ $event->location }}</div>
                    </div>
                </div>

                <div class="info-group">
                    <div class="label">Nama Peserta</div>
                    <div class="value">{{ $user->name }}</div>
                    <div style="font-size: 12px; color: #888;">{{ $user->email }}</div>
                </div>
            </div>

            <div class="right-section">
                <div class="label">Kode Tiket</div>
                <div class="ticket-code">{{ $registration->ticket_code }}</div>
                
                <br><br>
                
                <div class="status-badge">
                    CONFIRMED
                </div>

                <p style="margin-top: 20px; font-size: 10px; color: #999;">
                    Tiket ini sah dan dikeluarkan otomatis oleh sistem Event UBBG.
                </p>
            </div>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Universitas Bina Bangsa Getsempena. Harap datang 15 menit sebelum acara dimulai.
        </div>
    </div>

</body>
</html>
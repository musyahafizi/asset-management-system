<!DOCTYPE html>
<html>
<head>
    <title>Laptop Asset QR</title>
    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        @media print { .no-print { display: none; } }
        body { background: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .qr-card { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 400px; text-align: center; }
        #qrcode { display: flex; justify-content: center; margin: 20px 0; }
        #qrcode img { border: 10px solid white; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        .details-box { background: #f9f9f9; padding: 15px; border-radius: 8px; font-family: monospace; text-align: left; font-size: 14px; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="qr-card">
        <h2 style="color: #214761; margin-top: 0;">LAPTOP ASSET TAG</h2>
        <hr>
        
        <div id="qrcode"></div>
        
        <div class="details-box">
            <strong>ASSET ID:</strong> {{ $id ?? 'N/A' }}<br>
            <strong>Model:</strong> {{ $model ?? 'HP' }}<br>
            <strong>S/N:</strong> {{ $sn ?? 'N/A' }}<br>
            <strong>User:</strong> {{ $user ?? 'Unknown' }}<br>
            <strong>Dept:</strong> {{ $dept ?? 'N/A' }}
        </div>
        
        <hr class="no-print">
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-primary">Print Tag</button>
            <a href="{{ route('admin.laptop') }}" class="btn btn-default">Back</a>
        </div>
    </div>

    <script type="text/javascript">
        // The data to be encoded in the QR code
        var qrData = "LAPTOP ASSET\nID: {{ $id }}\nModel: {{ $model }}\nS/N: {{ $sn }}\nUser: {{ $user }}";

        new QRCode(document.getElementById("qrcode"), {
            text: qrData,
            width: 200,
            height: 200,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    </script>
</body>
</html>
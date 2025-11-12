<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>QR Drive</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background: white;
            max-width: 500px;
            width: 100%;
            border-radius: 12px;
            padding: 32px;
            text-align: center;
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }

        h1 {
            font-size: 24px;
            margin-bottom: 4px;
        }

        p {
            margin-top: 0;
            color: #6c757d;
        }

        .error {
            background: #ffe6e6;
            padding: 16px;
            border-left: 4px solid red;
            color: #b30000;
            border-radius: 6px;
            margin-top: 20px;
        }

        .qr-box {
            margin: 28px 0;
        }
    </style>
</head>
<body>

<div class="container">
    <p>Scan kode QR untuk membuka tautan.</p>

    @php
        $url = $_GET['url'] ?? null;
    @endphp

    @if(!$url)
        <div class="error">
            <strong>Oops!</strong> Parameter <b>url</b> belum diisi.
            <br><br>
            Contoh format:<br>
            <code>?url=https://drive.google.com/.....</code>
        </div>
    @else
        <div class="qr-box">
            {!! QrCode::size(300)->generate($url) !!}
        </div>

        <p><b>URL:</b><br>{{ $url }}</p>
    @endif
</div>

</body>
</html>

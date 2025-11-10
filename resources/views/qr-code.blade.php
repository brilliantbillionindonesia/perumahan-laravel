{{-- resources/views/qr.blade.php --}}
<!doctype html>
<html>
<head><meta charset="utf-8"><title>QR Drive</title></head>
<body>
  <h3>QR Link Google Drive</h3>
    @if($_GET['url'] != null)
        <h1>param URL nya diisi dong kang</h1>
    @else
        {!! QrCode::size(300)->generate($_GET['url']) !!}
    @endif
    {{-- https://drive.google.com/uc?export=download&id=1WkE7tduTGaSVHocBMDv55C_x_5GcmlGi --}}
</body>
</html>

{{-- resources/views/qr.blade.php --}}
<!doctype html>
<html>
<head><meta charset="utf-8"><title>QR Drive</title></head>
<body>
  <h3>QR Link Google Drive</h3>
  {!! QrCode::size(300)->generate('https://drive.google.com/file/d/1WkE7tduTGaSVHocBMDv55C_x_5GcmlGi/view?usp=drive_link') !!}
</body>
</html>

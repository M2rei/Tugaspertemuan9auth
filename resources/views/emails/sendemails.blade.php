<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Email Registrasi</title>
</head>
<body>
    <h1>Email Konfirm Registrasi</h1>
    <p>Selamat Datang, {{ $data->name }},</p>
    <p>Terimakasih telah mendaftar</p>
    <p>Kami sangat senang melihat Anda bergabung dengan kami. Dibawah ini adalah informasi yang perlu Anda ketahui</p>
    <ul>
        <li><strong>Nama :</strong> {{ $data->name }}</li>
        <li><strong>Email :</strong> {{ $data->email }}</li>
        <li><strong>No telepon :</strong> {{ $data->no_telepon }}</li>
    </ul>
    <p>Terimakasih Tuan</p>
</body>
</html>
<?php
require 'koneksi.php';

if (isset($_COOKIE['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = $conn->real_escape_string($_POST['nama_lengkap']);
    $nik = $conn->real_escape_string($_POST['nik']);
    $email = $conn->real_escape_string($_POST['email']);
    $no_hp = $conn->real_escape_string($_POST['no_hp']);
    $jenis_kelamin = $conn->real_escape_string($_POST['jenis_kelamin']);
    $password = $conn->real_escape_string($_POST['password']);
    
    $cek = $conn->query("SELECT * FROM Users WHERE NIK='$nik' OR email='$email'");
    if ($cek->num_rows > 0) {
        $pesan = "<div class='alert alert-bahaya'>Pendaftaran Gagal: NIK atau Email sudah terdaftar di sistem!</div>";
    } else {
        $query = "INSERT INTO Users (nama_lengkap, NIK, email, no_hp, jenis_kelamin, password, role) 
                  VALUES ('$nama_lengkap', '$nik', '$email', '$no_hp', '$jenis_kelamin', '$password', 'warga')";
                  
        if ($conn->query($query) === TRUE) {
            $pesan = "<div class='alert alert-sukses'>Registrasi berhasil! Silakan <a href='login.php' style='text-decoration:underline; font-weight:bold;'>Login di sini</a>.</div>";
        } else {
            $pesan = "<div class='alert alert-bahaya'>Terjadi kesalahan server: " . $conn->error . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Daftar Akun - Desa Kosar</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body style="background-color: #f1f5f9; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 2rem 0;">

  <div class="kartu-form" style="max-width: 500px; width: 100%; padding: 2.5rem 2rem;">
    <div style="text-align: center; margin-bottom: 2rem;">
      <h1 style="font-size: 1.5rem; font-weight: bold; color: var(--warna-primer); margin-bottom: 0.5rem;">Registrasi Warga</h1>
      <p style="color: var(--warna-teks-muda);">Buat akun untuk mengakses layanan Desa Kosar</p>
    </div>

    <?= $pesan ?>

    <form method="POST">
      <div class="grup-form">
        <label>Nama Lengkap (Sesuai KTP)</label>
        <input type="text" name="nama_lengkap" class="input-form" required />
      </div>
      
      <div class="grup-form">
        <label>NIK (16 Digit)</label>
        <input type="text" name="nik" class="input-form" pattern="[0-9]{16}" title="NIK harus 16 digit angka" required />
      </div>
      
      <div class="grup-form">
        <label>Email Aktif</label>
        <input type="email" name="email" class="input-form" required />
      </div>
      
      <div class="grup-form">
        <label>No. WhatsApp / HP</label>
        <input type="text" name="no_hp" class="input-form" required />
      </div>
      
      <div class="grup-form">
        <label>Jenis Kelamin</label>
        <select name="jenis_kelamin" class="input-form" required>
            <option value="laki-laki">Laki-laki</option>
            <option value="perempuan">Perempuan</option>
        </select>
      </div>
      
      <div class="grup-form">
        <label>Buat Password</label>
        <input type="password" name="password" class="input-form" minlength="6" required />
      </div>

      <button type="submit" class="btn-primer" style="width: 100%; margin-top: 1rem;">Daftar Sekarang</button>
    </form>

    <p style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem;">
      Sudah memiliki akun? <a href="login.php" style="color: var(--warna-info); font-weight: bold;">Login di sini</a>
    </p>
  </div>

</body>
</html>
<?php
require 'koneksi.php';

// Jika user sudah login (berdasarkan cookie), arahkan ke dashboard
if (isset($_COOKIE['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$pesan = "";

// Jika form pendaftaran dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = $conn->real_escape_string($_POST['nama_lengkap']);
    $nik = $conn->real_escape_string($_POST['nik']);
    $email = $conn->real_escape_string($_POST['email']);
    $no_hp = $conn->real_escape_string($_POST['no_hp']);
    $jenis_kelamin = $conn->real_escape_string($_POST['jenis_kelamin']);
    $password = $conn->real_escape_string($_POST['password']);

    // Cek apakah NIK sudah pernah digunakan
    $cek = $conn->query("SELECT * FROM Users WHERE NIK='$nik'");
    if ($cek->num_rows > 0) {
        $pesan = "<div style='background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 0.9rem;'>NIK sudah terdaftar! Silakan gunakan NIK lain atau Login.</div>";
    } else {
        // [UPDATE PDM] Masukkan data ke tabel Users
        $query = "INSERT INTO Users (nama_lengkap, NIK, email, no_hp, jenis_kelamin, password, role) 
                  VALUES ('$nama_lengkap', '$nik', '$email', '$no_hp', '$jenis_kelamin', '$password', 'warga')";
        
        if ($conn->query($query) === TRUE) {
            $pesan = "<div style='background: #dcfce7; color: #16a34a; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 0.9rem;'>Pendaftaran berhasil! Silakan <a href='login.php' style='font-weight:bold;color:#15803d;'>Login di sini</a>.</div>";
        } else {
            $pesan = "<div style='background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 0.9rem;'>Error: " . $conn->error . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Daftar - NamaWeb</title>
  <link rel="stylesheet" href="../css/style.css" />
</head>
<body>

  <div class="halaman-auth">
    <div class="auth-dekorasi-1"></div>
    <div class="auth-dekorasi-2"></div>

    <div class="kartu-auth" style="max-width: 450px;">
      <div class="auth-form-panel">
        <h2>Register Akun PDM</h2>

        <?= $pesan ?>

        <form method="POST">
          <div class="grup-input">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" class="input-teks" placeholder="Sesuai KTP" required />
          </div>

          <div class="grup-input">
            <label>NIK (16 Digit)</label>
            <input type="text" name="nik" class="input-teks" placeholder="Masukkan 16 angka NIK" required pattern="[0-9]{16}" />
          </div>

          <div class="grup-input">
            <label>Email & No. HP</label>
            <div style="display:flex; gap:10px;">
                <input type="email" name="email" class="input-teks" placeholder="Email" required />
                <input type="text" name="no_hp" class="input-teks" placeholder="08xx..." required />
            </div>
          </div>

          <div class="grup-input">
            <label>Jenis Kelamin</label>
            <select name="jenis_kelamin" class="input-teks" required>
                <option value="laki-laki">Laki-laki</option>
                <option value="perempuan">Perempuan</option>
            </select>
          </div>

          <div class="grup-input">
            <label>Password</label>
            <input type="password" name="password" class="input-teks" placeholder="Minimal 6 karakter" required minlength="6" />
          </div>

          <div style="margin-top:1.25rem;">
            <button type="submit" class="btn-primer" style="width:100%;">Daftar Sekarang</button>
          </div>
        </form>

        <div class="auth-link">
          Sudah punya akun? <a href="login.php">Sign In</a>
        </div>
        <div class="auth-link" style="margin-top:0.5rem;">
          <a href="../index.php" style="color:var(--warna-teks-muda);">← Kembali ke Beranda</a>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
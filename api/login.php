<?php
require 'koneksi.php';

// Jika sudah ada cookie (sudah login), lempar ke halaman yang sesuai
if (isset($_COOKIE['user_id'])) {
    if ($_COOKIE['role'] == 'kepala_desa') header("Location: dashboard-kades.php");
    else if ($_COOKIE['role'] == 'petugas') header("Location: dashboard-petugas.php");
    else header("Location: dashboard.php");
    exit;
}

$error = "";

// Jika tombol login ditekan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nik = $conn->real_escape_string($_POST['nik']);
    $password = $_POST['password'];

    // [UPDATE PDM] Cari user di tabel Users menggunakan NIK
    $result = $conn->query("SELECT * FROM Users WHERE NIK='$nik' AND password='$password'");

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Simpan data di COOKIE (Cocok untuk Vercel Serverless)
        setcookie('user_id', $user['id_user'], time() + (86400 * 7), "/");
        setcookie('nama', $user['nama_lengkap'], time() + (86400 * 7), "/");
        setcookie('role', $user['role'], time() + (86400 * 7), "/");

        // Arahkan ke dashboard sesuai role PDM
        if ($user['role'] == 'kepala_desa') {
            header("Location: dashboard-kades.php");
        } else if ($user['role'] == 'petugas') {
            header("Location: dashboard-petugas.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;
    } else {
        $error = "NIK atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - NamaWeb</title>
  <link rel="stylesheet" href="../css/style.css" />
</head>
<body>

  <div class="halaman-auth">
    <div class="auth-dekorasi-1"></div>
    <div class="auth-dekorasi-2"></div>

    <div class="kartu-auth">
      <div class="auth-form-panel">
        <h2>Login Sistem</h2>

        <?php if($error != ""): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 0.9rem;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">

          <div class="grup-input">
            <label>NIK</label>
            <input type="text" name="nik" class="input-teks" placeholder="Masukkan 16 digit NIK" required pattern="[0-9]{16}" />
          </div>

          <div class="grup-input">
            <label>Password</label>
            <input type="password" name="password" class="input-teks" placeholder="Masukkan password" required />
          </div>

          <div style="margin-top:1.25rem;">
            <button type="submit" class="btn-primer" style="width:100%;">Masuk</button>
          </div>

        </form>

        <div class="auth-link">
          Belum punya akun? <a href="register.php">Sign Up</a>
        </div>
        
        <div class="auth-link" style="margin-top:0.5rem;">
          <a href="../index.php" style="color:var(--warna-teks-muda);">← Kembali ke Beranda</a>
        </div>
      </div>

    </div>
  </div>

</body>
</html>
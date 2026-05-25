<?php
session_start();
require 'koneksi.php';

// Jika user sudah login, arahkan kembali ke dashboard agar tidak perlu daftar lagi
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$pesan = "";

// Jika form pendaftaran dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Cek apakah username sudah pernah digunakan
    $cek = $conn->query("SELECT * FROM pengguna WHERE username='$username'");
    if ($cek->num_rows > 0) {
        $pesan = "<div style='background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 0.9rem;'>Username sudah digunakan! Silakan pilih yang lain.</div>";
    } else {
        // Masukkan data ke database dengan role otomatis 'warga'
        // Karena di form tidak ada nama lengkap, kita gunakan username sebagai nama sementara
        $query = "INSERT INTO pengguna (nama, username, password, role) VALUES ('$username', '$username', '$password', 'warga')";
        
        if ($conn->query($query) === TRUE) {
            $pesan = "<div style='background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 0.9rem;'>Pendaftaran berhasil! Silakan <a href='login.php' style='font-weight:bold;'>Login di sini</a>.</div>";
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

    <div class="kartu-auth">
      <div class="auth-form-panel">
        <h2>Register</h2>

        <?= $pesan ?>

        <form method="POST">
          <div class="grup-input">
            <label for="regUsername">Username</label>
            <input
              type="text"
              name="username"
              id="regUsername"
              class="input-teks"
              placeholder="Buat username Anda"
              required
              autocomplete="username"
            />
          </div>

          <div class="grup-input">
            <label for="regEmail">Email</label>
            <input
              type="email"
              name="email"
              id="regEmail"
              class="input-teks"
              placeholder="Masukkan email aktif Anda"
              required
              autocomplete="email"
            />
          </div>

          <div class="grup-input">
            <label for="regPassword">Password</label>
            <input
              type="password"
              name="password"
              id="regPassword"
              class="input-teks"
              placeholder="Minimal 6 karakter"
              required
              autocomplete="new-password"
            />
          </div>

          <div style="margin-top:1.25rem;">
            <button type="submit" class="btn-primer" style="width:100%;">Register</button>
          </div>
        </form>

        <div class="auth-link">
          Sudah punya akun? <a href="login.php">Sign In</a>
        </div>

        <div class="auth-link" style="margin-top:0.5rem;">
          <a href="../index.html" style="color:var(--warna-teks-muda);">← Kembali ke Beranda</a>
        </div>
      </div>

      <div class="auth-sambutan-panel">
        <h3>Welcome<br />Back!</h3>
        <p>
          We're delighted to have you here.
          If you need any assistance, feel free to reach out.
        </p>
      </div>

    </div>
  </div>

  <script src="../js/main.js"></script>
</body>
</html>
<?php
require 'koneksi.php';

$error = "";

// Jika tombol login ditekan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Cari user di database
    $result = $conn->query("SELECT * FROM pengguna WHERE username='$username' AND password='$password'");
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // GUNAKAN COOKIE (Aman untuk Vercel Serverless)
        setcookie('user_id', $user['id'], time() + (86400 * 7), "/"); // Aktif 7 hari
        setcookie('nama', $user['nama'], time() + (86400 * 7), "/");
        setcookie('role', $user['role'], time() + (86400 * 7), "/");

        // Arahkan ke dashboard sesuai role
        if ($user['role'] == 'kades') {
            header("Location: dashboard-kades.php");
        } else if ($user['role'] == 'petugas') {
            header("Location: dashboard-petugas.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;
    } else {
        $error = "Username atau Password salah!";
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
        <h2>Login</h2>

        <?php if($error != ""): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 0.9rem;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">

          <div class="grup-input">
            <label for="loginUsername">Username</label>
            <input
              type="text"
              name="username"
              id="loginUsername"
              class="input-teks"
              placeholder="Gunakan: warga / petugas / kades"
              required
              autocomplete="username"
            />
          </div>

          <div class="grup-input">
            <label for="loginPassword">Password</label>
            <input
              type="password"
              name="password"
              id="loginPassword"
              class="input-teks"
              placeholder="Pass: admin123"
              required
              autocomplete="current-password"
            />
          </div>

          <div style="margin-top:1.25rem;">
            <button type="submit" class="btn-primer" style="width:100%;">Login</button>
          </div>

        </form>

        <div class="auth-link">
          Belum punya akun? <a href="register.php">Sign Up</a>
        </div>
        
        <div class="auth-link" style="margin-top:0.5rem;">
          <a href="../index.html" style="color:var(--warna-teks-muda);">← Kembali ke Beranda</a>
        </div>
      </div>

      <div class="auth-sambutan-panel">
        <h3>Welcome!</h3>
        <p>
          We are happy to have you with us again.
          If you need anything, we are here to help.
        </p>
      </div>

    </div>
  </div>

  <script src="../js/main.js"></script>
</body>
</html>
<?php
require 'koneksi.php';

if (isset($_COOKIE['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    $query = $conn->query("SELECT * FROM Users WHERE email='$email' OR NIK='$email'");
    
    if ($query->num_rows > 0) {
        $user = $query->fetch_assoc();
        if ($password == $user['password']) {
            setcookie('user_id', $user['id_user'], time() + (86400 * 30), "/");
            setcookie('role', $user['role'], time() + (86400 * 30), "/");
            setcookie('nama', $user['nama_lengkap'], time() + (86400 * 30), "/");

            if ($user['role'] == 'kepala_desa') {
                header("Location: dashboard-kades.php");
            } elseif ($user['role'] == 'petugas') {
                header("Location: dashboard-petugas.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        } else {
            $error = "Password yang Anda masukkan salah!";
        }
    } else {
        $error = "Akun tidak ditemukan! Silakan daftar terlebih dahulu.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Desa Kosar</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body style="background-color: #f1f5f9; display: flex; align-items: center; justify-content: center; min-height: 100vh;">

  <div class="kartu-form" style="max-width: 400px; width: 100%; padding: 2.5rem 2rem;">
    <div style="text-align: center; margin-bottom: 2rem;">
      <h1 style="font-size: 1.5rem; font-weight: bold; color: var(--warna-primer); margin-bottom: 0.5rem;">Desa Kosar</h1>
      <p style="color: var(--warna-teks-muda);">Sistem Layanan Administrasi</p>
    </div>

    <?php if($error): ?>
        <div class="alert alert-bahaya"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="grup-form">
        <label>Email atau NIK</label>
        <input type="text" name="email" class="input-form" placeholder="Masukkan Email atau NIK" required />
      </div>
      
      <div class="grup-form">
        <label>Password</label>
        <input type="password" name="password" class="input-form" placeholder="Masukkan Password" required />
      </div>

      <button type="submit" class="btn-primer" style="width: 100%; margin-top: 1rem;">Masuk Akun</button>
    </form>

    <p style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem;">
      Belum punya akun? <a href="register.php" style="color: var(--warna-info); font-weight: bold;">Daftar di sini</a>
    </p>
  </div>

</body>
</html>
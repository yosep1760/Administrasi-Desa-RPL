<?php
require 'koneksi.php';

if (!isset($_COOKIE['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = (int)$_COOKIE['user_id'];
$query = $conn->query("SELECT * FROM Users WHERE id_user = $id_user");
$user = $query->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Profil Saya - Desa Kosar</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <div class="layout-dashboard">
    <?php include 'sidebar.php'; ?>

    <div class="konten-dashboard">
      <header class="header-dashboard">
        <div style="display:flex;align-items:center;gap:0.75rem;">
          <button id="tombolBukaSidebar" class="tombol-hamburger" style="display:flex;"><span></span><span></span><span></span></button>
          <div class="header-pengguna">
            <h3>Halo, <?= htmlspecialchars($user['nama_lengkap']) ?></h3>
            <span style="text-transform: capitalize;"><?= str_replace('_', ' ', $user['role']) ?></span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;"><button type="submit" class="avatar-pengguna">👤</button></form>
      </header>

      <main class="area-konten">
        <h1 style="font-family:var(--font-judul);font-size:1.8rem;font-weight:700;margin-bottom:2rem;">Profil Saya</h1>

        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:2.5rem; flex-wrap:wrap; gap:1rem;">
            <div style="display:flex; align-items:center; gap:1.5rem;">
                <div style="width:100px; height:100px; border-radius:50%; background:#d1d5db; display:flex; align-items:center; justify-content:center; font-size:1.2rem; font-weight:bold; color:#4b5563;">
                    *Foto
                </div>
                <div>
                    <h2 style="font-size:1.5rem; font-weight:700; margin-bottom:0.2rem;"><?= htmlspecialchars($user['nama_lengkap']) ?></h2>
                    <p style="color:#6b7280; text-transform:capitalize;"><?= str_replace('_', ' ', $user['role']) ?> Desa Kosar</p>
                </div>
            </div>
            <button class="btn-sekunder" style="background:#9ca3af; color:white; border:none; border-radius:8px;">✏ Edit Profil</button>
        </div>

        <h3 style="font-size:1.2rem; font-weight:700; margin-bottom:1rem;">Informasi Akun</h3>
        
        <div style="background:#e5e7eb; border-radius:12px; padding:2rem; max-width:700px;">
            <table style="width:100%; border-collapse:collapse; font-size:1rem;">
                <tr>
                    <td style="padding:12px 0; width:40%; color:#374151;">Nomor Telepon:</td>
                    <td style="padding:12px 0; font-weight:500;"><?= htmlspecialchars($user['no_hp']) ?></td>
                </tr>
                <tr>
                    <td style="padding:12px 0; color:#374151;">NIK Identitas:</td>
                    <td style="padding:12px 0; font-weight:500;"><?= htmlspecialchars($user['NIK']) ?></td>
                </tr>
                <tr>
                    <td style="padding:12px 0; color:#374151;">Jenis Kelamin:</td>
                    <td style="padding:12px 0; font-weight:500; text-transform:capitalize;"><?= htmlspecialchars($user['jenis_kelamin']) ?></td>
                </tr>
                <tr>
                    <td style="padding:12px 0; color:#374151;">Email Aktif:</td>
                    <td style="padding:12px 0; font-weight:500;"><?= htmlspecialchars($user['email']) ?></td>
                </tr>
                <tr>
                    <td style="padding:12px 0; color:#374151;">Password:</td>
                    <td style="padding:12px 0; font-weight:500; display:flex; align-items:center; gap:1rem;">
                        ************** <button class="btn-sekunder btn-kecil" style="background:#9ca3af; color:white; border:none; padding:4px 10px; border-radius:6px;">👁 Lihat</button>
                    </td>
                </tr>
            </table>
        </div>

      </main>
    </div>
  </div>
  <script src="js/main.js"></script>
</body>
</html>
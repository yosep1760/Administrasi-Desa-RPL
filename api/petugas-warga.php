<?php
require 'koneksi.php';

if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'petugas') {
    header("Location: login.php");
    exit;
}
$nama_petugas = $_COOKIE['nama'];

if (isset($_GET['hapus_id'])) {
    $id_hapus = (int)$_GET['hapus_id'];
    $conn->query("DELETE FROM Users WHERE id_user = $id_hapus");
    header("Location: petugas-warga.php");
    exit;
}

$data_warga = $conn->query("SELECT * FROM Users WHERE role='warga' ORDER BY id_user DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Penduduk - Desa Kosar</title>
  <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
  <div class="layout-dashboard">
    <?php include 'sidebar.php'; ?>

    <div class="konten-dashboard">
      <header class="header-dashboard">
        <div style="display:flex;align-items:center;gap:0.75rem;">
          <button id="tombolBukaSidebar" class="tombol-hamburger" style="display:flex;"><span></span><span></span><span></span></button>
          <div class="header-pengguna">
            <h3>Halo, <?= htmlspecialchars($nama_petugas) ?></h3>
            <span>Petugas Desa Kosar</span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;"><button type="submit" class="avatar-pengguna">👤</button></form>
      </header>

      <main class="area-konten">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
          <div>
            <h1 style="font-family:var(--font-judul);font-size:1.8rem;font-weight:700;">Data Penduduk</h1>
            <p style="color:var(--warna-teks-muda);">Kelola akun warga desa terdaftar.</p>
          </div>
          <!-- TOMBOL PINDAH HALAMAN (POIN 7) -->
          <a href="tambah-warga.php" class="btn-primer" style="background:#000;">+ Tambah Penduduk</a>
        </div>

        <div class="kartu-tabel">
          <table class="tabel-data">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>NIK</th>
                <th>No. HP</th>
                <th>Gender</th>
                <th style="text-align:center;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($data_warga->num_rows > 0): ?>
                  <?php $no = 1; while($row = $data_warga->fetch_assoc()): ?>
                    <tr>
                      <td><?= $no++ ?></td>
                      <td><strong><?= htmlspecialchars($row['nama_lengkap']) ?></strong></td>
                      <td><?= htmlspecialchars($row['NIK']) ?></td>
                      <td><?= htmlspecialchars($row['no_hp']) ?></td>
                      <td style="text-transform: capitalize;"><?= htmlspecialchars($row['jenis_kelamin']) ?></td>
                      <td style="text-align:center;">
                          <a href="?hapus_id=<?= $row['id_user'] ?>" class="btn-sekunder btn-kecil" style="color:#ef4444; border-color:#ef4444; text-decoration:none;" onclick="return confirm('PERINGATAN! Yakin hapus akun warga ini beserta semua suratnya?');">Hapus</a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
              <?php else: ?>
                  <tr><td colspan="6" style="text-align:center; padding: 20px;">Belum ada data warga.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>
  <script src="../js/main.js"></script>
</body>
</html>
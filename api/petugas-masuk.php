<?php
require 'koneksi.php';

if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'petugas') {
    header("Location: login.php");
    exit;
}
$nama_petugas = $_COOKIE['nama'];

// LOGIKA TERUSKAN & TOLAK (Dipanggil dari halaman detail-surat.php atau dari sini)
if (isset($_GET['teruskan_id'])) {
    $id_pengajuan = (int)$_GET['teruskan_id'];
    $conn->query("UPDATE Pengajuan_Surat SET status='menunggu_persetujuan' WHERE id_pengajuan=$id_pengajuan");
    header("Location: petugas-masuk.php");
    exit;
}
if (isset($_GET['tolak_id']) && isset($_GET['catatan'])) {
    $id_pengajuan = (int)$_GET['tolak_id'];
    $catatan = $conn->real_escape_string($_GET['catatan']);
    $conn->query("UPDATE Pengajuan_Surat SET status='ditolak', catatan_petugas='$catatan' WHERE id_pengajuan=$id_pengajuan");
    header("Location: petugas-masuk.php");
    exit;
}

// Ambil surat yang statusnya "menunggu_verifikasi"
$query = "SELECT ps.*, u.nama_lengkap AS nama_warga, js.nama_surat FROM Pengajuan_Surat ps JOIN Users u ON ps.id_user = u.id_user JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis WHERE ps.status = 'menunggu_verifikasi' ORDER BY ps.tanggal_pengajuan ASC"; 
$data_surat = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Surat Masuk - Desa Kosar</title>
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
            <h3>Halo, <?= htmlspecialchars($nama_petugas) ?></h3>
            <span>Petugas Administrasi</span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;"><button type="submit" class="avatar-pengguna" title="Keluar">👤</button></form>
      </header>

      <main class="area-konten">
        <h1 style="font-family:var(--font-judul);font-size:1.8rem;font-weight:700;margin-bottom:1.5rem;">Surat yang Diajukan</h1>

        <!-- Header Pencarian Sesuai Figma -->
        <div style="display:flex; gap:1rem; margin-bottom:1.5rem; flex-wrap:wrap;">
            <input type="text" class="input-form" placeholder="Cari Nama Pemohon..." style="flex:1; max-width:500px;">
            <button class="btn-sekunder" style="background:transparent; border:1px solid #000; color:#000;">≡ Jenis Surat</button>
        </div>

        <div class="kartu-tabel">
          <table class="tabel-data">
            <thead>
              <tr style="background-color: #d1d5db;">
                <th>#</th>
                <th>Pemohon</th>
                <th>Jenis Surat</th>
                <th>Tanggal</th>
                <th style="text-align:center;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($data_surat->num_rows > 0): ?>
                  <?php $no = 1; while($row = $data_surat->fetch_assoc()): ?>
                    <tr>
                      <td><?= $no++ ?></td>
                      <td><strong><?= htmlspecialchars($row['nama_warga']) ?></strong></td>
                      <td><?= htmlspecialchars($row['nama_surat']) ?></td>
                      <td><?= date('d - m - Y', strtotime($row['tanggal_pengajuan'])) ?></td>
                      <td style="text-align:center;">
                          <!-- Tombol Lihat Figma Style -->
                          <a href="detail-surat.php?id=<?= $row['id_pengajuan'] ?>" class="btn-sekunder btn-kecil" style="background:#9ca3af; color:white; border:none; border-radius:6px; text-decoration:none;">👁 Lihat</a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
              <?php else: ?>
                  <tr><td colspan="5" style="text-align:center; padding: 20px;">Antrean kosong. Belum ada surat masuk.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>
  <script src="js/main.js"></script>
</body>
</html>
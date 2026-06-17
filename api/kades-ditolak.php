<?php
require 'koneksi.php';

if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'kepala_desa') {
    header("Location: login.php");
    exit;
}

$nama_kades = $_COOKIE['nama'];

$query = "SELECT ps.*, u.nama_lengkap AS nama_warga, js.nama_surat FROM Pengajuan_Surat ps JOIN Users u ON ps.id_user = u.id_user JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis WHERE ps.status = 'ditolak' AND ps.catatan_kades IS NOT NULL AND ps.catatan_kades != '' ORDER BY ps.tanggal_pengajuan DESC";
$data_surat = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Surat Ditolak - Desa Kosar</title>
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
            <h3>Halo, <?= htmlspecialchars($nama_kades) ?></h3>
            <span>Kepala Desa Kosar</span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;"><button type="submit" class="avatar-pengguna" title="Keluar">👤</button></form>
      </header>

      <main class="area-konten">
        <h1 style="font-family:var(--font-judul);font-size:1.8rem;font-weight:700;margin-bottom:1.5rem;">Riwayat Surat Ditolak</h1>

        <div class="kartu-tabel">
          <table class="tabel-data">
            <thead>
              <tr style="background-color: #d1d5db;">
                <th>#</th>
                <th>Pemohon</th>
                <th>Jenis Surat</th>
                <th>Tanggal Pengajuan</th>
                <th>Alasan Penolakan Anda</th>
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
                      <td>
                          <span style="color:#ef4444; font-weight:bold;">✖ Ditolak</span><br>
                          <span style="font-size:0.85rem; color:#4b5563;"><?= htmlspecialchars($row['catatan_kades']) ?></span>
                      </td>
                    </tr>
                  <?php endwhile; ?>
              <?php else: ?>
                  <tr><td colspan="5" style="text-align:center; padding: 20px;">Belum ada riwayat surat yang Anda tolak.</td></tr>
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
<?php
require 'koneksi.php';

if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'petugas') {
    header("Location: login.php");
    exit;
}
$nama_petugas = $_COOKIE['nama'];

// Statistik Petugas
$c_masuk = $conn->query("SELECT COUNT(*) as c FROM Pengajuan_Surat WHERE status='menunggu_verifikasi'")->fetch_assoc()['c'];
$c_proses = $conn->query("SELECT COUNT(*) as c FROM Pengajuan_Surat WHERE status='menunggu_persetujuan'")->fetch_assoc()['c'];
$c_selesai = $conn->query("SELECT COUNT(*) as c FROM Pengajuan_Surat WHERE status='selesai'")->fetch_assoc()['c'];

// Antrean Surat Masuk Terbaru
$q_masuk = $conn->query("SELECT ps.*, u.nama_lengkap, js.nama_surat FROM Pengajuan_Surat ps JOIN Users u ON ps.id_user = u.id_user JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis WHERE ps.status = 'menunggu_verifikasi' ORDER BY ps.tanggal_pengajuan ASC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Petugas - Desa Kosar</title>
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
        <h1 style="font-family:var(--font-judul);font-size:1.8rem;font-weight:700;margin-bottom:1.5rem;">Dashboard Petugas</h1>

        <div class="grid-statistik">
          <div class="kartu-statistik">
            <div class="statistik-angka" style="color: #ef4444;"><?= $c_masuk ?> 📩</div>
            <div class="statistik-label">Surat Baru Masuk</div>
          </div>
          <div class="kartu-statistik">
            <div class="statistik-angka" style="color: #f59e0b;"><?= $c_proses ?> ⏳</div>
            <div class="statistik-label">Menunggu Kades</div>
          </div>
          <div class="kartu-statistik">
            <div class="statistik-angka" style="color: #10b981;"><?= $c_selesai ?> ✅</div>
            <div class="statistik-label">Total Selesai</div>
          </div>
        </div>

        <h3 style="margin-top:2rem; margin-bottom:1rem; font-weight:700;">Antrean Verifikasi Berkas (Terlama ke Terbaru)</h3>
        
        <div class="kartu-tabel">
          <table class="tabel-data">
            <thead>
              <tr>
                <th>No</th>
                <th>Pemohon</th>
                <th>Jenis Surat</th>
                <th>Tanggal Masuk</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if($q_masuk->num_rows > 0): ?>
                  <?php $no=1; while($row = $q_masuk->fetch_assoc()): ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= htmlspecialchars($row['nama_lengkap']) ?></strong></td>
                    <td><?= htmlspecialchars($row['nama_surat']) ?></td>
                    <td><?= date('d M Y, H:i', strtotime($row['tanggal_pengajuan'])) ?></td>
                    <td>
                        <a href="detail-surat.php?id=<?= $row['id_pengajuan'] ?>" class="btn-primer btn-kecil" style="background:#000;">Verifikasi Berkas</a>
                    </td>
                  </tr>
                  <?php endwhile; ?>
              <?php else: ?>
                  <tr><td colspan="5" style="text-align:center; padding:2rem;">Tidak ada antrean surat baru.</td></tr>
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
<?php
require 'koneksi.php';

if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'warga') {
    header("Location: login.php");
    exit;
}

$id_user = $_COOKIE['user_id'];
$nama_warga = $_COOKIE['nama'];

// Mengambil Data Statistik Warga
$c_total = $conn->query("SELECT COUNT(*) as c FROM Pengajuan_Surat WHERE id_user=$id_user")->fetch_assoc()['c'];
$c_proses = $conn->query("SELECT COUNT(*) as c FROM Pengajuan_Surat WHERE id_user=$id_user AND status IN ('menunggu_verifikasi', 'menunggu_persetujuan')")->fetch_assoc()['c'];
$c_selesai = $conn->query("SELECT COUNT(*) as c FROM Pengajuan_Surat WHERE id_user=$id_user AND status='selesai'")->fetch_assoc()['c'];

// Mengambil Riwayat 5 Pengajuan Terbaru
$q_riwayat = $conn->query("SELECT ps.*, js.nama_surat FROM Pengajuan_Surat ps JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis WHERE ps.id_user = $id_user ORDER BY ps.tanggal_pengajuan DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Warga - Desa Kosar</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>

  <div class="layout-dashboard">
    <?php include 'sidebar.php'; ?>

    <div class="konten-dashboard">
      <header class="header-dashboard">
        <div style="display:flex;align-items:center;gap:0.75rem;">
          <button id="tombolBukaSidebar" class="tombol-hamburger" style="display:flex;">
            <span></span><span></span><span></span>
          </button>
          <div class="header-pengguna">
            <h3>Halo, <?= htmlspecialchars($nama_warga) ?></h3>
            <span>Warga Desa</span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;">
            <button type="submit" class="avatar-pengguna" title="Keluar dari akun">👤</button>
        </form>
      </header>

      <main class="area-konten">
        <h1 style="font-family:var(--font-judul);font-size:1.8rem;font-weight:700;margin-bottom:1.5rem;">Dashboard Saya</h1>

        <div class="grid-statistik">
          <div class="kartu-statistik">
            <div class="statistik-angka"><?= $c_total ?> 📄</div>
            <div class="statistik-label">Total Pengajuan</div>
          </div>
          <div class="kartu-statistik">
            <div class="statistik-angka" style="color: #f59e0b;"><?= $c_proses ?> ⏳</div>
            <div class="statistik-label">Sedang Diproses</div>
          </div>
          <div class="kartu-statistik">
            <div class="statistik-angka" style="color: #10b981;"><?= $c_selesai ?> ✅</div>
            <div class="statistik-label">Surat Selesai</div>
          </div>
        </div>

        <h3 style="margin-top:2rem; margin-bottom:1rem; font-weight:700;">Pengajuan Terbaru</h3>
        
        <div class="kartu-tabel">
          <table class="tabel-data">
            <thead>
              <tr>
                <th>No</th>
                <th>Jenis Surat</th>
                <th>Tanggal Masuk</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if($q_riwayat->num_rows > 0): ?>
                  <?php $no=1; while($row = $q_riwayat->fetch_assoc()): ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= htmlspecialchars($row['nama_surat']) ?></strong></td>
                    <td><?= date('d M Y', strtotime($row['tanggal_pengajuan'])) ?></td>
                    <td style="text-transform: capitalize;">
                        <span class="badge badge-<?= str_replace('_', '-', $row['status']) ?>">
                            <?= str_replace('_', ' ', $row['status']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="detail-surat.php?id=<?= $row['id_pengajuan'] ?>" class="btn-sekunder btn-kecil">Cek Detail</a>
                    </td>
                  </tr>
                  <?php endwhile; ?>
              <?php else: ?>
                  <tr>
                    <td colspan="5" style="text-align:center; padding:2rem;">Anda belum pernah mengajukan surat.</td>
                  </tr>
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
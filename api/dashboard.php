<?php
require 'koneksi.php';

// Cek COOKIE
if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'warga') {
    header("Location: login.php");
    exit;
}

$id_user = (int)$_COOKIE['user_id'];
$nama_warga = $_COOKIE['nama'];

// [UPDATE PDM] Ambil data statistik dari tabel Pengajuan_Surat sesuai ENUM
$q_total = $conn->query("SELECT COUNT(*) as total FROM Pengajuan_Surat WHERE id_user = $id_user")->fetch_assoc()['total'];
$q_proses = $conn->query("SELECT COUNT(*) as proses FROM Pengajuan_Surat WHERE id_user = $id_user AND status = 'menunggu_verifikasi'")->fetch_assoc()['proses'];
$q_kades = $conn->query("SELECT COUNT(*) as kades FROM Pengajuan_Surat WHERE id_user = $id_user AND status = 'menunggu_persetujuan'")->fetch_assoc()['kades'];
$q_setuju = $conn->query("SELECT COUNT(*) as setuju FROM Pengajuan_Surat WHERE id_user = $id_user AND status IN ('disetujui', 'selesai')")->fetch_assoc()['setuju'];

// [UPDATE PDM] Ambil 3 pengajuan terakhir (JOIN dengan Jenis_Surat)
$q_riwayat = $conn->query("
    SELECT ps.*, js.nama_surat 
    FROM Pengajuan_Surat ps
    JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis
    WHERE ps.id_user = $id_user 
    ORDER BY ps.tanggal_pengajuan DESC LIMIT 3
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - NamaWeb</title>
  <link rel="stylesheet" href="../css/style.css" />
</head>
<body>

  <div class="layout-dashboard">
    <?php include 'sidebar.php'; ?>
    <div class="konten-dashboard">
      <header class="header-dashboard">
        <div style="display:flex;align-items:center;gap:0.75rem;">
          <button id="tombolBukaSidebar" class="tombol-hamburger" aria-label="Buka menu" style="display:flex;">
            <span></span><span></span><span></span>
          </button>
          <div class="header-pengguna">
            <h3>Halo, <?= htmlspecialchars($nama_warga) ?></h3>
            <span>Warga Desa</span>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:0.75rem;">
          <form action="logout.php" method="POST" style="margin: 0;">
            <button type="submit" class="avatar-pengguna" title="Keluar dari akun" aria-label="Logout" onclick="return confirm('Yakin ingin keluar?');" style="cursor: pointer;">👤</button>
          </form>
        </div>
      </header>

      <main class="area-konten">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
          <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;">Selamat Datang</h1>
          <a href="pengajuan.php" class="btn-primer btn-kecil">+ Ajukan Surat</a>
        </div>

        <div class="grid-statistik">
          <div class="kartu-statistik">
            <div class="statistik-angka"><?= $q_total ?> <span class="statistik-naik">↑</span></div>
            <div class="statistik-label">Total Pengajuan</div>
          </div>
          <div class="kartu-statistik">
            <div class="statistik-angka"><?= $q_proses ?> <span class="statistik-naik">↑</span></div>
            <div class="statistik-label">Verifikasi Petugas</div>
          </div>
          <div class="kartu-statistik">
            <div class="statistik-angka"><?= $q_kades ?> <span class="statistik-naik">↑</span></div>
            <div class="statistik-label">Persetujuan Kades</div>
          </div>
          <div class="kartu-statistik">
            <div class="statistik-angka"><?= $q_setuju ?> <span class="statistik-naik">↑</span></div>
            <div class="statistik-label">Selesai/Disetujui</div>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1.5fr 1fr;gap:1.25rem;">
          <div class="kartu-tabel">
            <div class="kartu-tabel-header">
              <h3>Pengajuan Terakhir Saya</h3>
              <a href="riwayat.php">Lihat semua &rarr;</a>
            </div>
            <table class="tabel-data">
              <thead>
                <tr>
                  <th>Jenis Surat</th>
                  <th>Tanggal</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($q_riwayat->num_rows > 0): ?>
                  <?php while($row = $q_riwayat->fetch_assoc()): ?>
                    <?php 
                      // Terjemahkan ENUM PDM
                      $status_db = $row['status'];
                      $badgeClass = 'badge-menunggu';
                      $status_text = 'Menunggu Petugas';

                      if ($status_db == 'menunggu_persetujuan') { $badgeClass = 'badge-verifikasi'; $status_text = 'Proses Kades'; }
                      elseif ($status_db == 'disetujui') { $badgeClass = 'badge-verifikasi'; $status_text = 'Disetujui (Cetak)'; }
                      elseif ($status_db == 'selesai') { $badgeClass = 'badge-disetujui'; $status_text = 'Selesai'; }
                      elseif ($status_db == 'ditolak') { $badgeClass = 'badge-ditolak'; $status_text = 'Ditolak'; }
                    ?>
                    <tr>
                      <td><?= htmlspecialchars($row['nama_surat']) ?></td>
                      <td><?= date('d M Y', strtotime($row['tanggal_pengajuan'])) ?></td>
                      <td><span class="badge <?= $badgeClass ?>"><?= $status_text ?></span></td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3" style="text-align: center; padding:20px;">Belum ada pengajuan.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <div class="panel-notifikasi">
            <div class="panel-notifikasi-header">
              <h3>Pemberitahuan</h3>
              <a href="#">Lihat semua &rarr;</a>
            </div>
            <div class="item-notifikasi">
              <span class="notif-titik notif-biru"></span>
              Sistem telah diperbarui ke struktur PDM Relasional.
            </div>
          </div>
        </div>

      </main>
    </div>
  </div>

  <script src="../js/main.js"></script>
</body>
</html>
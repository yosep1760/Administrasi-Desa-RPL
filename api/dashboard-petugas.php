<?php
require 'koneksi.php';

if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'petugas') {
    header("Location: login.php");
    exit;
}

$nama_petugas = $_COOKIE['nama'];

// [UPDATE] Hitung statistik sesuai nama status baru di Activity Diagram
$c_menunggu = $conn->query("SELECT COUNT(*) as c FROM surat WHERE status='Menunggu Verifikasi Petugas'")->fetch_assoc()['c'];
$c_diproses = $conn->query("SELECT COUNT(*) as c FROM surat WHERE status IN ('Menunggu Approval Kepala desa', 'Disetujui')")->fetch_assoc()['c'];
$c_selesai = $conn->query("SELECT COUNT(*) as c FROM surat WHERE status='Selesai'")->fetch_assoc()['c'];

// Ambil 5 pengajuan terbaru
$query = "SELECT surat.*, pengguna.nama AS nama_warga 
          FROM surat 
          JOIN pengguna ON surat.id_warga = pengguna.id 
          WHERE surat.status = 'Menunggu Verifikasi Petugas'
          ORDER BY surat.tanggal ASC LIMIT 5";
$data_surat = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Petugas - NamaWeb</title>
  <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
  <div class="layout-dashboard">
    <div id="overlaySidebar" class="overlay-sidebar"></div>

    <aside id="sidebar" class="sidebar">
      <div class="sidebar-header">*Logo + NamaWeb</div>
      <div class="sidebar-cari">
        <input type="search" class="input-cari" placeholder="Search" />
      </div>
      <nav class="sidebar-nav">
        <div class="sidebar-label">Dashboard <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="dashboard-petugas.php" class="sidebar-link aktif"><span class="sidebar-link-ikon">🏠</span>Home</a>
        </div>
        <div class="sidebar-label">Layanan <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="petugas-masuk.php" class="sidebar-link"><span class="sidebar-link-ikon">📩</span>Surat Masuk</a>
          <a href="petugas-diproses.php" class="sidebar-link"><span class="sidebar-link-ikon">⏳</span>Sedang Diproses</a>
          <a href="petugas-upload.php" class="sidebar-link"><span class="sidebar-link-ikon">📤</span>Upload Surat (Selesai)</a>
          <a href="petugas-ditolak.php" class="sidebar-link"><span class="sidebar-link-ikon">❌</span>Surat Ditolak</a>
        </div>
        <div class="sidebar-label">Kelola Data <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="petugas-warga.php" class="sidebar-link"><span class="sidebar-link-ikon">👥</span>Data Warga</a>
        </div>
        <div class="sidebar-label">Pengaturan <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="profil.php" class="sidebar-link"><span class="sidebar-link-ikon">👤</span>Profil Saya</a>
        </div>
      </nav>
    </aside>

    <div class="konten-dashboard">
      <header class="header-dashboard">
        <div style="display:flex;align-items:center;gap:0.75rem;">
          <button id="tombolBukaSidebar" class="tombol-hamburger" style="display:flex;"><span></span><span></span><span></span></button>
          <div class="header-pengguna">
            <h3>Halo, <?= htmlspecialchars($nama_petugas) ?></h3>
            <span>Petugas Desa</span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;">
            <button type="submit" class="avatar-pengguna" title="Keluar" onclick="return confirm('Yakin ingin keluar?');" style="cursor: pointer;">👤</button>
        </form>
      </header>

      <main class="area-konten">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
          <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;">Dashboard Petugas</h1>
        </div>

        <div class="grid-statistik" style="margin-bottom:2rem;">
          <div class="kartu-statistik">
            <div class="ikon-statistik" style="background:var(--warna-primer-pudar);color:var(--warna-primer);">📩</div>
            <div>
              <h3 style="font-size:1.5rem;font-weight:700;margin-bottom:0.25rem;"><?= $c_menunggu ?></h3>
              <p style="color:var(--warna-teks-muda);font-size:0.875rem;">Surat Masuk</p>
            </div>
          </div>
          <div class="kartu-statistik">
            <div class="ikon-statistik" style="background:#fef3c7;color:#d97706;">⏳</div>
            <div>
              <h3 style="font-size:1.5rem;font-weight:700;margin-bottom:0.25rem;"><?= $c_diproses ?></h3>
              <p style="color:var(--warna-teks-muda);font-size:0.875rem;">Sedang Diproses Kades</p>
            </div>
          </div>
          <div class="kartu-statistik">
            <div class="ikon-statistik" style="background:#dcfce7;color:#16a34a;">✅</div>
            <div>
              <h3 style="font-size:1.5rem;font-weight:700;margin-bottom:0.25rem;"><?= $c_selesai ?></h3>
              <p style="color:var(--warna-teks-muda);font-size:0.875rem;">Selesai Diupload</p>
            </div>
          </div>
        </div>

        <div class="kartu-tabel">
          <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--warna-border); display:flex; justify-content:space-between; align-items:center;">
            <h3 style="font-size:1rem; font-weight:700;">Antrean Surat Masuk Terbaru</h3>
            <a href="petugas-masuk.php" style="font-size:0.85rem; color:var(--warna-primer); text-decoration:none;">Lihat semua →</a>
          </div>
          <table class="tabel-data">
            <thead>
              <tr>
                <th>No</th>
                <th>Pemohon</th>
                <th>Jenis Surat</th>
                <th>Tanggal</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($data_surat->num_rows > 0): ?>
                  <?php $no = 1; while($row = $data_surat->fetch_assoc()): ?>
                    <tr>
                      <td><?= $no++ ?></td>
                      <td><strong><?= htmlspecialchars($row['nama_warga']) ?></strong></td>
                      <td><?= htmlspecialchars($row['jenis_surat']) ?></td>
                      <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                      <td><span class="badge badge-menunggu">Menunggu Verifikasi</span></td>
                    </tr>
                  <?php endwhile; ?>
              <?php else: ?>
                  <tr>
                    <td colspan="5" style="text-align:center; padding: 20px;">Belum ada antrean surat masuk terbaru.</td>
                  </tr>
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
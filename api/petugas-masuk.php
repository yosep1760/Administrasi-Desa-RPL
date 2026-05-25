<?php
require 'koneksi.php';

// Lindungi halaman: Pastikan yang login HANYA PETUGAS
if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'petugas') {
    header("Location: login.php");
    exit;
}

$nama_petugas = $_COOKIE['nama'];

// Logika Petugas: Meneruskan ke Kades
if (isset($_GET['teruskan_id'])) {
    $id_surat = (int)$_GET['teruskan_id'];
    // Ubah status menjadi butuh persetujuan Kades
    $conn->query("UPDATE surat SET status='Persetujuan Kades' WHERE id=$id_surat");
    header("Location: petugas-masuk.php");
    exit;
}

// Logika Petugas: Menolak Surat
if (isset($_GET['tolak_id'])) {
    $id_surat = (int)$_GET['tolak_id'];
    $conn->query("UPDATE surat SET status='Ditolak' WHERE id=$id_surat");
    header("Location: petugas-masuk.php");
    exit;
}

// Ambil HANYA pengajuan yang baru masuk (Menunggu)
$query = "SELECT surat.*, pengguna.nama AS nama_warga 
          FROM surat 
          JOIN pengguna ON surat.id_warga = pengguna.id 
          WHERE surat.status = 'Menunggu'
          ORDER BY surat.tanggal ASC"; // Yang paling lama mengantre di atas
$data_surat = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Surat Masuk - NamaWeb</title>
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
          <a href="dashboard-petugas.php" class="sidebar-link"><span class="sidebar-link-ikon">🏠</span>Home</a>
        </div>
        <div class="sidebar-label">Layanan <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="petugas-masuk.php" class="sidebar-link aktif"><span class="sidebar-link-ikon">📩</span>Surat Masuk</a>
          <a href="petugas-diproses.php" class="sidebar-link"><span class="sidebar-link-ikon">⏳</span>Sedang Diproses</a>
          <a href="petugas-ditolak.php" class="sidebar-link"><span class="sidebar-link-ikon">❌</span>Surat Ditolak</a>
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
          <button id="tombolBukaSidebar" class="tombol-hamburger" style="display:flex;">
            <span></span><span></span><span></span>
          </button>
          <div class="header-pengguna">
            <h3>Halo, <?= htmlspecialchars($nama_petugas) ?></h3>
            <span>Petugas Desa</span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;">
            <button type="submit" class="avatar-pengguna" title="Keluar" onclick="return confirm('Keluar dari sistem?');" style="cursor:pointer;">👤</button>
        </form>
      </header>

      <main class="area-konten">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
          <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;">Antrean Surat Masuk</h1>
        </div>

        <div class="kartu-tabel">
          <table class="tabel-data">
            <thead>
              <tr>
                <th>No</th>
                <th>Tanggal Masuk</th>
                <th>Pemohon (Warga)</th>
                <th>Jenis Surat</th>
                <th>Keterangan Tambahan</th>
                <th style="text-align:center;">Verifikasi</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($data_surat->num_rows > 0): ?>
                  <?php $no = 1; while($row = $data_surat->fetch_assoc()): ?>
                    <tr>
                      <td><?= $no++ ?></td>
                      <td><?= date('d M Y, H:i', strtotime($row['tanggal'])) ?></td>
                      <td><strong><?= htmlspecialchars($row['nama_warga']) ?></strong></td>
                      <td><?= htmlspecialchars($row['jenis_surat']) ?></td>
                      <td><?= htmlspecialchars($row['keterangan']) ?></td>
                      <td style="display:flex; gap:0.5rem; justify-content:center;">
                          <a href="?teruskan_id=<?= $row['id'] ?>" class="btn-primer btn-kecil" style="background:#3b82f6; border:none; text-decoration:none;" onclick="return confirm('Berkas lengkap? Teruskan ke Kepala Desa?');">➡️ Teruskan ke Kades</a>
                          <a href="?tolak_id=<?= $row['id'] ?>" class="btn-sekunder btn-kecil" style="color:#ef4444; border-color:#ef4444; text-decoration:none;" onclick="return confirm('Yakin ingin menolak pengajuan ini?');">✖ Tolak</a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
              <?php else: ?>
                  <tr>
                    <td colspan="6" style="text-align:center; padding: 20px;">Antrean kosong. Belum ada surat baru dari warga.</td>
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
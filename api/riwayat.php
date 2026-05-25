<?php
session_start();
require 'koneksi.php';

// Lindungi halaman
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'warga') {
    header("Location: login.php");
    exit;
}

$id_warga = $_SESSION['user_id'];
$nama_warga = $_SESSION['nama'];

// Ambil semua data riwayat surat milik warga yang sedang login
$query_riwayat = $conn->query("SELECT * FROM surat WHERE id_warga = $id_warga ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Riwayat Pengajuan - NamaWeb</title>
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
        <a href="dashboard.php" class="sidebar-link">
          <span class="sidebar-link-ikon">📊</span>Dashboard
        </a>
        <div class="sidebar-label">Ajukan Surat <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="pengajuan.php?jenis=nikah" class="sidebar-link">
            <span class="sidebar-link-ikon">✉</span>Surat Pengantar Nikah
          </a>
          <a href="pengajuan.php?jenis=usaha" class="sidebar-link">
            <span class="sidebar-link-ikon">✉</span>Surat Keterangan Usaha
          </a>
          <a href="pengajuan.php?jenis=domisili" class="sidebar-link">
            <span class="sidebar-link-ikon">✉</span>Surat Keterangan Domisili
          </a>
          <a href="pengajuan.php?jenis=lainnya" class="sidebar-link">
            <span class="sidebar-link-ikon">✉</span>Surat lorem ipsum
          </a>
        </div>
        <div class="sidebar-label">Informasi <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="riwayat.php" class="sidebar-link aktif">
            <span class="sidebar-link-ikon">🕐</span>Riwayat Pengajuan
          </a>
          <a href="profil.php" class="sidebar-link">
            <span class="sidebar-link-ikon">👤</span>Profil Saya
          </a>
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
            <h3>Halo, <?= htmlspecialchars($nama_warga) ?></h3>
            <span>Warga</span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;">
            <button type="submit" class="avatar-pengguna" title="Keluar dari akun" aria-label="Logout" onclick="return confirm('Yakin ingin keluar?');" style="cursor: pointer;">👤</button>
        </form>
      </header>

      <main class="area-konten">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:0.75rem;">
          <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;">Riwayat Pengajuan</h1>
        </div>

        <div class="kartu-tabel">
          <table class="tabel-data" id="tabelRiwayat">
            <thead>
              <tr>
                <th>#</th>
                <th>Pemohon</th>
                <th>Jenis Surat</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($query_riwayat->num_rows > 0): ?>
                  <?php $no = 1; while($row = $query_riwayat->fetch_assoc()): ?>
                    <?php 
                      // Logika Badge Status
                      $badge = 'badge-menunggu';
                      if($row['status'] == 'Diproses' || $row['status'] == 'Persetujuan Kades') $badge = 'badge-verifikasi';
                      if($row['status'] == 'Selesai') $badge = 'badge-disetujui';
                      if($row['status'] == 'Ditolak') $badge = 'badge-ditolak';
                    ?>
                    <tr>
                      <td><?= $no++ ?></td>
                      <td><?= htmlspecialchars($nama_warga) ?></td>
                      <td><?= htmlspecialchars($row['jenis_surat']) ?></td>
                      <td><?= date('d M Y, H:i', strtotime($row['tanggal'])) ?></td>
                      <td><span class="badge <?= $badge ?>"><?= $row['status'] ?></span></td>
                      <td style="display:flex; gap:0.4rem; align-items:center;">
                        <a href="detail-surat.php?id=<?= $row['id'] ?>" class="btn-lihat" style="text-decoration:none;">👁 Lihat</a>
                        
                        <?php if($row['status'] == 'Selesai'): ?>
                            <button class="btn-unduh" title="Unduh surat" onclick="window.print()">⬇</button>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endwhile; ?>
              <?php else: ?>
                  <tr>
                    <td colspan="6" style="text-align:center; padding: 20px;">Anda belum memiliki riwayat pengajuan surat.</td>
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
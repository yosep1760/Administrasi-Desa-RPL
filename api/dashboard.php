<?php
session_start();
require 'koneksi.php';

// Lindungi halaman: Jika belum login atau bukan warga, tendang ke login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'warga') {
    header("Location: login.php");
    exit;
}

$id_warga = $_SESSION['user_id'];
$nama_warga = $_SESSION['nama'];

// Ambil data statistik dari database
$q_total = $conn->query("SELECT COUNT(*) as total FROM surat WHERE id_warga = $id_warga")->fetch_assoc()['total'];
$q_proses = $conn->query("SELECT COUNT(*) as proses FROM surat WHERE id_warga = $id_warga AND status = 'Diproses'")->fetch_assoc()['proses'];
$q_kades = $conn->query("SELECT COUNT(*) as kades FROM surat WHERE id_warga = $id_warga AND status = 'Persetujuan Kades'")->fetch_assoc()['kades'];
$q_setuju = $conn->query("SELECT COUNT(*) as setuju FROM surat WHERE id_warga = $id_warga AND status = 'Selesai'")->fetch_assoc()['setuju'];

// Ambil 3 pengajuan terakhir
$q_riwayat = $conn->query("SELECT * FROM surat WHERE id_warga = $id_warga ORDER BY id DESC LIMIT 3");
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
    <div id="overlaySidebar" class="overlay-sidebar"></div>

    <aside id="sidebar" class="sidebar">
      <div class="sidebar-header">*Logo + NamaWeb</div>
      <div class="sidebar-cari">
        <input type="search" class="input-cari" placeholder="Search" aria-label="Cari menu" />
      </div>
      <nav class="sidebar-nav">
        <a href="dashboard.php" class="sidebar-link aktif">
          <span class="sidebar-link-ikon">📊</span> Dashboard
        </a>
        <div class="sidebar-label">Ajukan Surat <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="pengajuan.php?jenis=nikah" class="sidebar-link"><span class="sidebar-link-ikon">✉</span> Surat Pengantar Nikah</a>
          <a href="pengajuan.php?jenis=usaha" class="sidebar-link"><span class="sidebar-link-ikon">✉</span> Surat Keterangan Usaha</a>
          <a href="pengajuan.php?jenis=domisili" class="sidebar-link"><span class="sidebar-link-ikon">✉</span> Surat Keterangan Domisili</a>
          <a href="pengajuan.php?jenis=lainnya" class="sidebar-link"><span class="sidebar-link-ikon">✉</span> Surat lorem ipsum</a>
        </div>
        <div class="sidebar-label">Informasi <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="riwayat.php" class="sidebar-link"><span class="sidebar-link-ikon">🕐</span> Riwayat Pengajuan</a>
          <a href="profil.php" class="sidebar-link"><span class="sidebar-link-ikon">👤</span> Profil Saya</a>
        </div>
      </nav>
    </aside>

    <div class="konten-dashboard">
      <header class="header-dashboard">
        <div style="display:flex;align-items:center;gap:0.75rem;">
          <button id="tombolBukaSidebar" class="tombol-hamburger" aria-label="Buka menu" style="display:flex;">
            <span></span><span></span><span></span>
          </button>
          <div class="header-pengguna">
            <h3>Halo, <?= htmlspecialchars($nama_warga) ?></h3>
            <span>Warga</span>
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
            <div class="statistik-label">Disetujui</div>
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
                      $badge = 'badge-menunggu';
                      if($row['status'] == 'Diproses' || $row['status'] == 'Persetujuan Kades') $badge = 'badge-verifikasi';
                      if($row['status'] == 'Selesai') $badge = 'badge-disetujui';
                      if($row['status'] == 'Ditolak') $badge = 'badge-ditolak';
                    ?>
                    <tr>
                      <td><?= htmlspecialchars($row['jenis_surat']) ?></td>
                      <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                      <td><span class="badge <?= $badge ?>"><?= $row['status'] ?></span></td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3" style="text-align: center;">Belum ada pengajuan.</td></tr>
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
              Sistem telah terhubung ke database TiDB Cloud.
            </div>
          </div>
        </div>

      </main>
    </div>
  </div>

  <script src="../js/main.js"></script>
</body>
</html>
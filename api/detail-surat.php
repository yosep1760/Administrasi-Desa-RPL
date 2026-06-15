<?php
require 'koneksi.php';

// Lindungi halaman dengan COOKIE
if (!isset($_COOKIE['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user_login = (int)$_COOKIE['user_id'];
$nama_user = $_COOKIE['nama'];
$role_user = $_COOKIE['role'];

// Cek apakah ada ID pengajuan di URL
if (!isset($_GET['id'])) {
    header("Location: riwayat.php");
    exit;
}

$id_pengajuan = (int)$_GET['id'];

// [UPDATE PDM] Ambil data surat JOIN dengan tabel Users dan Jenis_Surat
$query = $conn->query("
    SELECT ps.*, u.nama_lengkap, u.NIK, js.nama_surat 
    FROM Pengajuan_Surat ps 
    JOIN Users u ON ps.id_user = u.id_user 
    JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis 
    WHERE ps.id_pengajuan = $id_pengajuan
");

if ($query->num_rows == 0) {
    echo "<script>alert('Surat tidak ditemukan!'); window.location.href='riwayat.php';</script>";
    exit;
}

$data = $query->fetch_assoc();

// Keamanan: Warga hanya bisa melihat surat miliknya sendiri
if ($role_user == 'warga' && $data['id_user'] != $id_user_login) {
    echo "<script>alert('Akses Ditolak! Ini bukan surat Anda.'); window.location.href='riwayat.php';</script>";
    exit;
}

// [UPDATE PDM] Ambil daftar dokumen lampiran dari tabel Dokumen_Pengajuan
$query_dokumen = $conn->query("SELECT * FROM Dokumen_Pengajuan WHERE id_pengajuan = $id_pengajuan");

// Pewarnaan Badge Status & Teks ENUM
$status_db = $data['status'];
$badgeClass = 'badge-menunggu';
$status_text = 'Menunggu Petugas';

if ($status_db == 'menunggu_persetujuan') {
    $badgeClass = 'badge-verifikasi'; $status_text = 'Proses Kades';
} elseif ($status_db == 'disetujui') {
    $badgeClass = 'badge-verifikasi'; $status_text = 'Disetujui (Tahap Cetak)';
} elseif ($status_db == 'selesai') {
    $badgeClass = 'badge-disetujui'; $status_text = 'Selesai';
} elseif ($status_db == 'ditolak') {
    $badgeClass = 'badge-ditolak'; $status_text = 'Ditolak / Perlu Perbaikan';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Detail Pengajuan - NamaWeb</title>
  <link rel="stylesheet" href="../css/style.css" />
  <style>
      .tabel-detail { width: 100%; border-collapse: collapse; margin-top: 1rem; }
      .tabel-detail th, .tabel-detail td { padding: 12px 15px; border-bottom: 1px solid #e2e8f0; text-align: left; }
      .tabel-detail th { width: 30%; color: #64748b; font-weight: 600; background-color: #f8fafc; }
      .alert-catatan { background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 15px; margin-top: 20px; border-radius: 4px; }
      .alert-catatan h4 { color: #b91c1c; margin-bottom: 5px; font-size: 1rem; }
      .alert-catatan p { color: #7f1d1d; font-size: 0.95rem; line-height: 1.5; margin-bottom: 5px;}
      .badge-dokumen { background: #e2e8f0; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; color: #475569; margin-right: 5px; border: 1px solid #cbd5e1;}
  </style>
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
        <?php if($role_user == 'warga'): ?>
        <div class="sidebar-label">Ajukan Surat <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="pengajuan.php?id_jenis=1" class="sidebar-link"><span class="sidebar-link-ikon">✉</span>Surat Pengantar Nikah</a>
          <a href="pengajuan.php?id_jenis=2" class="sidebar-link"><span class="sidebar-link-ikon">✉</span>Surat Keterangan Usaha</a>
          <a href="pengajuan.php?id_jenis=3" class="sidebar-link"><span class="sidebar-link-ikon">✉</span>Surat Keter. Domisili</a>
          <a href="pengajuan.php?id_jenis=4" class="sidebar-link"><span class="sidebar-link-ikon">✉</span>Surat Lainnya</a>
        </div>
        <div class="sidebar-label">Informasi <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="riwayat.php" class="sidebar-link aktif"><span class="sidebar-link-ikon">🕐</span>Riwayat Pengajuan</a>
          <a href="profil.php" class="sidebar-link"><span class="sidebar-link-ikon">👤</span>Profil Saya</a>
        </div>
        <?php else: ?>
        <div class="sidebar-label">Navigasi Petugas <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="dashboard-petugas.php" class="sidebar-link"><span class="sidebar-link-ikon">🏠</span>Dashboard</a>
        </div>
        <?php endif; ?>
      </nav>
    </aside>

    <div class="konten-dashboard">
      <header class="header-dashboard">
        <div style="display:flex;align-items:center;gap:0.75rem;">
          <button id="tombolBukaSidebar" class="tombol-hamburger" style="display:flex;"><span></span><span></span><span></span></button>
          <div class="header-pengguna">
            <h3>Halo, <?= htmlspecialchars($nama_user) ?></h3>
            <span><?= ucfirst($role_user) ?></span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;">
            <button type="submit" class="avatar-pengguna" title="Keluar" onclick="return confirm('Yakin ingin keluar?');" style="cursor:pointer;">👤</button>
        </form>
      </header>

      <main class="area-konten">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
          <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;">Detail Pengajuan Surat</h1>
          <?php if($role_user == 'warga'): ?>
             <a href="riwayat.php" class="btn-sekunder btn-kecil" style="text-decoration:none;">← Kembali ke Riwayat</a>
          <?php endif; ?>
        </div>

        <div class="kartu-form">
          <h3 style="font-size:1.1rem; font-weight:700; border-bottom:1px solid #e2e8f0; padding-bottom:10px;">Informasi Dokumen</h3>
          
          <table class="tabel-detail">
            <tr>
              <th>Nomor Registrasi</th>
              <td>#SRT-<?= str_pad($data['id_pengajuan'], 5, '0', STR_PAD_LEFT) ?></td>
            </tr>
            <tr>
              <th>Tanggal Pengajuan</th>
              <td><?= date('d M Y, H:i', strtotime($data['tanggal_pengajuan'])) ?> WIB</td>
            </tr>
            <tr>
              <th>Nama Pemohon</th>
              <td><strong><?= htmlspecialchars($data['nama_lengkap']) ?></strong></td>
            </tr>
            <tr>
              <th>NIK Pemohon</th>
              <td><?= htmlspecialchars($data['NIK']) ?></td>
            </tr>
            <tr>
              <th>Jenis Surat</th>
              <td><?= htmlspecialchars($data['nama_surat']) ?></td>
            </tr>
            <tr>
              <th>Keperluan</th>
              <td><?= htmlspecialchars($data['keperluan']) ?></td>
            </tr>
            <tr>
              <th>Status Verifikasi</th>
              <td><span class="badge <?= $badgeClass ?>" style="font-size:0.9rem; padding:5px 10px;"><?= $status_text ?></span></td>
            </tr>
            <tr>
              <th>Lampiran Warga</th>
              <td>
                  <?php if($query_dokumen->num_rows > 0): ?>
                      <?php while($doc = $query_dokumen->fetch_assoc()): ?>
                          <span class="badge-dokumen">📎 <?= htmlspecialchars($doc['jenis_dokumen']) ?> : <?= htmlspecialchars($doc['nama_file']) ?></span><br>
                      <?php endwhile; ?>
                  <?php else: ?>
                      <span style="color:#94a3b8; font-style:italic;">Tidak ada lampiran dokumen</span>
                  <?php endif; ?>
              </td>
            </tr>
          </table>

          <?php if (!empty($data['catatan_petugas']) || !empty($data['catatan_kades'])): ?>
          <div class="alert-catatan">
            <h4>⚠️ Pemberitahuan Sistem:</h4>
            <?php if (!empty($data['catatan_petugas'])): ?>
                <p><strong>Catatan Petugas:</strong> <?= htmlspecialchars($data['catatan_petugas']) ?></p>
            <?php endif; ?>
            
            <?php if (!empty($data['catatan_kades'])): ?>
                <p><strong>Alasan Tolak Kades:</strong> <?= htmlspecialchars($data['catatan_kades']) ?></p>
            <?php endif; ?>
          </div>
          <?php endif; ?>

        </div>
      </main>
    </div>
  </div>

  <script src="../js/main.js"></script>
</body>
</html>
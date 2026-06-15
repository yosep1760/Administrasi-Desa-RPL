<?php
require 'koneksi.php';

// Lindungi halaman dengan COOKIE
if (!isset($_COOKIE['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_COOKIE['user_id'];
$nama_user = $_COOKIE['nama'];
$role_user = $_COOKIE['role'];

// Cek apakah ada ID surat di URL
if (!isset($_GET['id'])) {
    header("Location: riwayat.php");
    exit;
}

$id_surat = (int)$_GET['id'];

// Ambil data surat beserta detail pemohonnya
$query = $conn->query("SELECT surat.*, pengguna.nama AS nama_pemohon, pengguna.nik AS nik_pemohon 
                       FROM surat 
                       JOIN pengguna ON surat.id_warga = pengguna.id 
                       WHERE surat.id = $id_surat");

if ($query->num_rows == 0) {
    echo "<script>alert('Surat tidak ditemukan!'); window.location.href='riwayat.php';</script>";
    exit;
}

$data = $query->fetch_assoc();

// Keamanan: Warga hanya bisa melihat surat miliknya sendiri
if ($role_user == 'warga' && $data['id_warga'] != $id_user) {
    echo "<script>alert('Akses Ditolak! Ini bukan surat Anda.'); window.location.href='riwayat.php';</script>";
    exit;
}

// Pewarnaan Badge Status
$badgeClass = 'badge-menunggu';
if($data['status'] == 'Perlu Perbaikan' || $data['status'] == 'Ditolak') $badgeClass = 'badge-ditolak';
if($data['status'] == 'Menunggu Approval Kepala desa' || $data['status'] == 'Disetujui') $badgeClass = 'badge-verifikasi';
if($data['status'] == 'Selesai') $badgeClass = 'badge-disetujui';

// TRIK CERDAS: Memisahkan Keterangan Asli dan Catatan Penolakan/Perbaikan
$keterangan_asli = $data['keterangan'];
$catatan_sistem = "";

// Jika ada tanda '|' di dalam teks, berarti ada catatan dari sistem/petugas
if (strpos($keterangan_asli, '|') !== false) {
    $pecah = explode('|', $keterangan_asli);
    $keterangan_asli = trim($pecah[0]); // Bagian pertama adalah keterangan asli warga
    
    // Gabungkan sisanya menjadi catatan sistem
    unset($pecah[0]);
    $catatan_sistem = implode('<br>', array_map('trim', $pecah));
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
      .alert-catatan p { color: #7f1d1d; font-size: 0.95rem; line-height: 1.5; }
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
        <a href="dashboard.php" class="sidebar-link">
          <span class="sidebar-link-ikon">📊</span>Dashboard
        </a>
        <div class="sidebar-label">Ajukan Surat <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="pengajuan.php?jenis=nikah" class="sidebar-link"><span class="sidebar-link-ikon">✉</span>Surat Pengantar Nikah</a>
          <a href="pengajuan.php?jenis=usaha" class="sidebar-link"><span class="sidebar-link-ikon">✉</span>Surat Keterangan Usaha</a>
          <a href="pengajuan.php?jenis=domisili" class="sidebar-link"><span class="sidebar-link-ikon">✉</span>Surat Keterangan Domisili</a>
          <a href="pengajuan.php?jenis=lainnya" class="sidebar-link"><span class="sidebar-link-ikon">✉</span>Surat lorem ipsum</a>
        </div>
        <div class="sidebar-label">Informasi <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="riwayat.php" class="sidebar-link aktif"><span class="sidebar-link-ikon">🕐</span>Riwayat Pengajuan</a>
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
            <h3>Halo, <?= htmlspecialchars($nama_user) ?></h3>
            <span>Warga</span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;">
            <button type="submit" class="avatar-pengguna" title="Keluar" onclick="return confirm('Yakin ingin keluar?');" style="cursor:pointer;">👤</button>
        </form>
      </header>

      <main class="area-konten">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
          <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;">Detail Pengajuan Surat</h1>
          <a href="riwayat.php" class="btn-sekunder btn-kecil" style="text-decoration:none;">← Kembali ke Riwayat</a>
        </div>

        <div class="kartu-form">
          <h3 style="font-size:1.1rem; font-weight:700; border-bottom:1px solid #e2e8f0; padding-bottom:10px;">Informasi Dokumen</h3>
          
          <table class="tabel-detail">
            <tr>
              <th>Nomor Registrasi</th>
              <td>#SRT-<?= str_pad($data['id'], 5, '0', STR_PAD_LEFT) ?></td>
            </tr>
            <tr>
              <th>Tanggal Pengajuan</th>
              <td><?= date('d M Y, H:i', strtotime($data['tanggal'])) ?> WIB</td>
            </tr>
            <tr>
              <th>Nama Pemohon</th>
              <td><strong><?= htmlspecialchars($data['nama_pemohon']) ?></strong></td>
            </tr>
            <tr>
              <th>NIK Pemohon</th>
              <td><?= htmlspecialchars($data['nik']) ?></td>
            </tr>
            <tr>
              <th>Jenis Surat</th>
              <td><?= htmlspecialchars($data['jenis_surat']) ?></td>
            </tr>
            <tr>
              <th>Keperluan Awal</th>
              <td><?= htmlspecialchars($keterangan_asli) ?></td>
            </tr>
            <tr>
              <th>Status Verifikasi</th>
              <td><span class="badge <?= $badgeClass ?>" style="font-size:0.9rem; padding:5px 10px;"><?= $data['status'] ?></span></td>
            </tr>
          </table>

          <?php if (!empty($catatan_sistem)): ?>
          <div class="alert-catatan">
            <h4>Pemberitahuan Sistem / Catatan Petugas:</h4>
            <p><?= $catatan_sistem ?></p>
          </div>
          <?php endif; ?>

        </div>
      </main>
    </div>
  </div>

  <script src="../js/main.js"></script>
</body>
</html>
<?php
require 'koneksi.php';

// Lindungi halaman (Gunakan COOKIE)
if (!isset($_COOKIE['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_COOKIE['user_id'];
$nama_user = $_COOKIE['nama'];
$role_user = $_COOKIE['role']; // Ambil role dari cookie

// Cek apakah ada ID surat di URL
if (!isset($_GET['id'])) {
    header("Location: riwayat.php");
    exit;
}

$id_surat = (int)$_GET['id'];

// Ambil data surat beserta nama pemohonnya
$query = $conn->query("SELECT surat.*, pengguna.nama AS nama_pemohon 
                       FROM surat 
                       JOIN pengguna ON surat.id_warga = pengguna.id 
                       WHERE surat.id = $id_surat");

if ($query->num_rows == 0) {
    echo "<script>alert('Surat tidak ditemukan!'); window.location.href='riwayat.php';</script>";
    exit;
}

$data = $query->fetch_assoc();

// Keamanan Tambahan: Pastikan Warga HANYA bisa melihat surat miliknya sendiri
if ($role_user == 'warga' && $data['id_warga'] != $id_user) {
    echo "<script>alert('Akses Ditolak! Ini bukan surat Anda.'); window.location.href='riwayat.php';</script>";
    exit;
}

// Logika Badge Status
$badgeClass = 'badge-menunggu';
if($data['status'] == 'Diproses' || $data['status'] == 'Persetujuan Kades') $badgeClass = 'badge-verifikasi';
if($data['status'] == 'Selesai') $badgeClass = 'badge-disetujui';
if($data['status'] == 'Ditolak') $badgeClass = 'badge-ditolak';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Detail Surat - NamaWeb</title>
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
            <span style="text-transform: capitalize;"><?= htmlspecialchars($_SESSION['role']) ?></span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;">
            <button type="submit" class="avatar-pengguna" title="Keluar dari akun" aria-label="Logout" onclick="return confirm('Yakin ingin keluar?');" style="cursor: pointer;">👤</button>
        </form>
      </header>

      <main class="area-konten">
        <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;margin-bottom:0.25rem;">Detail Surat</h1>
        <button class="tombol-kembali" onclick="window.history.back()">
          ← Kembali
        </button>

        <div class="grid-detail">
          <div class="detail-panel-kiri">
            <div class="kartu-detail">
              <h3><?= htmlspecialchars($data['jenis_surat']) ?></h3>
              <p class="nomor-pengajuan">Nomor Pengajuan: PS - <?= sprintf("%05d", $data['id']) ?></p>

              <dl>
                <div class="baris-detail">
                  <dt>Jenis Surat:</dt>
                  <dd><?= htmlspecialchars($data['jenis_surat']) ?></dd>
                </div>
                <div class="baris-detail">
                  <dt>Nama Pemohon:</dt>
                  <dd><?= htmlspecialchars($data['nama_pemohon']) ?></dd>
                </div>
                <div class="baris-detail">
                  <dt>NIK:</dt>
                  <dd><?= htmlspecialchars($data['nik']) ?></dd>
                </div>
                <div class="baris-detail">
                  <dt>Tanggal Pengajuan:</dt>
                  <dd><?= date('d F Y, H:i', strtotime($data['tanggal'])) ?></dd>
                </div>
                <div class="baris-detail">
                  <dt>Status:</dt>
                  <dd>
                    <span class="badge <?= $badgeClass ?>"><?= $data['status'] ?></span>
                  </dd>
                </div>
                <div class="baris-detail">
                  <dt>Keterangan Tambahan:</dt>
                  <dd><?= htmlspecialchars($data['keterangan']) ?></dd>
                </div>
              </dl>
            </div>

            <div class="kartu-detail">
              <h3 style="font-size:1rem;margin-bottom:1rem;">Dokumen Pendukung</h3>
              <div class="item-dokumen">
                <div class="item-dokumen-info">
                  <span class="ikon-pdf">📄</span> Dokumen tersimpan di sistem aman.
                </div>
              </div>
            </div>
          </div>

          <div class="detail-panel-kanan">
            <div class="panel-notifikasi">
              <div class="panel-notifikasi-header">
                <h3>Pemberitahuan Status</h3>
              </div>

              <?php if($data['status'] == 'Menunggu'): ?>
                <div class="item-notifikasi">
                  <span class="notif-titik notif-biru"></span>
                  Pengajuan Anda telah diterima sistem dan sedang menunggu verifikasi petugas.
                </div>
              <?php elseif($data['status'] == 'Diproses'): ?>
                <div class="item-notifikasi">
                  <span class="notif-titik notif-biru"></span>
                  Dokumen Anda sedang <strong>diverifikasi</strong> oleh Petugas Desa.
                </div>
              <?php elseif($data['status'] == 'Persetujuan Kades'): ?>
                <div class="item-notifikasi">
                  <span class="notif-titik notif-biru"></span>
                  Surat telah diverifikasi petugas dan sedang menunggu <strong>Persetujuan Kepala Desa</strong>.
                </div>
              <?php elseif($data['status'] == 'Selesai'): ?>
                <div class="item-notifikasi">
                  <span class="notif-titik notif-hijau"></span>
                  Surat Anda telah <strong>Disetujui</strong> dan selesai diproses! 
                </div>
                <button class="btn-primer" style="width:100%; margin-top:1rem;" onclick="window.print()">Cetak Surat</button>
              <?php elseif($data['status'] == 'Ditolak'): ?>
                <div class="item-notifikasi">
                  <span class="notif-titik notif-merah"></span>
                  Mohon maaf, pengajuan surat Anda <strong>Ditolak</strong> karena data tidak valid atau dokumen kurang lengkap.
                </div>
              <?php endif; ?>

            </div>
          </div>
        </div>

      </main>
    </div>
  </div>

  <script src="../js/main.js"></script>
</body>
</html>
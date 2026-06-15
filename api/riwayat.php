<?php
require 'koneksi.php';

// Lindungi halaman dengan COOKIE
if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'warga') {
    header("Location: login.php");
    exit;
}

$id_user = (int)$_COOKIE['user_id'];
$nama_warga = $_COOKIE['nama'];

// [UPDATE PDM] Ambil data riwayat surat dari tabel Pengajuan_Surat JOIN Jenis_Surat
$query_riwayat = $conn->query("
    SELECT ps.*, js.nama_surat 
    FROM Pengajuan_Surat ps
    JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis
    WHERE ps.id_user = $id_user 
    ORDER BY ps.tanggal_pengajuan DESC
");
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
            <span>Warga Desa</span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;">
            <button type="submit" class="avatar-pengguna" title="Keluar" onclick="return confirm('Yakin ingin keluar?');" style="cursor:pointer;">👤</button>
        </form>
      </header>

      <main class="area-konten">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
          <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;">Riwayat & Tracking Surat</h1>
        </div>

        <div class="kartu-tabel">
          <table class="tabel-data">
            <thead>
              <tr>
                <th>No</th>
                <th>Tgl Pengajuan</th>
                <th>Jenis Surat</th>
                <th>Status Saat Ini</th>
                <th style="text-align:center;">Aksi Lanjutan</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($query_riwayat->num_rows > 0): ?>
                  <?php $no = 1; while($row = $query_riwayat->fetch_assoc()): ?>
                    <?php 
                      // Menerjemahkan ENUM Database menjadi Teks dan Warna Badge yang rapi
                      $status_db = $row['status'];
                      $badgeClass = 'badge-menunggu';
                      $status_text = 'Menunggu';

                      if ($status_db == 'menunggu_verifikasi') {
                          $badgeClass = 'badge-menunggu'; // Kuning
                          $status_text = 'Menunggu Petugas';
                      } elseif ($status_db == 'menunggu_persetujuan') {
                          $badgeClass = 'badge-verifikasi'; // Biru
                          $status_text = 'Proses Kades';
                      } elseif ($status_db == 'disetujui') {
                          $badgeClass = 'badge-verifikasi'; // Biru
                          $status_text = 'Disetujui (Tahap Cetak)';
                      } elseif ($status_db == 'selesai') {
                          $badgeClass = 'badge-disetujui'; // Hijau
                          $status_text = 'Selesai';
                      } elseif ($status_db == 'ditolak') {
                          $badgeClass = 'badge-ditolak'; // Merah
                          $status_text = 'Ditolak / Perbaikan';
                      }
                    ?>
                    <tr>
                      <td><?= $no++ ?></td>
                      <td><?= date('d M Y, H:i', strtotime($row['tanggal_pengajuan'])) ?></td>
                      <td><strong><?= htmlspecialchars($row['nama_surat']) ?></strong></td>
                      <td><span class="badge <?= $badgeClass ?>"><?= $status_text ?></span></td>
                      
                      <td style="text-align:center;">
                          <?php 
                            // PERCABANGAN TOMBOL (Jika selesai = Cetak, jika belum = Detail)
                            if ($status_db == 'selesai') {
                                echo '<a href="cetak.php?id='.$row['id_pengajuan'].'" target="_blank" class="btn-primer btn-kecil" style="background:#16a34a; border:none; text-decoration:none;">🖨️ Cetak Dokumen</a>';
                            } else {
                                echo '<a href="detail-surat.php?id='.$row['id_pengajuan'].'" class="btn-sekunder btn-kecil" style="text-decoration:none;">🔍 Lihat Detail</a>';
                            }
                          ?>
                      </td>
                    </tr>
                  <?php endwhile; ?>
              <?php else: ?>
                  <tr>
                    <td colspan="5" style="text-align:center; padding: 20px;">Anda belum memiliki riwayat pengajuan surat.</td>
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
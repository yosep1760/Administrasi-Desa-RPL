<?php
require 'koneksi.php';

// Lindungi halaman: Pastikan yang login HANYA PETUGAS
if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'petugas') {
    header("Location: login.php");
    exit;
}

$nama_petugas = $_COOKIE['nama'];

// LOGIKA 1: Tombol Terima / Teruskan (Sesuai Diagram: Set status = Menunggu Approval Kepala desa)
if (isset($_GET['teruskan_id'])) {
    $id_surat = (int)$_GET['teruskan_id'];
    
    $conn->query("UPDATE surat SET status='Menunggu Approval Kepala desa' WHERE id=$id_surat");
    header("Location: petugas-masuk.php");
    exit;
}

// LOGIKA 2: Tombol Tolak (Sesuai Diagram: Set status = Perlu Perbaikan & Menyimpan catatan perbaikan)
if (isset($_GET['tolak_id']) && isset($_GET['catatan'])) {
    $id_surat = (int)$_GET['tolak_id'];
    $catatan = $conn->real_escape_string($_GET['catatan']);
    
    // Gabungkan catatan perbaikan ke kolom keterangan agar terbaca di riwayat warga
    $conn->query("UPDATE surat SET status='Perlu Perbaikan', keterangan = CONCAT(keterangan, ' | ⚠️ CATATAN PERBAIKAN: ', '$catatan') WHERE id=$id_surat");
    
    header("Location: petugas-masuk.php");
    exit;
}

// Ambil data surat yang statusnya "Menunggu Verifikasi Petugas"
$query = "SELECT surat.*, pengguna.nama AS nama_warga 
          FROM surat 
          JOIN pengguna ON surat.id_warga = pengguna.id 
          WHERE surat.status = 'Menunggu Verifikasi Petugas'
          ORDER BY surat.tanggal ASC"; 
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
                <th>Keterangan</th>
                <th style="text-align:center;">Verifikasi data</th>
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
                          <a href="?teruskan_id=<?= $row['id'] ?>" class="btn-primer btn-kecil" style="background:#22c55e; border:none; text-decoration:none;" onclick="return confirm('Data valid? Tekan OK untuk meneruskan ke Kepala Desa.');">✔ Terima</a>
                          
                          <button onclick="tolakDenganCatatan(<?= $row['id'] ?>)" class="btn-sekunder btn-kecil" style="color:#ef4444; border-color:#ef4444; cursor:pointer;">✖ Tolak</button>
                      </td>
                    </tr>
                  <?php endwhile; ?>
              <?php else: ?>
                  <tr>
                    <td colspan="6" style="text-align:center; padding: 20px;">Antrean kosong. Belum ada surat baru dengan status Menunggu Verifikasi Petugas.</td>
                  </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>

  <script src="../js/main.js"></script>
  
  <script>
    function tolakDenganCatatan(idSurat) {
        let catatan = prompt("Menambahkan catatan perbaikan untuk warga (wajib diisi):");
        
        if (catatan != null && catatan.trim() !== "") {
            window.location.href = "?tolak_id=" + idSurat + "&catatan=" + encodeURIComponent(catatan);
        } else if (catatan != null) {
            alert("Gagal! Catatan perbaikan tidak boleh kosong.");
        }
    }
  </script>
</body>
</html>
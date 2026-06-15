<?php
require 'koneksi.php';

// Lindungi halaman: Pastikan yang login HANYA PETUGAS
if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'petugas') {
    header("Location: login.php");
    exit;
}

$nama_petugas = $_COOKIE['nama'];

// LOGIKA 1: Tombol Terima / Teruskan (Ubah status sesuai ENUM PDM)
if (isset($_GET['teruskan_id'])) {
    $id_pengajuan = (int)$_GET['teruskan_id'];
    
    // Status diteruskan ke Kades
    $conn->query("UPDATE Pengajuan_Surat SET status='menunggu_persetujuan' WHERE id_pengajuan=$id_pengajuan");
    header("Location: petugas-masuk.php");
    exit;
}

// LOGIKA 2: Tombol Tolak (Ubah status & isi kolom catatan_petugas PDM)
if (isset($_GET['tolak_id']) && isset($_GET['catatan'])) {
    $id_pengajuan = (int)$_GET['tolak_id'];
    $catatan = $conn->real_escape_string($_GET['catatan']);
    
    // Sesuai PDM, status menjadi 'ditolak' dan catatan dimasukkan ke kolom khususnya
    $conn->query("UPDATE Pengajuan_Surat SET status='ditolak', catatan_petugas='$catatan' WHERE id_pengajuan=$id_pengajuan");
    
    header("Location: petugas-masuk.php");
    exit;
}

// [UPDATE PDM] Ambil surat yang statusnya "menunggu_verifikasi" (Antrean Petugas)
$query = "
    SELECT ps.*, u.nama_lengkap AS nama_warga, js.nama_surat 
    FROM Pengajuan_Surat ps
    JOIN Users u ON ps.id_user = u.id_user 
    JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis
    WHERE ps.status = 'menunggu_verifikasi'
    ORDER BY ps.tanggal_pengajuan ASC
"; 
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
          <p style="color:var(--warna-teks-muda);">Periksa lampiran sebelum menyetujui pengajuan.</p>
        </div>

        <div class="kartu-tabel">
          <table class="tabel-data">
            <thead>
              <tr>
                <th>No</th>
                <th>Tgl Masuk</th>
                <th>Pemohon</th>
                <th>Jenis Surat</th>
                <th>Keperluan</th>
                <th style="text-align:center;">Aksi Verifikasi</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($data_surat->num_rows > 0): ?>
                  <?php $no = 1; while($row = $data_surat->fetch_assoc()): ?>
                    <tr>
                      <td><?= $no++ ?></td>
                      <td><?= date('d M Y, H:i', strtotime($row['tanggal_pengajuan'])) ?></td>
                      <td><strong><?= htmlspecialchars($row['nama_warga']) ?></strong></td>
                      <td><?= htmlspecialchars($row['nama_surat']) ?></td>
                      <td><?= htmlspecialchars($row['keperluan']) ?></td>
                      <td style="display:flex; gap:0.5rem; justify-content:center;">
                          
                          <a href="detail-surat.php?id=<?= $row['id_pengajuan'] ?>" class="btn-sekunder btn-kecil" style="text-decoration:none;" title="Lihat Lampiran KTP/KK">🔍 Cek</a>

                          <a href="?teruskan_id=<?= $row['id_pengajuan'] ?>" class="btn-primer btn-kecil" style="background:#22c55e; border:none; text-decoration:none;" onclick="return confirm('Sudah cek dokumen? Tekan OK untuk meneruskan ke Kepala Desa.');">✔ Terima</a>
                          
                          <button onclick="tolakDenganCatatan(<?= $row['id_pengajuan'] ?>)" class="btn-sekunder btn-kecil" style="color:#ef4444; border-color:#ef4444; cursor:pointer;">✖ Tolak</button>
                      </td>
                    </tr>
                  <?php endwhile; ?>
              <?php else: ?>
                  <tr>
                    <td colspan="6" style="text-align:center; padding: 20px;">Antrean kosong. Belum ada surat baru dengan status Menunggu Verifikasi.</td>
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
    function tolakDenganCatatan(idPengajuan) {
        let catatan = prompt("Tulis alasan mengapa ditolak/perlu perbaikan (Contoh: KTP buram, KK tidak valid):");
        
        if (catatan != null && catatan.trim() !== "") {
            window.location.href = "?tolak_id=" + idPengajuan + "&catatan=" + encodeURIComponent(catatan);
        } else if (catatan != null) {
            alert("Gagal! Catatan perbaikan tidak boleh kosong.");
        }
    }
  </script>
</body>
</html>
<?php
require 'koneksi.php';

// Lindungi halaman: Pastikan yang login HANYA KADES
if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'kepala_desa') {
    header("Location: login.php");
    exit;
}

$nama_kades = $_COOKIE['nama'];

// LOGIKA 1: Menekan tombol Setujui (Ubah status ke 'disetujui' sesuai ENUM PDM)
if (isset($_GET['approve_id'])) {
    $id_pengajuan = (int)$_GET['approve_id'];
    
    // Status diubah menjadi "disetujui" agar masuk ke antrean Upload Petugas
    $conn->query("UPDATE Pengajuan_Surat SET status='disetujui' WHERE id_pengajuan=$id_pengajuan");
    header("Location: kades-request.php");
    exit;
}

// LOGIKA 2: Menekan tombol Tolak (Ubah status & isi kolom catatan_kades PDM)
if (isset($_GET['reject_id']) && isset($_GET['alasan'])) {
    $id_pengajuan = (int)$_GET['reject_id'];
    $alasan = $conn->real_escape_string($_GET['alasan']);
    
    // Menyimpan alasan penolakan dan mengubah status menjadi 'ditolak'
    $conn->query("UPDATE Pengajuan_Surat SET status='ditolak', catatan_kades='$alasan' WHERE id_pengajuan=$id_pengajuan");
    
    header("Location: kades-request.php");
    exit;
}

// [UPDATE PDM] Ambil pengajuan yang statusnya "menunggu_persetujuan"
$query = "
    SELECT ps.*, u.nama_lengkap AS nama_warga, js.nama_surat 
    FROM Pengajuan_Surat ps 
    JOIN Users u ON ps.id_user = u.id_user 
    JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis 
    WHERE ps.status = 'menunggu_persetujuan'
    ORDER BY ps.tanggal_pengajuan ASC
"; 
$data_surat = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Approval Surat - NamaWeb</title>
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
          <a href="dashboard-kades.php" class="sidebar-link"><span class="sidebar-link-ikon">🏠</span>Home</a>
        </div>
        <div class="sidebar-label">Layanan <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="kades-request.php" class="sidebar-link aktif"><span class="sidebar-link-ikon">📩</span>Request Approval</a>
          <a href="kades-disetujui.php" class="sidebar-link"><span class="sidebar-link-ikon">✅</span>Surat Disetujui</a>
          <a href="kades-ditolak.php" class="sidebar-link"><span class="sidebar-link-ikon">❌</span>Surat Ditolak</a>
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
            <h3>Halo, <?= htmlspecialchars($nama_kades) ?></h3>
            <span>Kepala Desa</span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;">
            <button type="submit" class="avatar-pengguna" title="Keluar" onclick="return confirm('Keluar dari sistem?');" style="cursor:pointer;">👤</button>
        </form>
      </header>

      <main class="area-konten">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
          <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;">Daftar Approval Surat</h1>
          <p style="color:var(--warna-teks-muda);">Daftar permohonan yang telah diverifikasi oleh petugas.</p>
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
                <th style="text-align:center;">Review Pengajuan</th>
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
                          
                          <a href="detail-surat.php?id=<?= $row['id_pengajuan'] ?>" class="btn-sekunder btn-kecil" style="text-decoration:none;" title="Lihat Lampiran & Catatan Petugas">🔍 Cek</a>

                          <a href="?approve_id=<?= $row['id_pengajuan'] ?>" class="btn-primer btn-kecil" style="background:#22c55e; border:none; text-decoration:none;" onclick="return confirm('Menyetujui surat ini untuk di-upload oleh petugas?');">✔ Setujui</a>
                          
                          <button onclick="tolakSuratKades(<?= $row['id_pengajuan'] ?>)" class="btn-sekunder btn-kecil" style="color:#ef4444; border-color:#ef4444; cursor:pointer;">✖ Tolak</button>
                      </td>
                    </tr>
                  <?php endwhile; ?>
              <?php else: ?>
                  <tr>
                    <td colspan="6" style="text-align:center; padding: 20px;">Belum ada surat di daftar approval.</td>
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
    function tolakSuratKades(idPengajuan) {
        let alasan = prompt("Tulis alasan penolakan mutlak dari Kepala Desa (wajib diisi):");
        
        if (alasan != null && alasan.trim() !== "") {
            window.location.href = "?reject_id=" + idPengajuan + "&alasan=" + encodeURIComponent(alasan);
        } else if (alasan != null) {
            alert("Proses dibatalkan! Alasan penolakan tidak boleh kosong.");
        }
    }
  </script>
</body>
</html>
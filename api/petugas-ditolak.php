<?php
require 'koneksi.php';

if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'petugas') {
    header("Location: login.php");
    exit;
}

$nama_petugas = $_COOKIE['nama'];

// Ambil pengajuan yang statusnya 'ditolak'
$query = "
    SELECT ps.*, u.nama_lengkap AS nama_warga, js.nama_surat 
    FROM Pengajuan_Surat ps 
    JOIN Users u ON ps.id_user = u.id_user 
    JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis
    WHERE ps.status = 'ditolak'
    ORDER BY ps.tanggal_pengajuan DESC
";
$data_surat = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Surat Ditolak - SiKosar</title>
  <link rel="stylesheet" href="../css/style.css" />
  <style>
      .tabel-custom { width: 100%; border-collapse: collapse; }
      .tabel-custom th, .tabel-custom td { padding: 12px 15px; border-bottom: 1px solid #e2e8f0; text-align: left; }
      .tabel-custom th { background-color: #f8fafc; font-weight: 600; color: #475569; }
      .tabel-custom tr:hover { background-color: #f1f5f9; }
  </style>
</head>
<body>
  <div class="layout-dashboard">
    <?php include 'sidebar.php'; ?>

    <div class="konten-dashboard">
      <header class="header-dashboard">
        <div style="display:flex;align-items:center;gap:0.75rem;">
          <button id="tombolBukaSidebar" class="tombol-hamburger" style="display:flex;"><span></span><span></span><span></span></button>
          <div class="header-pengguna">
            <h3>Halo, <?= htmlspecialchars($nama_petugas) ?></h3>
            <span>Petugas Layanan</span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;">
            <button type="submit" class="avatar-pengguna" title="Keluar">👤</button>
        </form>
      </header>

      <main class="area-konten">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <div>
              <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;">Riwayat Surat Ditolak</h1>
              <p style="color:var(--warna-teks-muda);margin-top:0.25rem;">Daftar pengajuan surat yang tidak valid dan perlu diperbaiki warga.</p>
            </div>
        </div>

        <div class="kartu-form" style="padding:0; overflow-x:auto;">
          <table class="tabel-custom">
            <thead>
              <tr>
                <th>No. Referensi</th>
                <th>Tanggal Pengajuan</th>
                <th>Pemohon</th>
                <th>Jenis Surat</th>
                <th>Alasan Ditolak</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($data_surat->num_rows > 0): ?>
                  <?php while($row = $data_surat->fetch_assoc()): ?>
                    <?php 
                        $alasan = $row['catatan_petugas'] ? $row['catatan_petugas'] : $row['catatan_kades'];
                    ?>
                    <tr>
                      <td>#SKSR-<?= 1000 + $row['id_pengajuan'] ?></td>
                      <td><?= date('d M Y, H:i', strtotime($row['tanggal_pengajuan'])) ?></td>
                      <td><strong><?= htmlspecialchars($row['nama_warga']) ?></strong></td>
                      <td><?= htmlspecialchars($row['nama_surat']) ?></td>
                      <td style="color:#dc2626; max-width:250px;">
                          <em>"<?= htmlspecialchars($alasan) ?>"</em>
                      </td>
                    </tr>
                  <?php endwhile; ?>
              <?php else: ?>
                  <tr>
                    <td colspan="5" style="text-align:center; padding: 30px; color:#64748b;">Belum ada riwayat surat yang ditolak.</td>
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
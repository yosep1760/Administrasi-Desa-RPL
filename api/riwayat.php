<?php
require 'koneksi.php';

if (!isset($_COOKIE['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = (int)$_COOKIE['user_id'];
$nama_user = $_COOKIE['nama'];
$role_user = $_COOKIE['role'];

// Jika Warga, hanya melihat miliknya. Jika bukan, melihat semua riwayat.
if ($role_user == 'warga') {
    $query = "SELECT ps.*, u.nama_lengkap, js.nama_surat FROM Pengajuan_Surat ps JOIN Users u ON ps.id_user = u.id_user JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis WHERE ps.id_user = $id_user ORDER BY ps.tanggal_pengajuan DESC";
} else {
    $query = "SELECT ps.*, u.nama_lengkap, js.nama_surat FROM Pengajuan_Surat ps JOIN Users u ON ps.id_user = u.id_user JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis ORDER BY ps.tanggal_pengajuan DESC";
}

$data_riwayat = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Riwayat Pengajuan - Desa Kosar</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <div class="layout-dashboard">
    <?php include 'sidebar.php'; ?>

    <div class="konten-dashboard">
      <header class="header-dashboard">
        <div style="display:flex;align-items:center;gap:0.75rem;">
          <button id="tombolBukaSidebar" class="tombol-hamburger" style="display:flex;"><span></span><span></span><span></span></button>
          <div class="header-pengguna">
            <h3>Halo, <?= htmlspecialchars($nama_user) ?></h3>
            <span style="text-transform: capitalize;"><?= str_replace('_', ' ', $role_user) ?></span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;"><button type="submit" class="avatar-pengguna">👤</button></form>
      </header>

      <main class="area-konten">
        <h1 style="font-family:var(--font-judul);font-size:1.8rem;font-weight:700;margin-bottom:1.5rem;">Riwayat Pengajuan</h1>

        <div style="display:flex; gap:1rem; margin-bottom:1.5rem; flex-wrap:wrap;">
            <input type="text" class="input-form" placeholder="Cari Nama Pemohon / Surat..." style="flex:1; max-width:400px;">
            <button class="btn-sekunder" style="background:transparent; border:1px solid #000; color:#000;">≡ Jenis Surat</button>
            <button class="btn-sekunder" style="background:transparent; border:1px solid #000; color:#000;">≡ Status</button>
        </div>

        <div class="kartu-tabel">
          <table class="tabel-data">
            <thead>
              <tr style="background-color: #d1d5db;">
                <th>#</th>
                <th>Pemohon</th>
                <th>Jenis Surat</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th style="text-align:center;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if($data_riwayat->num_rows > 0): ?>
                  <?php $no=1; while($row = $data_riwayat->fetch_assoc()): ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= htmlspecialchars($row['nama_lengkap']) ?></strong></td>
                    <td><?= htmlspecialchars($row['nama_surat']) ?></td>
                    <td><?= date('d - m - Y', strtotime($row['tanggal_pengajuan'])) ?></td>
                    <td style="text-transform: capitalize;">
                        <?= str_replace('_', ' ', $row['status']) ?>
                    </td>
                    <td style="text-align:center; display:flex; justify-content:center; gap:0.5rem;">
                        <a href="detail-surat.php?id=<?= $row['id_pengajuan'] ?>" class="btn-sekunder btn-kecil" style="background:#9ca3af; color:white; border:none; border-radius:6px;">👁 Lihat</a>
                        <?php if($row['status'] == 'selesai'): ?>
                            <a href="cetak.php?id=<?= $row['id_pengajuan'] ?>" target="_blank" class="btn-sekunder btn-kecil" style="border:1px solid #000; border-radius:6px; color:#000;">📥</a>
                        <?php endif; ?>
                    </td>
                  </tr>
                  <?php endwhile; ?>
              <?php else: ?>
                  <tr><td colspan="6" style="text-align:center; padding:2rem;">Data riwayat kosong.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </main>
    </div>
  </div>
  <script src="js/main.js"></script>
</body>
</html>
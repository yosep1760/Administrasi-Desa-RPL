<?php
require 'koneksi.php';

// Lindungi halaman
if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'petugas') {
    header("Location: login.php");
    exit;
}

$nama_petugas = $_COOKIE['nama'];

// LOGIKA 1: Teruskan ke Kades
if (isset($_GET['teruskan_id'])) {
    $id_pengajuan = (int)$_GET['teruskan_id'];
    $conn->query("UPDATE Pengajuan_Surat SET status='menunggu_persetujuan' WHERE id_pengajuan=$id_pengajuan");
    header("Location: petugas-masuk.php");
    exit;
}

// LOGIKA 2: Tolak Dokumen karena tidak lengkap
if (isset($_GET['tolak_id']) && isset($_GET['alasan'])) {
    $id_pengajuan = (int)$_GET['tolak_id'];
    $alasan = $conn->real_escape_string($_GET['alasan']);
    // Masukkan ke kolom catatan_kades meskipun yang menolak adalah petugas (untuk pesan alasan)
    $conn->query("UPDATE Pengajuan_Surat SET status='ditolak', catatan_kades='$alasan' WHERE id_pengajuan=$id_pengajuan");
    header("Location: petugas-masuk.php");
    exit;
}

// Ambil pengajuan yang baru masuk (status menunggu_verifikasi)
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
  <title>Verifikasi Surat Masuk - SiKosar</title>
  <link rel="stylesheet" href="../css/style.css" />
  <style>
      .tabel-custom { width: 100%; border-collapse: collapse; }
      .tabel-custom th, .tabel-custom td { padding: 12px 15px; border-bottom: 1px solid #e2e8f0; text-align: left; }
      .tabel-custom th { background-color: #f8fafc; font-weight: 600; color: #475569; }
      .tabel-custom tr:hover { background-color: #f1f5f9; }
      .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center;}
      .modal-content { background-color: #fff; padding: 20px; border-radius: 8px; width: 400px; max-width: 90%; }
      .modal-header { font-size: 1.25rem; font-weight: bold; margin-bottom: 15px; display:flex; justify-content:space-between;}
      .close-modal { cursor: pointer; color: #dc2626; font-size: 1.5rem; font-weight:bold; }
  </style>
</head>
<body>

  <div class="layout-dashboard">
    <?php include 'sidebar.php'; ?>

    <div class="konten-dashboard">
      <header class="header-dashboard">
        <div style="display:flex;align-items:center;gap:0.75rem;">
          <button id="tombolBukaSidebar" class="tombol-hamburger" style="display:flex;">
            <span></span><span></span><span></span>
          </button>
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
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <div>
              <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;">Verifikasi Surat Masuk</h1>
              <p style="color:var(--warna-teks-muda);margin-top:0.25rem;">Periksa kelengkapan KTP/KK warga, lalu teruskan ke Kepala Desa.</p>
            </div>
        </div>

        <div class="kartu-form" style="padding:0; overflow-x:auto;">
          <table class="tabel-custom">
            <thead>
              <tr>
                <th>Waktu Masuk</th>
                <th>Nama Warga</th>
                <th>Jenis Surat</th>
                <th>Cek Dokumen</th>
                <th style="text-align:center;">Aksi Validasi</th>
              </tr>
            </thead>
            <tbody>
              <?php if($data_surat->num_rows > 0): ?>
                <?php while($row = $data_surat->fetch_assoc()): ?>
                <tr>
                  <td><?= date('d M Y, H:i', strtotime($row['tanggal_pengajuan'])) ?></td>
                  <td><strong><?= htmlspecialchars($row['nama_warga']) ?></strong></td>
                  <td><?= htmlspecialchars($row['nama_surat']) ?></td>
                  <td>
                      <a href="detail-surat.php?id=<?= $row['id_pengajuan'] ?>" class="btn-sekunder btn-kecil" style="text-decoration:none;">🔍 Buka File Warga</a>
                  </td>
                  <td style="text-align:center;">
                      <div style="display:flex; gap:0.5rem; justify-content:center;">
                          <a href="?teruskan_id=<?= $row['id_pengajuan'] ?>" class="btn-primer btn-kecil" style="background:#3b82f6; border:none; text-decoration:none;" onclick="return confirm('Dokumen valid? Teruskan ke Kepala Desa?');">➡️ Teruskan Kades</a>
                          <button type="button" class="btn-sekunder btn-kecil" style="color:#dc2626; border-color:#fca5a5;" onclick="bukaModalTolak(<?= $row['id_pengajuan'] ?>)">❌ Tolak</button>
                      </div>
                  </td>
                </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding: 30px; color:#64748b;">Belum ada pengajuan surat baru dari warga.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </main>
    </div>
  </div>

  <div id="modalTolak" class="modal">
      <div class="modal-content">
          <div class="modal-header">
              <span>Tolak Dokumen Warga</span>
              <span class="close-modal" onclick="tutupModalTolak()">&times;</span>
          </div>
          <form id="formTolak" action="" method="GET">
              <input type="hidden" name="tolak_id" id="tolak_id" value="">
              <div class="grup-form">
                  <label class="label-form">Alasan Tolak (misal: "Foto KTP buram, mohon upload ulang"):</label>
                  <textarea name="alasan" class="input-form" rows="4" required placeholder="Tuliskan pesan untuk warga..."></textarea>
              </div>
              <button type="submit" class="btn-primer" style="background:#dc2626; border:none; width:100%;">Tolak Pengajuan</button>
          </form>
      </div>
  </div>

  <script src="../js/main.js"></script>
  <script>
      function bukaModalTolak(id) {
          document.getElementById('tolak_id').value = id;
          document.getElementById('modalTolak').style.display = 'flex';
      }
      function tutupModalTolak() {
          document.getElementById('modalTolak').style.display = 'none';
      }
  </script>
</body>
</html>
<?php
require 'koneksi.php';

if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'petugas') {
    header("Location: login.php");
    exit;
}

$nama_petugas = $_COOKIE['nama'];

// Hitung statistik
$c_menunggu = $conn->query("SELECT COUNT(*) as c FROM Pengajuan_Surat WHERE status='menunggu_verifikasi'")->fetch_assoc()['c'];
$c_diproses = $conn->query("SELECT COUNT(*) as c FROM Pengajuan_Surat WHERE status IN ('menunggu_persetujuan', 'disetujui')")->fetch_assoc()['c'];
$c_selesai = $conn->query("SELECT COUNT(*) as c FROM Pengajuan_Surat WHERE status='selesai'")->fetch_assoc()['c'];

// Ambil 5 pengajuan terbaru yang masuk ke meja petugas
$query = "
    SELECT ps.*, u.nama_lengkap AS nama_warga, js.nama_surat 
    FROM Pengajuan_Surat ps
    JOIN Users u ON ps.id_user = u.id_user 
    JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis
    WHERE ps.status = 'menunggu_verifikasi'
    ORDER BY ps.tanggal_pengajuan ASC LIMIT 5
";
$data_masuk = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Petugas - SiKosar</title>
  <link rel="stylesheet" href="../css/style.css" />
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
        <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;margin-bottom:1.5rem;">
          Dashboard Petugas
        </h1>

        <div class="grid-statistik">
          <div class="kartu-statistik">
            <div class="statistik-angka"><?= $c_menunggu ?> <span class="statistik-naik" style="color:var(--warna-aksen);">!</span></div>
            <div class="statistik-label">Menunggu Verifikasi Anda</div>
          </div>
          <div class="kartu-statistik">
            <div class="statistik-angka"><?= $c_diproses ?> <span class="statistik-naik">↑</span></div>
            <div class="statistik-label">Sedang Diproses (ACC Kades / Cetak)</div>
          </div>
          <div class="kartu-statistik">
            <div class="statistik-angka"><?= $c_selesai ?> <span class="statistik-naik">↑</span></div>
            <div class="statistik-label">Surat Selesai (Siap Ambil)</div>
          </div>
        </div>

        <div class="kartu-tabel" style="margin-top: 2rem;">
          <div class="kartu-tabel-header">
            <h3>Antrean Verifikasi Dokumen Masuk</h3>
            <a href="petugas-masuk.php">Lihat semua &rarr;</a>
          </div>

          <?php if ($data_masuk->num_rows > 0): ?>
            <?php while($row = $data_masuk->fetch_assoc()): ?>
                <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--warna-border); display:flex; justify-content:space-between; align-items:center;">
                    <div style="display:flex; flex-direction:column; gap:0.25rem;">
                        <strong style="color:var(--warna-teks);"><?= htmlspecialchars($row['nama_warga']) ?> — <?= htmlspecialchars($row['nama_surat']) ?></strong>
                        <span style="color:var(--warna-teks-muda);font-size:0.85rem;">Tanggal: <?= date('d M Y, H:i', strtotime($row['tanggal_pengajuan'])) ?></span>
                    </div>
                    <div style="display:flex; gap:0.5rem;">
                        <a href="detail-surat.php?id=<?= $row['id_pengajuan'] ?>&from=masuk" class="btn-primer btn-kecil" style="text-decoration:none;">Periksa Dokumen</a>
                    </div>
                </div>
            <?php endwhile; ?>
          <?php else: ?>
            <div style="padding:2rem; text-align:center; color:var(--warna-teks-muda);">
                Hore! Belum ada surat baru yang perlu diverifikasi.
            </div>
          <?php endif; ?>
        </div>
      </main>
    </div>
  </div>

  <script src="../js/main.js"></script>
</body>
</html>
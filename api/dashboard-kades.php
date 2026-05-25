<?php
require 'koneksi.php';

// Lindungi halaman: Pastikan yang login HANYA KADES (Gunakan COOKIE)
if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'kades') {
    header("Location: login.php");
    exit;
}

$nama_kades = $_COOKIE['nama'];

// Logika Kades untuk menyetujui surat
if (isset($_GET['approve_id'])) {
    $id_surat = (int)$_GET['approve_id'];
    $conn->query("UPDATE surat SET status='Selesai' WHERE id=$id_surat");
    header("Location: dashboard-kades.php");
    exit;
}
if (isset($_GET['reject_id'])) {
    $id_surat = (int)$_GET['reject_id'];
    $conn->query("UPDATE surat SET status='Ditolak' WHERE id=$id_surat");
    header("Location: dashboard-kades.php");
    exit;
}

// Hitung statistik khusus Kades
$c_total = $conn->query("SELECT COUNT(*) as c FROM surat")->fetch_assoc()['c'];
$c_menunggu = $conn->query("SELECT COUNT(*) as c FROM surat WHERE status='Menunggu' OR status='Diproses'")->fetch_assoc()['c'];
$c_persetujuan = $conn->query("SELECT COUNT(*) as c FROM surat WHERE status='Persetujuan Kades'")->fetch_assoc()['c'];
$c_selesai = $conn->query("SELECT COUNT(*) as c FROM surat WHERE status='Selesai'")->fetch_assoc()['c'];

// Ambil pengajuan yang butuh persetujuan Kades ATAU pengajuan terbaru
$query = "SELECT surat.*, pengguna.nama AS nama_warga 
          FROM surat 
          JOIN pengguna ON surat.id_warga = pengguna.id 
          ORDER BY FIELD(status, 'Persetujuan Kades') DESC, surat.id DESC LIMIT 5";
$data_surat = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Kepala Desa - NamaWeb</title>
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
          <a href="dashboard-kades.php" class="sidebar-link aktif"><span class="sidebar-link-ikon">🏠</span>Home</a>
        </div>
        <div class="sidebar-label">Layanan <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="#" class="sidebar-link"><span class="sidebar-link-ikon">📩</span>Request Surat</a>
          <a href="#" class="sidebar-link"><span class="sidebar-link-ikon">✅</span>Surat Disetujui</a>
          <a href="#" class="sidebar-link"><span class="sidebar-link-ikon">❌</span>Surat Ditolak</a>
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
        <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;margin-bottom:1.5rem;">
          Dashboard
        </h1>

        <div class="grid-statistik">
          <div class="kartu-statistik">
            <div class="statistik-angka"><?= $c_total ?> <span class="statistik-naik">↑</span></div>
            <div class="statistik-label">Total Pengajuan</div>
          </div>
          <div class="kartu-statistik">
            <div class="statistik-angka"><?= $c_menunggu ?> <span class="statistik-naik">↑</span></div>
            <div class="statistik-label">Menunggu / Diproses</div>
          </div>
          <div class="kartu-statistik">
            <div class="statistik-angka"><?= $c_persetujuan ?> <span class="statistik-naik" style="color:var(--warna-aksen);">!</span></div>
            <div class="statistik-label">Butuh Persetujuan</div>
          </div>
          <div class="kartu-statistik">
            <div class="statistik-angka"><?= $c_selesai ?> <span class="statistik-naik">↑</span></div>
            <div class="statistik-label">Selesai</div>
          </div>
        </div>

        <div class="kartu-tabel" style="margin-top: 2rem;">
          <div class="kartu-tabel-header">
            <h3>Antrean Persetujuan Kepala Desa</h3>
            <a href="#">Lihat semua &rarr;</a>
          </div>

          <?php if ($data_surat->num_rows > 0): ?>
            <?php while($row = $data_surat->fetch_assoc()): ?>
                <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--warna-border); display:flex; justify-content:space-between; align-items:center;">
                    <div style="display:flex; flex-direction:column; gap:0.25rem;">
                        <strong style="color:var(--warna-teks);"><?= htmlspecialchars($row['nama_warga']) ?> — <?= htmlspecialchars($row['jenis_surat']) ?></strong>
                        <span style="color:var(--warna-teks-muda);font-size:0.85rem;">Status: <?= $row['status'] ?> | Tgl: <?= date('d M Y', strtotime($row['tanggal'])) ?></span>
                    </div>
                    
                    <?php if($row['status'] == 'Persetujuan Kades'): ?>
                        <div style="display:flex; gap:0.5rem;">
                            <a href="?approve_id=<?= $row['id'] ?>" class="btn-primer btn-kecil" style="background:#22c55e; border:none;">✔ Setujui</a>
                            <a href="?reject_id=<?= $row['id'] ?>" class="btn-sekunder btn-kecil" style="color:#ef4444; border-color:#ef4444;">✖ Tolak</a>
                        </div>
                    <?php else: ?>
                        <span style="font-size:0.85rem; font-weight:bold; color:var(--warna-teks-muda);">Telah Diproses</span>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
          <?php else: ?>
            <div style="padding:2rem; text-align:center; color:var(--warna-teks-muda);">
                Belum ada surat yang memerlukan persetujuan.
            </div>
          <?php endif; ?>
        </div>

      </main>
    </div>
  </div>

  <script src="../js/main.js"></script>
</body>
</html>
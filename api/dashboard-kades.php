<?php
require 'koneksi.php';

// Lindungi halaman: Pastikan yang login HANYA KADES (Gunakan COOKIE)
if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'kepala_desa') {
    header("Location: login.php");
    exit;
}

$nama_kades = $_COOKIE['nama'];

// Hitung statistik khusus Kades
$c_total = $conn->query("SELECT COUNT(*) as c FROM Pengajuan_Surat")->fetch_assoc()['c'];
$c_menunggu = $conn->query("SELECT COUNT(*) as c FROM Pengajuan_Surat WHERE status='menunggu_verifikasi'")->fetch_assoc()['c'];
$c_persetujuan = $conn->query("SELECT COUNT(*) as c FROM Pengajuan_Surat WHERE status='menunggu_persetujuan'")->fetch_assoc()['c'];
$c_selesai = $conn->query("SELECT COUNT(*) as c FROM Pengajuan_Surat WHERE status='selesai' OR status='disetujui'")->fetch_assoc()['c'];

// Ambil pengajuan terbaru yang butuh persetujuan Kades
$query = "
    SELECT ps.*, u.nama_lengkap AS nama_warga, js.nama_surat 
    FROM Pengajuan_Surat ps 
    JOIN Users u ON ps.id_user = u.id_user 
    JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis
    WHERE ps.status = 'menunggu_persetujuan'
    ORDER BY ps.tanggal_pengajuan ASC LIMIT 5
";
$data_surat = $conn->query($query);

// Data untuk Grafik (Surat berdasarkan jenis)
$query_grafik = $conn->query("
    SELECT js.nama_surat, COUNT(ps.id_pengajuan) as jumlah 
    FROM Pengajuan_Surat ps 
    JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis 
    GROUP BY ps.id_jenis
");
$labels_grafik = [];
$data_grafik = [];
while($row = $query_grafik->fetch_assoc()) {
    $labels_grafik[] = $row['nama_surat'];
    $data_grafik[] = $row['jumlah'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Kepala Desa - SiKosar</title>
  <link rel="stylesheet" href="../css/style.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
          Dashboard Overview
        </h1>

        <div class="grid-statistik">
          <div class="kartu-statistik">
            <div class="statistik-angka"><?= $c_total ?> <span class="statistik-naik">↑</span></div>
            <div class="statistik-label">Total Pengajuan</div>
          </div>
          <div class="kartu-statistik">
            <div class="statistik-angka"><?= $c_menunggu ?> <span class="statistik-naik">↑</span></div>
            <div class="statistik-label">Verifikasi Petugas</div>
          </div>
          <div class="kartu-statistik" style="<?= ($c_persetujuan > 0) ? 'border: 2px solid #ef4444; background: #fff1f2;' : '' ?>">
            <div class="statistik-angka"><?= $c_persetujuan ?> <span class="statistik-naik" style="color:var(--warna-aksen);">!</span></div>
            <div class="statistik-label">Butuh ACC Anda</div>
          </div>
          <div class="kartu-statistik">
            <div class="statistik-angka"><?= $c_selesai ?> <span class="statistik-naik">↑</span></div>
            <div class="statistik-label">Selesai / Disetujui</div>
          </div>
        </div>

        <div style="display:grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-top: 2rem;">
            <div class="kartu-tabel">
              <div class="kartu-tabel-header">
                <h3>Antrean Persetujuan Kepala Desa</h3>
                <a href="kades-request.php">Lihat semua &rarr;</a>
              </div>

              <?php if ($data_surat->num_rows > 0): ?>
                <?php while($row = $data_surat->fetch_assoc()): ?>
                    <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--warna-border); display:flex; justify-content:space-between; align-items:center;">
                        <div style="display:flex; flex-direction:column; gap:0.25rem;">
                            <strong style="color:var(--warna-teks);"><?= htmlspecialchars($row['nama_warga']) ?></strong>
                            <span style="font-size:0.9rem; color:#475569;"><?= htmlspecialchars($row['nama_surat']) ?></span>
                            <span style="color:var(--warna-teks-muda);font-size:0.8rem;">⏳ Diajukan: <?= date('d M Y, H:i', strtotime($row['tanggal_pengajuan'])) ?></span>
                        </div>
                        
                        <div style="display:flex; gap:0.5rem;">
                            <a href="detail-surat.php?id=<?= $row['id_pengajuan'] ?>&from=masuk" class="btn-primer btn-kecil" style="background:#10b981; border:none; text-decoration:none;">✔️ Proses ACC</a>
                        </div>
                    </div>
                <?php endwhile; ?>
              <?php else: ?>
                <div style="padding:2rem; text-align:center; color:var(--warna-teks-muda);">
                    Belum ada surat terbaru yang memerlukan persetujuan.
                </div>
              <?php endif; ?>
            </div>

            <div class="kartu-tabel" style="padding: 1.5rem; text-align:center;">
                <h3 style="font-size:1.1rem; margin-bottom: 1rem; color:var(--warna-teks);">Statistik Surat Warga</h3>
                <canvas id="grafikSurat" style="width: 100%; height: 250px;"></canvas>
            </div>
        </div>

      </main>
    </div>
  </div>

  <script src="../js/main.js"></script>
  <script>
    // Inisialisasi Grafik
    const ctx = document.getElementById('grafikSurat').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($labels_grafik) ?>,
            datasets: [{
                label: 'Jumlah Pengajuan',
                data: <?= json_encode($data_grafik) ?>,
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
  </script>
</body>
</html>
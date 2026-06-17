<?php
require 'koneksi.php';

if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'kepala_desa') {
    header("Location: login.php");
    exit;
}
$nama_kades = $_COOKIE['nama'];

// Data Kartu Statistik
$c_total = $conn->query("SELECT COUNT(*) as c FROM Pengajuan_Surat")->fetch_assoc()['c'];
$c_menunggu = $conn->query("SELECT COUNT(*) as c FROM Pengajuan_Surat WHERE status='menunggu_verifikasi'")->fetch_assoc()['c'];
$c_persetujuan = $conn->query("SELECT COUNT(*) as c FROM Pengajuan_Surat WHERE status='menunggu_persetujuan'")->fetch_assoc()['c'];
$c_selesai = $conn->query("SELECT COUNT(*) as c FROM Pengajuan_Surat WHERE status='selesai'")->fetch_assoc()['c'];

// Data untuk Grafik Pie (Chart.js)
$q_chart = $conn->query("SELECT js.nama_surat, COUNT(ps.id_pengajuan) as total FROM Pengajuan_Surat ps JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis GROUP BY ps.id_jenis");
$label_grafik = [];
$data_grafik = [];
while($r = $q_chart->fetch_assoc()) {
    $label_grafik[] = $r['nama_surat'];
    $data_grafik[] = $r['total'];
}

// Data Tabel Antrean
$data_surat = $conn->query("SELECT ps.*, u.nama_lengkap AS nama_warga, js.nama_surat FROM Pengajuan_Surat ps JOIN Users u ON ps.id_user = u.id_user JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis WHERE ps.status = 'menunggu_persetujuan' ORDER BY ps.tanggal_pengajuan ASC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Kepala Desa - Desa Kosar</title>
  <link rel="stylesheet" href="../css/style.css" />
  <!-- Script Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

  <div class="layout-dashboard">
    <!-- PANGGIL SIDEBAR DINAMIS -->
    <?php include 'sidebar.php'; ?>

    <div class="konten-dashboard">
      <header class="header-dashboard">
        <div style="display:flex;align-items:center;gap:0.75rem;">
          <button id="tombolBukaSidebar" class="tombol-hamburger" style="display:flex;"><span></span><span></span><span></span></button>
          <div class="header-pengguna">
            <h3>Halo, <?= htmlspecialchars($nama_kades) ?></h3>
            <span>Kepala Desa Kosar</span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;">
            <button type="submit" class="avatar-pengguna" title="Keluar" onclick="return confirm('Keluar dari sistem?');" style="cursor:pointer;">👤</button>
        </form>
      </header>

      <main class="area-konten">
        <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;margin-bottom:1.5rem;">Dashboard Kepala Desa</h1>

        <!-- Kartu Statistik -->
        <div class="grid-statistik">
          <div class="kartu-statistik"><div class="statistik-angka"><?= $c_total ?> ↗</div><div class="statistik-label">Total Pengajuan</div></div>
          <div class="kartu-statistik"><div class="statistik-angka"><?= $c_menunggu ?> ↗</div><div class="statistik-label">Verifikasi Petugas</div></div>
          <div class="kartu-statistik"><div class="statistik-angka"><?= $c_persetujuan ?> ❗</div><div class="statistik-label">Butuh Persetujuan</div></div>
          <div class="kartu-statistik"><div class="statistik-angka"><?= $c_selesai ?> ↗</div><div class="statistik-label">Surat Selesai</div></div>
        </div>

        <!-- GRAFIK CHART.JS (POIN 3) -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-top: 2rem;">
            <div class="kartu-form">
                <h3 style="margin-bottom: 1rem; font-weight: 700;">Tren Pengajuan Per Bulan</h3>
                <canvas id="barChart" style="width: 100%; height: 250px;"></canvas>
            </div>
            <div class="kartu-form">
                <h3 style="margin-bottom: 1rem; font-weight: 700;">Komposisi Jenis Surat</h3>
                <canvas id="pieChart" style="width: 100%; height: 250px;"></canvas>
            </div>
        </div>

      </main>
    </div>
  </div>

  <script src="../js/main.js"></script>
  <script>
    // Inisialisasi Grafik Pie (Jenis Surat)
    const ctxPie = document.getElementById('pieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($label_grafik) ?>,
            datasets: [{
                data: <?= json_encode($data_grafik) ?>,
                backgroundColor: ['#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', '#ef4444']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // Inisialisasi Grafik Bar (Simulasi Tren)
    const ctxBar = document.getElementById('barChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
            datasets: [{
                label: 'Jumlah Pengajuan',
                data: [12, 19, 15, 25, 22, 30],
                backgroundColor: '#3b82f6',
                borderRadius: 4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
  </script>
</body>
</html>
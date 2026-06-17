<?php
require 'koneksi.php';

// Cek COOKIE
if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'warga') {
    header("Location: login.php");
    exit;
}

$id_user = (int)$_COOKIE['user_id'];
$nama_warga = $_COOKIE['nama'];

// Ambil data statistik warga
$q_total = $conn->query("SELECT COUNT(*) as total FROM Pengajuan_Surat WHERE id_user = $id_user")->fetch_assoc()['total'];
$q_proses = $conn->query("SELECT COUNT(*) as proses FROM Pengajuan_Surat WHERE id_user = $id_user AND status = 'menunggu_verifikasi'")->fetch_assoc()['proses'];
$q_kades = $conn->query("SELECT COUNT(*) as kades FROM Pengajuan_Surat WHERE id_user = $id_user AND status = 'menunggu_persetujuan'")->fetch_assoc()['kades'];
$q_setuju = $conn->query("SELECT COUNT(*) as setuju FROM Pengajuan_Surat WHERE id_user = $id_user AND status IN ('disetujui', 'selesai')")->fetch_assoc()['setuju'];

// Ambil 3 pengajuan terakhir
$query_terakhir = "
    SELECT ps.*, js.nama_surat 
    FROM Pengajuan_Surat ps 
    JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis 
    WHERE ps.id_user = $id_user 
    ORDER BY ps.tanggal_pengajuan DESC LIMIT 3
";
$riwayat_terbaru = $conn->query($query_terakhir);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Warga - SiKosar</title>
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
            <h3>Halo, <?= htmlspecialchars($nama_warga) ?></h3>
            <span>Warga Desa</span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;">
            <button type="submit" class="avatar-pengguna" title="Keluar" onclick="return confirm('Yakin ingin keluar?');" style="cursor:pointer;">👤</button>
        </form>
      </header>

      <main class="area-konten">
        <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;margin-bottom:1.5rem;">
          Dashboard Saya
        </h1>

        <div class="grid-statistik">
          <div class="kartu-statistik">
            <div class="statistik-angka"><?= $q_total ?> <span class="statistik-naik">↑</span></div>
            <div class="statistik-label">Total Diajukan</div>
          </div>
          <div class="kartu-statistik">
            <div class="statistik-angka"><?= $q_proses ?></div>
            <div class="statistik-label">Diperiksa Petugas</div>
          </div>
          <div class="kartu-statistik">
            <div class="statistik-angka"><?= $q_kades ?></div>
            <div class="statistik-label">Menunggu Kades</div>
          </div>
          <div class="kartu-statistik">
            <div class="statistik-angka"><?= $q_setuju ?> <span class="statistik-naik">✓</span></div>
            <div class="statistik-label">Surat Selesai</div>
          </div>
        </div>

        <div class="kartu-tabel" style="margin-top: 2rem;">
          <div class="kartu-tabel-header">
            <h3>Pengajuan Surat Terbaru Saya</h3>
            <a href="riwayat.php">Lihat semua &rarr;</a>
          </div>
          
          <?php if($riwayat_terbaru->num_rows > 0): ?>
            <div class="tabel-wrapper" style="overflow-x:auto;">
              <table style="width:100%; text-align:left; border-collapse:collapse;">
                <thead style="border-bottom:1px solid var(--warna-border);">
                  <tr>
                    <th style="padding:1rem;">Tanggal</th>
                    <th style="padding:1rem;">Jenis Surat</th>
                    <th style="padding:1rem;">Status</th>
                    <th style="padding:1rem;">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while($row = $riwayat_terbaru->fetch_assoc()): 
                        $status = $row['status'];
                        if($status == 'menunggu_verifikasi') { $badge = "badge-menunggu"; $teks_status = "Diperiksa Petugas"; }
                        elseif($status == 'menunggu_persetujuan') { $badge = "badge-verifikasi"; $teks_status = "Menunggu Kades"; }
                        elseif($status == 'disetujui') { $badge = "badge-verifikasi"; $teks_status = "Disetujui / Tahap Cetak"; }
                        elseif($status == 'selesai') { $badge = "badge-disetujui"; $teks_status = "Selesai (Siap Ambil)"; }
                        elseif($status == 'ditolak') { $badge = "badge-ditolak"; $teks_status = "Ditolak"; }
                  ?>
                  <tr style="border-bottom:1px solid var(--warna-border);">
                    <td style="padding:1rem;"><?= date('d M Y', strtotime($row['tanggal_pengajuan'])) ?></td>
                    <td style="padding:1rem; font-weight:600;"><?= htmlspecialchars($row['nama_surat']) ?></td>
                    <td style="padding:1rem;"><span class="badge <?= $badge ?>"><?= $teks_status ?></span></td>
                    <td style="padding:1rem;"><a href="detail-surat.php?id=<?= $row['id_pengajuan'] ?>" class="btn-sekunder btn-kecil" style="text-decoration:none;">Lacak Status</a></td>
                  </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div style="padding:2rem; text-align:center; color:var(--warna-teks-muda);">
                Belum ada pengajuan surat yang Anda buat. <br><br>
                <a href="pengajuan.php" class="btn-primer">Mulai Ajukan Surat</a>
            </div>
          <?php endif; ?>
        </div>

      </main>
    </div>
  </div>

  <script src="../js/main.js"></script>
</body>
</html>
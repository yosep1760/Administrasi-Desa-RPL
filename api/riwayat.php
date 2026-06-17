<?php
require 'koneksi.php';

// Lindungi halaman dengan COOKIE
if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'warga') {
    header("Location: login.php");
    exit;
}

$id_user = (int)$_COOKIE['user_id'];
$nama_warga = $_COOKIE['nama'];

// Ambil data riwayat surat dari tabel Pengajuan_Surat JOIN Jenis_Surat
$query_riwayat = $conn->query("
    SELECT ps.*, js.nama_surat 
    FROM Pengajuan_Surat ps
    JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis
    WHERE ps.id_user = $id_user 
    ORDER BY ps.tanggal_pengajuan DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Riwayat Pengajuan - SiKosar</title>
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
        <div style="margin-bottom:1.5rem;">
          <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;">Riwayat Pengajuan Surat</h1>
          <p style="color:var(--warna-teks-muda);margin-top:0.25rem;">Pantau status dokumen dan surat yang pernah Anda ajukan</p>
        </div>

        <div class="kartu-tabel">
          <div class="kartu-tabel-header">
            <h3>Semua Riwayat Pengajuan</h3>
          </div>

          <div class="tabel-wrapper" style="overflow-x:auto;">
            <table style="width:100%; text-align:left; border-collapse:collapse;">
              <thead style="border-bottom:1px solid var(--warna-border);">
                <tr>
                  <th style="padding:1rem;">ID</th>
                  <th style="padding:1rem;">Tanggal & Waktu</th>
                  <th style="padding:1rem;">Jenis Surat</th>
                  <th style="padding:1rem;">Status Saat Ini</th>
                  <th style="padding:1rem; text-align:center;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php if($query_riwayat->num_rows > 0): ?>
                  <?php while($row = $query_riwayat->fetch_assoc()): 
                        // Konversi ENUM database ke text badge
                        $status = $row['status'];
                        if($status == 'menunggu_verifikasi') { $badge = "badge-menunggu"; $teks_status = "Diperiksa Petugas"; }
                        elseif($status == 'menunggu_persetujuan') { $badge = "badge-verifikasi"; $teks_status = "Menunggu ACC Kades"; }
                        elseif($status == 'disetujui') { $badge = "badge-verifikasi"; $teks_status = "Disetujui / Tahap Cetak"; }
                        elseif($status == 'selesai') { $badge = "badge-disetujui"; $teks_status = "Selesai"; }
                        elseif($status == 'ditolak') { $badge = "badge-ditolak"; $teks_status = "Ditolak / Perlu Perbaikan"; }
                  ?>
                  <tr style="border-bottom:1px solid var(--warna-border);">
                    <td style="padding:1rem;">#<?= 1000 + $row['id_pengajuan'] ?></td>
                    <td style="padding:1rem;"><?= date('d M Y, H:i', strtotime($row['tanggal_pengajuan'])) ?></td>
                    <td style="padding:1rem; font-weight:600; color:var(--warna-teks);"><?= htmlspecialchars($row['nama_surat']) ?></td>
                    <td style="padding:1rem;"><span class="badge <?= $badge ?>"><?= $teks_status ?></span></td>
                    <td style="padding:1rem; text-align:center;">
                        <a href="detail-surat.php?id=<?= $row['id_pengajuan'] ?>" class="btn-sekunder btn-kecil" style="text-decoration:none;">Lacak Status</a>
                        <?php if($status == 'selesai'): ?>
                            <a href="cetak.php?id=<?= $row['id_pengajuan'] ?>" class="btn-primer btn-kecil" style="text-decoration:none; margin-left:5px;" target="_blank">🖨️ Cetak</a>
                        <?php endif; ?>
                    </td>
                  </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr>
                      <td colspan="5" style="text-align:center; padding:2rem; color:var(--warna-teks-muda);">
                          Belum ada riwayat pengajuan surat.
                      </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>

  <script src="../js/main.js"></script>
</body>
</html>
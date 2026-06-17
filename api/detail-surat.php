<?php
require 'koneksi.php';

if (!isset($_COOKIE['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user_login = (int)$_COOKIE['user_id'];
$nama_user = $_COOKIE['nama'];
$role_user = $_COOKIE['role'];

if (!isset($_GET['id'])) { header("Location: dashboard.php"); exit; }
$id_pengajuan = (int)$_GET['id'];

$query = $conn->query("SELECT ps.*, u.nama_lengkap, u.NIK, js.nama_surat FROM Pengajuan_Surat ps JOIN Users u ON ps.id_user = u.id_user JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis WHERE ps.id_pengajuan = $id_pengajuan");
if ($query->num_rows == 0) { echo "<script>alert('Surat tidak ditemukan!'); window.location.href='dashboard.php';</script>"; exit; }
$data = $query->fetch_assoc();

if ($role_user == 'warga' && $data['id_user'] != $id_user_login) { echo "<script>alert('Akses Ditolak!'); window.location.href='dashboard.php';</script>"; exit; }

$query_dokumen = $conn->query("SELECT * FROM Dokumen_Pengajuan WHERE id_pengajuan = $id_pengajuan");
$status_db = $data['status'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Detail Surat - Desa Kosar</title>
  <link rel="stylesheet" href="../css/style.css" />
  <style>
      .layout-detail { display: grid; grid-template-columns: 2fr 1.2fr; gap: 1.5rem; margin-top: 1rem; }
      
      /* Styling Timeline Tracker (Poin 10) */
      .tracker-box { background: #e2e8f0; border-radius: 12px; padding: 1.5rem; }
      .step { display: flex; gap: 1rem; margin-bottom: 1.5rem; position: relative; }
      .step:last-child { margin-bottom: 0; }
      .step-icon { width: 28px; height: 28px; border-radius: 50%; background: #cbd5e1; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: bold; color: white; z-index: 2; flex-shrink: 0; }
      .step.active .step-icon { background: #16a34a; }
      .step.rejected .step-icon { background: #ef4444; }
      .step-line { position: absolute; left: 13px; top: 28px; bottom: -1.5rem; width: 2px; background: #cbd5e1; z-index: 1; }
      .step:last-child .step-line { display: none; }
      .step-text h4 { font-size: 0.9rem; margin-bottom: 0.2rem; color: #1e293b; }
      .step-text p { font-size: 0.8rem; color: #64748b; }

      /* Tombol Aksi Figma (Poin 5) */
      .action-box { background: #e2e8f0; border-radius: 12px; padding: 1.5rem; text-align: center; margin-top: 1.5rem; }
      .btn-acc { background: #000; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold; width: 48%; }
      .btn-tolak { background: transparent; color: #000; border: 1px solid #000; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold; width: 48%; }
      
      @media (max-width: 768px) { .layout-detail { grid-template-columns: 1fr; } }
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
            <h3>Halo, <?= htmlspecialchars($nama_user) ?></h3>
            <span style="text-transform: capitalize;"><?= str_replace('_', ' ', $role_user) ?></span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;">
            <button type="submit" class="avatar-pengguna" title="Keluar">👤</button>
        </form>
      </header>

      <main class="area-konten">
        <a href="javascript:history.back()" class="btn-sekunder btn-kecil" style="border:none; color:var(--warna-info); padding:0; margin-bottom:1rem;">&larr; Kembali ke Riwayat</a>
        <h1 style="font-family:var(--font-judul);font-size:1.8rem;font-weight:700;"><?= htmlspecialchars($data['nama_surat']) ?></h1>
        <p style="color:var(--warna-teks-muda);">Nomor Pengajuan: PS - <?= str_pad($data['id_pengajuan'], 5, '0', STR_PAD_LEFT) ?></p>

        <div class="layout-detail">
            <!-- PANEL KIRI: INFO SURAT & DOKUMEN -->
            <div>
                <div class="kartu-form" style="background:#e2e8f0; border:none; margin-bottom:1rem;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                        <tr><td style="padding: 8px 0; width: 35%;">Jenis Surat:</td><td><strong><?= htmlspecialchars($data['nama_surat']) ?></strong></td></tr>
                        <tr><td style="padding: 8px 0;">Nama Pemohon:</td><td><?= htmlspecialchars($data['nama_lengkap']) ?></td></tr>
                        <tr><td style="padding: 8px 0;">Tanggal Pengajuan:</td><td><?= date('d M Y, H:i', strtotime($data['tanggal_pengajuan'])) ?></td></tr>
                        <tr><td style="padding: 8px 0;">Keperluan:</td><td><?= htmlspecialchars($data['keperluan']) ?></td></tr>
                    </table>
                </div>

                <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem; font-weight:700;">Dokumen Lampiran</h3>
                <?php while($doc = $query_dokumen->fetch_assoc()): ?>
                    <div style="background:#e2e8f0; padding:10px 15px; border-radius:8px; display:flex; justify-content:space-between; margin-bottom:8px; align-items:center;">
                        <span style="font-size:0.9rem;">📄 <?= htmlspecialchars($doc['jenis_dokumen']) ?> - <?= htmlspecialchars($doc['nama_file']) ?></span>
                        <a href="<?= htmlspecialchars($doc['file_dokumen']) ?>" target="_blank" style="color:var(--warna-info); text-decoration:none; font-size:0.85rem; font-weight:bold;">Lihat / Unduh</a>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- PANEL KANAN: TRACKING TIMELINE & TOMBOL AKSI -->
            <div>
                <div class="tracker-box">
                    <h3 style="font-size: 1.1rem; margin-bottom: 1.25rem; font-weight:700;">Riwayat Status</h3>
                    
                    <!-- Step 1: Diajukan -->
                    <div class="step active">
                        <div class="step-line"></div>
                        <div class="step-icon">✔</div>
                        <div class="step-text">
                            <h4>Diajukan Pada <?= date('d M Y', strtotime($data['tanggal_pengajuan'])) ?></h4>
                            <p>Menunggu verifikasi dari Petugas Desa.</p>
                        </div>
                    </div>

                    <!-- Step 2: Verifikasi Petugas -->
                    <?php 
                        $step2_class = ($status_db != 'menunggu_verifikasi') ? 'active' : '';
                        $step2_icon = ($status_db != 'menunggu_verifikasi') ? '✔' : '⏱';
                        if($status_db == 'ditolak' && $data['catatan_petugas']) { $step2_class = 'rejected'; $step2_icon = '✖'; }
                    ?>
                    <div class="step <?= $step2_class ?>">
                        <div class="step-line"></div>
                        <div class="step-icon"><?= $step2_icon ?></div>
                        <div class="step-text">
                            <h4>Verifikasi Petugas</h4>
                            <p><?= ($step2_class == 'rejected') ? "Ditolak: " . htmlspecialchars($data['catatan_petugas']) : "Petugas memeriksa kelengkapan berkas." ?></p>
                        </div>
                    </div>

                    <!-- Step 3: Persetujuan Kades -->
                    <?php 
                        $step3_class = ($status_db == 'disetujui' || $status_db == 'selesai') ? 'active' : '';
                        $step3_icon = ($status_db == 'disetujui' || $status_db == 'selesai') ? '✔' : '⏱';
                        if($status_db == 'ditolak' && $data['catatan_kades']) { $step3_class = 'rejected'; $step3_icon = '✖'; }
                    ?>
                    <div class="step <?= $step3_class ?>">
                        <div class="step-line"></div>
                        <div class="step-icon"><?= $step3_icon ?></div>
                        <div class="step-text">
                            <h4>Persetujuan Kepala Desa</h4>
                            <p><?= ($step3_class == 'rejected') ? "Ditolak Kades: " . htmlspecialchars($data['catatan_kades']) : "Menunggu tanda tangan digital Kepala Desa." ?></p>
                        </div>
                    </div>

                    <!-- Step 4: Selesai -->
                    <div class="step <?= ($status_db == 'selesai') ? 'active' : '' ?>">
                        <div class="step-icon"><?= ($status_db == 'selesai') ? '✔' : '🏁' ?></div>
                        <div class="step-text">
                            <h4>Selesai & Siap Unduh</h4>
                            <p>Surat telah diterbitkan dan dapat diunduh.</p>
                        </div>
                    </div>
                </div>

                <!-- KOTAK AKSI VERIFIKASI (Khusus Kades / Petugas) (POIN 5) -->
                <?php if ($role_user == 'petugas' && $status_db == 'menunggu_verifikasi'): ?>
                    <div class="action-box">
                        <h3 style="margin-bottom:15px; font-size:1rem;">Hasil Verifikasi Dokumen</h3>
                        <div style="display:flex; justify-content:space-between;">
                            <a href="petugas-masuk.php?teruskan_id=<?= $id_pengajuan ?>" class="btn-acc" onclick="return confirm('Terima berkas?');">✔ Terima</a>
                            <button class="btn-tolak" onclick="tolakSistem(<?= $id_pengajuan ?>, 'petugas')">✖ Tolak</button>
                        </div>
                    </div>
                <?php elseif ($role_user == 'kepala_desa' && $status_db == 'menunggu_persetujuan'): ?>
                    <div class="action-box">
                        <h3 style="margin-bottom:15px; font-size:1rem;">Persetujuan Kepala Desa</h3>
                        <div style="display:flex; justify-content:space-between;">
                            <a href="kades-request.php?approve_id=<?= $id_pengajuan ?>" class="btn-acc" onclick="return confirm('Setujui surat?');">✔ Setujui</a>
                            <button class="btn-tolak" onclick="tolakSistem(<?= $id_pengajuan ?>, 'kades')">✖ Tolak</button>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($role_user == 'warga' && $status_db == 'selesai'): ?>
                    <div class="action-box">
                        <a href="cetak.php?id=<?= $id_pengajuan ?>" target="_blank" class="btn-acc" style="width:100%; display:block; text-decoration:none;">🖨️ Cetak / Unduh Surat</a>
                    </div>
                <?php endif; ?>

            </div>
        </div>
      </main>
    </div>
  </div>

  <script src="../js/main.js"></script>
  <script>
    function tolakSistem(id, role) {
        let alasan = prompt("Tulis alasan penolakan/perbaikan:");
        if (alasan) {
            if(role === 'petugas') window.location.href = "petugas-masuk.php?tolak_id=" + id + "&catatan=" + encodeURIComponent(alasan);
            if(role === 'kades') window.location.href = "kades-request.php?reject_id=" + id + "&alasan=" + encodeURIComponent(alasan);
        }
    }
  </script>
</body>
</html>
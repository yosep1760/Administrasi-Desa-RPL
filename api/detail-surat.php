<?php
require 'koneksi.php';

// Load Library PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Lindungi halaman dengan COOKIE
if (!isset($_COOKIE['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user_login = (int)$_COOKIE['user_id'];
$nama_user = $_COOKIE['nama'];
$role_user = $_COOKIE['role'];

if (!isset($_GET['id'])) {
    header("Location: riwayat.php");
    exit;
}

$id_pengajuan = (int)$_GET['id'];

// Ambil data surat
$query = $conn->query("
    SELECT ps.*, u.nama_lengkap, u.NIK, u.email, js.nama_surat 
    FROM Pengajuan_Surat ps 
    JOIN Users u ON ps.id_user = u.id_user 
    JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis 
    WHERE ps.id_pengajuan = $id_pengajuan
");

if ($query->num_rows == 0) {
    echo "<script>alert('Surat tidak ditemukan!'); window.location.href='riwayat.php';</script>";
    exit;
}
$data = $query->fetch_assoc();

if ($role_user == 'warga' && $data['id_user'] != $id_user_login) {
    echo "<script>alert('Akses Ditolak!'); window.location.href='riwayat.php';</script>";
    exit;
}

// LOGIKA ACTION: ACC KADES
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action_acc']) && $role_user == 'kepala_desa') {
    $conn->query("UPDATE Pengajuan_Surat SET status = 'disetujui' WHERE id_pengajuan = $id_pengajuan");
    
    // ==========================================
    // KIRIM EMAIL NOTIFIKASI KE WARGA DENGAN PHPMAILER
    // ==========================================
    $to_warga = (isset($data['email']) && $data['email'] != '') ? $data['email'] : "warga@desakosar.dpdns.org"; 
    $subject = "Surat Anda Telah Disetujui! - SiKosar";
    $message = "Halo ".$data['nama_lengkap'].",<br><br>Kabar baik! Pengajuan <strong>".$data['nama_surat']."</strong> Anda telah disetujui (di-ACC) oleh Kepala Desa.<br>Saat ini surat sedang dalam tahap persiapan pencetakan/upload oleh Petugas.<br><br>Terima kasih.";

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'mail.desakosar.dpdns.org';  // Server SMTP
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sistem@desakosar.dpdns.org'; // Username Email
        $mail->Password   = 'kelompok5isthebest';         // Password Email
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;  // SSL
        $mail->Port       = 465;

        $mail->setFrom('sistem@desakosar.dpdns.org', 'Sistem SiKosar');
        $mail->addAddress($to_warga);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
    } catch (Exception $e) {
        // Email gagal kirim diabaikan agar ACC tetap masuk
    }
    // ==========================================

    echo "<script>alert('Surat telah sukses di-ACC!'); window.location.href='kades-disetujui.php';</script>";
    exit;
}

// Ambil lampiran
$query_dokumen = $conn->query("SELECT * FROM Dokumen_Pengajuan WHERE id_pengajuan = $id_pengajuan");

$status_db = $data['status'];
$status_text = 'Menunggu Petugas';
$step = 1;

if ($status_db == 'menunggu_persetujuan') { $status_text = 'Menunggu ACC Kades'; $step = 2; } 
elseif ($status_db == 'disetujui') { $status_text = 'Disetujui (Tahap Cetak)'; $step = 3; } 
elseif ($status_db == 'selesai') { $status_text = 'Selesai'; $step = 4; } 
elseif ($status_db == 'ditolak') { $status_text = 'Ditolak'; $step = 0; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Detail Pengajuan - SiKosar</title>
  <link rel="stylesheet" href="../css/style.css" />
  <style>
      .tabel-detail { width: 100%; border-collapse: collapse; margin-top: 1rem; }
      .tabel-detail th, .tabel-detail td { padding: 12px 15px; border-bottom: 1px solid #e2e8f0; text-align: left; }
      .tabel-detail th { width: 30%; color: #64748b; font-weight: 600; background-color: #f8fafc; }
      .badge-dokumen { background: #e2e8f0; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; color: #475569; margin-right: 5px; border: 1px solid #cbd5e1;}
      
      .tracking-card { background:#fff; padding:25px; border-radius:8px; border:1px solid #e2e8f0; margin-bottom:20px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); }
      .timeline-track { display:flex; justify-content:space-between; align-items:center; position:relative; margin-top:20px; }
      .timeline-track::before { content:''; position:absolute; top:15px; left:0; width:100%; height:4px; background:#e2e8f0; z-index:1; }
      .track-step { position:relative; z-index:2; text-align:center; flex:1; }
      .track-dot { width:34px; height:34px; background:#e2e8f0; border-radius:50%; margin:0 auto; display:flex; align-items:center; justify-content:center; font-weight:bold; color:#64748b; border:4px solid #fff;}
      .track-step.aktif .track-dot { background:#3b82f6; color:#fff; }
      .track-step.done .track-dot { background:#10b981; color:#fff; }
      .track-step.tolak .track-dot { background:#ef4444; color:#fff; }
      .track-text { font-size:0.85rem; margin-top:8px; font-weight:600; color:#475569;}
      .track-step.aktif .track-text { color:#3b82f6; }
      
      .btn-acc-kades { background:#10b981; color:white; width:100%; padding:15px; font-size:1.1rem; border:none; border-radius:6px; font-weight:bold; cursor:pointer; margin-top:20px; box-shadow:0 4px 10px rgba(16,185,129,0.3); transition:all 0.3s; display:flex; justify-content:center; align-items:center; gap:10px;}
      .btn-acc-kades:hover { background:#059669; transform:translateY(-2px); }
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
            <h3>Halo, <?= htmlspecialchars($nama_user) ?></h3>
            <span>Sistem Layanan Desa</span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;">
            <button type="submit" class="avatar-pengguna" title="Keluar">👤</button>
        </form>
      </header>

      <main class="area-konten">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
          <div>
            <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;">Detail Pengajuan Surat</h1>
            <p style="color:var(--warna-teks-muda);margin-top:0.25rem;">ID Referensi: #SKSR-<?= 1000 + $id_pengajuan ?></p>
          </div>
          <button onclick="history.back()" class="btn-sekunder">Kembali</button>
        </div>

        <div class="tracking-card">
            <h3 style="margin-bottom:5px; font-size:1.1rem;">Status Saat Ini: <span style="color:#3b82f6;"><?= $status_text ?></span></h3>
            <p style="font-size:0.9rem; color:#64748b;">Melacak progress dokumen Anda secara real-time.</p>
            
            <div class="timeline-track">
                <div class="track-step <?= ($step >= 1 || $step == 0) ? 'done' : '' ?>">
                    <div class="track-dot">✓</div>
                    <div class="track-text">Diajukan</div>
                </div>
                <div class="track-step <?= ($step == 1 && $status_db != 'ditolak') ? 'aktif' : ($step > 1 ? 'done' : ($status_db == 'ditolak' ? 'tolak' : '')) ?>">
                    <div class="track-dot"><?= ($step > 1) ? '✓' : '2' ?></div>
                    <div class="track-text">Verifikasi Petugas</div>
                </div>
                <div class="track-step <?= ($step == 2) ? 'aktif' : ($step > 2 ? 'done' : '') ?>">
                    <div class="track-dot"><?= ($step > 2) ? '✓' : '3' ?></div>
                    <div class="track-text">ACC Kades</div>
                </div>
                <div class="track-step <?= ($step == 3) ? 'aktif' : ($step == 4 ? 'done' : '') ?>">
                    <div class="track-dot"><?= ($step == 4) ? '✓' : '4' ?></div>
                    <div class="track-text">Proses Cetak</div>
                </div>
                <div class="track-step <?= ($step == 4) ? 'done' : '' ?>">
                    <div class="track-dot"><?= ($step == 4) ? '✓' : '5' ?></div>
                    <div class="track-text">Selesai</div>
                </div>
            </div>
        </div>

        <div class="kartu-form">
            <h3 style="font-size:1.1rem; border-bottom:1px solid #e2e8f0; padding-bottom:10px;">Informasi Dokumen</h3>
            <table class="tabel-detail">
                <tr><th>Nama Pemohon</th><td><?= htmlspecialchars($data['nama_lengkap']) ?></td></tr>
                <tr><th>NIK</th><td><?= htmlspecialchars($data['NIK']) ?></td></tr>
                <tr><th>Jenis Surat</th><td><strong style="color:var(--warna-aksen);"><?= htmlspecialchars($data['nama_surat']) ?></strong></td></tr>
                <tr><th>Tanggal Pengajuan</th><td><?= date('d M Y, H:i', strtotime($data['tanggal_pengajuan'])) ?></td></tr>
                <tr>
                    <th>Detail / Keperluan</th>
                    <td>
                        <span style="white-space: pre-wrap; font-family:monospace; background:#f8fafc; padding:10px; display:block; border-radius:4px; border:1px solid #e2e8f0;"><?= htmlspecialchars($data['keperluan']) ?></span>
                    </td>
                </tr>
            </table>

            <h3 style="font-size:1.1rem; border-bottom:1px solid #e2e8f0; padding-bottom:10px; margin-top:30px;">Dokumen Pendukung</h3>
            <table class="tabel-detail">
                <?php while($dok = $query_dokumen->fetch_assoc()): ?>
                <tr>
                    <th><?= htmlspecialchars($dok['jenis_dokumen']) ?></th>
                    <td>
                        <span class="badge-dokumen"><?= htmlspecialchars($dok['nama_file']) ?></span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
            
            <?php if ($role_user == 'kepala_desa' && $status_db == 'menunggu_persetujuan'): ?>
                <form method="POST" action="detail-surat.php?id=<?= $id_pengajuan ?>">
                    <input type="hidden" name="action_acc" value="1">
                    <button type="submit" class="btn-acc-kades" onclick="return confirm('Apakah Anda yakin menyetujui (ACC) surat ini agar segera diproses cetak oleh Petugas?');">
                        <span>✅</span> Setujui / ACC Surat Ini
                    </button>
                </form>
            <?php endif; ?>
        </div>
      </main>
    </div>
  </div>

  <script src="../js/main.js"></script>
</body>
</html>
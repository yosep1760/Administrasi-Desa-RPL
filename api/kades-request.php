<?php
require 'koneksi.php';

// Panggil helper email
require_once 'kirim_email.php';

// Lindungi halaman: Pastikan yang login HANYA KADES
if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'kepala_desa') {
    header("Location: login.php");
    exit;
}

$nama_kades = $_COOKIE['nama'];

// ==========================================
// LOGIKA 1: KADES MENYETUJUI SURAT
// ==========================================
if (isset($_GET['approve_id'])) {
    $id_pengajuan = (int)$_GET['approve_id'];
    
    // Status diubah menjadi "disetujui" agar masuk ke antrean Upload Petugas
    $conn->query("UPDATE Pengajuan_Surat SET status='disetujui' WHERE id_pengajuan=$id_pengajuan");
    
    // Cari email pemohon (Warga) untuk dikabari
    $q_warga = $conn->query("SELECT u.email, u.nama_lengkap FROM Pengajuan_Surat ps JOIN Users u ON ps.id_user = u.id_user WHERE ps.id_pengajuan = $id_pengajuan");
    if($q_warga->num_rows > 0) {
        $warga = $q_warga->fetch_assoc();
        $pesan_warga = "<h3>Selamat {$warga['nama_lengkap']}!</h3>
                        <p>Surat pengajuan Anda telah <b>DISETUJUI</b> dan ditandatangani digital oleh Kepala Desa Kosar.</p>
                        <p>Saat ini surat sedang dalam tahap penerbitan PDF oleh Petugas. Silakan cek menu Riwayat secara berkala.</p>
                        <br><p>Salam,<br>Pemerintah Desa Kosar</p>";
        kirimEmail($warga['email'], "Surat Anda Disetujui! - Desa Kosar", $pesan_warga);
    }

    header("Location: kades-request.php");
    exit;
}

// ==========================================
// LOGIKA 2: KADES MENOLAK SURAT
// ==========================================
if (isset($_GET['reject_id']) && isset($_GET['alasan'])) {
    $id_pengajuan = (int)$_GET['reject_id'];
    $alasan = $conn->real_escape_string($_GET['alasan']);
    
    // Menyimpan alasan penolakan dan mengubah status menjadi 'ditolak'
    $conn->query("UPDATE Pengajuan_Surat SET status='ditolak', catatan_kades='$alasan' WHERE id_pengajuan=$id_pengajuan");
    
    // Kirim email penolakan ke Warga
    $q_warga = $conn->query("SELECT u.email, u.nama_lengkap FROM Pengajuan_Surat ps JOIN Users u ON ps.id_user = u.id_user WHERE ps.id_pengajuan = $id_pengajuan");
    if($q_warga->num_rows > 0) {
        $warga = $q_warga->fetch_assoc();
        $pesan_warga = "<h3>Mohon Maaf {$warga['nama_lengkap']},</h3>
                        <p>Surat pengajuan Anda <b>DITOLAK</b> oleh Kepala Desa Kosar dengan alasan berikut:</p>
                        <p style='background:#fee2e2; color:#991b1b; padding:10px; border-left:4px solid #ef4444;'><i>\"$alasan\"</i></p>
                        <p>Silakan ajukan ulang dengan memperbaiki data sesuai catatan di atas.</p>
                        <br><p>Salam,<br>Pemerintah Desa Kosar</p>";
        kirimEmail($warga['email'], "Status Pengajuan Ditolak - Desa Kosar", $pesan_warga);
    }
    
    header("Location: kades-request.php");
    exit;
}

// Ambil pengajuan yang statusnya "menunggu_persetujuan"
$query = "
    SELECT ps.*, u.nama_lengkap AS nama_warga, js.nama_surat 
    FROM Pengajuan_Surat ps 
    JOIN Users u ON ps.id_user = u.id_user 
    JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis 
    WHERE ps.status = 'menunggu_persetujuan'
    ORDER BY ps.tanggal_pengajuan ASC
"; 
$data_surat = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Approval Surat - Desa Kosar</title>
  <link rel="stylesheet" href="../css/style.css" />
</head>
<body>

  <div class="layout-dashboard">
    <!-- PANGGIL SIDEBAR MASTER -->
    <?php include 'sidebar.php'; ?>

    <div class="konten-dashboard">
      <header class="header-dashboard">
        <div style="display:flex;align-items:center;gap:0.75rem;">
          <button id="tombolBukaSidebar" class="tombol-hamburger" style="display:flex;">
            <span></span><span></span><span></span>
          </button>
          <div class="header-pengguna">
            <h3>Halo, <?= htmlspecialchars($nama_kades) ?></h3>
            <span>Kepala Desa Kosar</span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;">
            <button type="submit" class="avatar-pengguna" title="Keluar" onclick="return confirm('Keluar dari sistem?');">👤</button>
        </form>
      </header>

      <main class="area-konten">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
          <div>
            <h1 style="font-family:var(--font-judul);font-size:1.8rem;font-weight:700;">Daftar Approval Surat</h1>
            <p style="color:var(--warna-teks-muda);">Daftar permohonan yang telah diverifikasi kelengkapannya oleh petugas.</p>
          </div>
        </div>

        <div class="kartu-tabel">
          <table class="tabel-data">
            <thead>
              <tr>
                <th>No</th>
                <th>Tgl Masuk</th>
                <th>Pemohon</th>
                <th>Jenis Surat</th>
                <th>Keperluan</th>
                <th style="text-align:center;">Review & Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($data_surat->num_rows > 0): ?>
                  <?php $no = 1; while($row = $data_surat->fetch_assoc()): ?>
                    <tr>
                      <td><?= $no++ ?></td>
                      <td><?= date('d M Y, H:i', strtotime($row['tanggal_pengajuan'])) ?></td>
                      <td><strong><?= htmlspecialchars($row['nama_warga']) ?></strong></td>
                      <td><?= htmlspecialchars($row['nama_surat']) ?></td>
                      <td><?= htmlspecialchars($row['keperluan']) ?></td>
                      <td style="display:flex; gap:0.5rem; justify-content:center;">
                          
                          <a href="detail-surat.php?id=<?= $row['id_pengajuan'] ?>" class="btn-sekunder btn-kecil" style="text-decoration:none;" title="Lihat Lampiran & Catatan Petugas">🔍 Cek</a>

                          <a href="?approve_id=<?= $row['id_pengajuan'] ?>" class="btn-primer btn-kecil" style="background:#22c55e; border:none; text-decoration:none;" onclick="return confirm('Menyetujui surat ini? (Sistem akan otomatis mengirim email ke warga).');">✔ Setujui</a>
                          
                          <button onclick="tolakSuratKades(<?= $row['id_pengajuan'] ?>)" class="btn-sekunder btn-kecil" style="color:#ef4444; border-color:#ef4444; cursor:pointer;">✖ Tolak</button>
                      </td>
                    </tr>
                  <?php endwhile; ?>
              <?php else: ?>
                  <tr>
                    <td colspan="6" style="text-align:center; padding: 20px;">Belum ada surat di daftar approval.</td>
                  </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </main>
    </div>
  </div>

  <script src="../js/main.js"></script>
  
  <script>
    function tolakSuratKades(idPengajuan) {
        let alasan = prompt("Tulis alasan penolakan mutlak dari Kepala Desa (Alasan ini akan dikirim ke Email warga):");
        
        if (alasan != null && alasan.trim() !== "") {
            window.location.href = "?reject_id=" + idPengajuan + "&alasan=" + encodeURIComponent(alasan);
        } else if (alasan != null) {
            alert("Proses dibatalkan! Alasan penolakan tidak boleh kosong.");
        }
    }
  </script>
</body>
</html>
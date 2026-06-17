<?php
require 'koneksi.php';

// Lindungi halaman
if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'petugas') {
    header("Location: login.php");
    exit;
}

$nama_petugas = $_COOKIE['nama'];
$pesan = "";

// LOGIKA UPLOAD SURAT PDF
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_pengajuan'])) {
    $id_pengajuan = (int)$_POST['id_pengajuan'];
    $no_surat = $conn->real_escape_string($_POST['nomor_surat']);
    $tgl_surat = $conn->real_escape_string($_POST['tanggal_surat']);
    
    if (!empty($no_surat) && !empty($tgl_surat) && isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] == 0) {
        $nama_file = $conn->real_escape_string($_FILES['file_pdf']['name']);
        
        $ekstensi = pathinfo($nama_file, PATHINFO_EXTENSION);
        if (strtolower($ekstensi) == 'pdf') {
            
            // Ubah status jadi selesai dan simpan info surat
            $query_update = "UPDATE Pengajuan_Surat SET 
                             status = 'selesai', 
                             no_surat = '$no_surat', 
                             tanggal_surat = '$tgl_surat', 
                             file_surat = '$nama_file' 
                             WHERE id_pengajuan = $id_pengajuan";
            
            if ($conn->query($query_update)) {
                $pesan = "<div style='background: #dcfce7; color: #16a34a; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #bbf7d0;'>
                            <strong>Berhasil!</strong> Dokumen surat telah di-upload. Warga kini dapat mengunduh/mencetak suratnya di akun mereka.
                          </div>";
            }
        } else {
            $pesan = "<div style='background: #fee2e2; color: #dc2626; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #fecaca;'>
                        <strong>Gagal!</strong> File surat yang diunggah harus berformat PDF.
                      </div>";
        }
    } else {
        $pesan = "<div style='background: #fee2e2; color: #dc2626; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #fecaca;'>
                    <strong>Peringatan!</strong> Harap lengkapi Nomor Surat, Tanggal Surat, dan File PDF.
                  </div>";
    }
}

// Ambil pengajuan yang sudah di-ACC Kades (status = disetujui)
$query = "
    SELECT ps.*, u.nama_lengkap AS nama_warga, js.nama_surat 
    FROM Pengajuan_Surat ps 
    JOIN Users u ON ps.id_user = u.id_user 
    JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis 
    WHERE ps.status = 'disetujui'
    ORDER BY ps.tanggal_pengajuan ASC
"; 
$data_surat = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Upload Surat Selesai - SiKosar</title>
  <link rel="stylesheet" href="../css/style.css" />
  <style>
      .tabel-custom { width: 100%; border-collapse: collapse; }
      .tabel-custom th, .tabel-custom td { padding: 12px 15px; border-bottom: 1px solid #e2e8f0; text-align: left; }
      .tabel-custom th { background-color: #f8fafc; font-weight: 600; color: #475569; }
      .tabel-custom tr:hover { background-color: #f1f5f9; }
      .form-upload { display: none; background: #f0f9ff; padding: 15px; border: 1px dashed #0ea5e9; border-radius: 8px; margin-top: 10px; }
      .form-upload.aktif { display: block; }
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
            <h3>Halo, <?= htmlspecialchars($nama_petugas) ?></h3>
            <span>Petugas Layanan</span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;">
            <button type="submit" class="avatar-pengguna" title="Keluar">👤</button>
        </form>
      </header>

      <main class="area-konten">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
          <div>
              <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;">Upload Dokumen Surat</h1>
              <p style="color:var(--warna-teks-muda);margin-top:0.25rem;">Upload hasil cetak surat (PDF) yang sudah di-ACC & Ditandatangani Kades.</p>
          </div>
        </div>

        <?= $pesan ?>

        <div class="kartu-form" style="padding:0; overflow-x:auto;">
          <table class="tabel-custom">
            <thead>
              <tr>
                <th>No. Referensi</th>
                <th>Nama Warga</th>
                <th>Jenis Surat</th>
                <th>Status</th>
                <th style="text-align:center;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($data_surat->num_rows > 0): ?>
                  <?php while($row = $data_surat->fetch_assoc()): ?>
                    <tr>
                      <td>#SKSR-<?= 1000 + $row['id_pengajuan'] ?></td>
                      <td><strong><?= htmlspecialchars($row['nama_warga']) ?></strong></td>
                      <td><?= htmlspecialchars($row['nama_surat']) ?></td>
                      <td><span class="badge badge-disetujui">✔ Telah Di-ACC Kades</span></td>
                      <td style="text-align:center;">
                          <button onclick="bukaFormUpload(<?= $row['id_pengajuan'] ?>)" class="btn-primer btn-kecil" style="background:#3b82f6; border:none; cursor:pointer;">📤 Upload File</button>
                      </td>
                    </tr>
                    
                    <!-- BARIS TERSEMBUNYI UNTUK FORM UPLOAD -->
                    <tr id="baris-upload-<?= $row['id_pengajuan'] ?>" style="display:none; background:#f8fafc;">
                        <td colspan="5">
                            <form method="POST" action="" enctype="multipart/form-data" class="form-upload aktif">
                                <input type="hidden" name="id_pengajuan" value="<?= $row['id_pengajuan'] ?>">
                                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; align-items:end;">
                                    <div class="grup-form" style="margin:0;">
                                        <label class="label-form" style="font-size:0.8rem; color:#0369a1;">Nomor Surat Terbit</label>
                                        <input type="text" name="nomor_surat" class="input-form" placeholder="Contoh: 145/DS-KOSAR/2026" required>
                                    </div>
                                    <div class="grup-form" style="margin:0;">
                                        <label class="label-form" style="font-size:0.8rem; color:#0369a1;">Tanggal Surat</label>
                                        <input type="date" name="tanggal_surat" class="input-form" required>
                                    </div>
                                    <div class="grup-form" style="margin:0;">
                                        <label class="label-form" style="font-size:0.8rem; color:#0369a1;">Pilih File Surat (PDF)</label>
                                        <input type="file" name="file_pdf" accept="application/pdf" class="input-form" required style="padding-top:5px; background:white;">
                                    </div>
                                </div>
                                <div style="margin-top:1rem; text-align:right;">
                                    <button type="button" onclick="tutupFormUpload(<?= $row['id_pengajuan'] ?>)" class="btn-sekunder btn-kecil">Batal</button>
                                    <button type="submit" class="btn-primer btn-kecil" style="background:#16a34a; border:none;">Simpan & Selesai</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                  <?php endwhile; ?>
              <?php else: ?>
                  <tr>
                    <td colspan="5" style="text-align:center; padding: 30px; color:#64748b;">Belum ada surat yang menunggu untuk di-upload.</td>
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
    function bukaFormUpload(id) {
        document.getElementById('baris-upload-' + id).style.display = 'table-row';
    }
    function tutupFormUpload(id) {
        document.getElementById('baris-upload-' + id).style.display = 'none';
    }
  </script>
</body>
</html>
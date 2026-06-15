<?php
require 'koneksi.php';

// Lindungi halaman: Pastikan yang login HANYA PETUGAS
if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'petugas') {
    header("Location: login.php");
    exit;
}

$nama_petugas = $_COOKIE['nama'];
$pesan = "";

// LOGIKA UPLOAD (Sesuai PDM: Memasukkan data ke kolom khusus)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_pengajuan'])) {
    $id_pengajuan = (int)$_POST['id_pengajuan'];
    $no_surat = $conn->real_escape_string($_POST['nomor_surat']);
    $tgl_surat = $conn->real_escape_string($_POST['tanggal_surat']);
    
    // VALIDASI DATA
    if (!empty($no_surat) && !empty($tgl_surat) && isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] == 0) {
        $nama_file = $conn->real_escape_string($_FILES['file_pdf']['name']);
        
        // Pastikan ekstensi file adalah PDF
        $ekstensi = pathinfo($nama_file, PATHINFO_EXTENSION);
        if (strtolower($ekstensi) == 'pdf') {
            
            // [UPDATE PDM] Simpan ke kolom khusus dan ubah status jadi 'selesai'
            $query_update = "UPDATE Pengajuan_Surat SET 
                             status = 'selesai', 
                             no_surat = '$no_surat', 
                             tanggal_surat = '$tgl_surat', 
                             file_surat = '$nama_file' 
                             WHERE id_pengajuan = $id_pengajuan";
            
            if ($conn->query($query_update)) {
                $pesan = "<div style='background: #dcfce7; color: #16a34a; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #bbf7d0;'>
                            <strong>Berhasil!</strong> File PDF dan Nomor Surat berhasil disimpan. Status diubah menjadi Selesai.
                          </div>";
            }
        } else {
            $pesan = "<div style='background: #fee2e2; color: #dc2626; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #fecaca;'>
                        <strong>Error!</strong> File yang diunggah harus berformat PDF.
                      </div>";
        }
    } else {
        $pesan = "<div style='background: #fee2e2; color: #dc2626; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #fecaca;'>
                    <strong>Error!</strong> Harap isi Nomor Surat, Tanggal Surat, dan pilih file PDF.
                  </div>";
    }
}

// [UPDATE PDM] Ambil pengajuan yang statusnya "disetujui" (Telah di-ACC Kades)
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
  <title>Upload Surat - NamaWeb</title>
  <link rel="stylesheet" href="../css/style.css" />
  <style>
      .form-upload { display: none; background: #f9fafb; padding: 15px; border: 1px dashed #cbd5e1; border-radius: 8px; margin-top: 10px; }
      .form-upload.aktif { display: block; }
  </style>
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
          <a href="dashboard-petugas.php" class="sidebar-link"><span class="sidebar-link-ikon">🏠</span>Home</a>
        </div>
        <div class="sidebar-label">Layanan <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="petugas-masuk.php" class="sidebar-link"><span class="sidebar-link-ikon">📩</span>Surat Masuk</a>
          <a href="petugas-diproses.php" class="sidebar-link"><span class="sidebar-link-ikon">⏳</span>Sedang Diproses</a>
          <a href="petugas-upload.php" class="sidebar-link aktif"><span class="sidebar-link-ikon">📤</span>Upload Surat (Selesai)</a>
          <a href="petugas-ditolak.php" class="sidebar-link"><span class="sidebar-link-ikon">❌</span>Surat Ditolak</a>
        </div>
        <div class="sidebar-label">Kelola Data <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="petugas-warga.php" class="sidebar-link"><span class="sidebar-link-ikon">👥</span>Data Warga</a>
        </div>
        <div class="sidebar-label">Pengaturan <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="profil.php" class="sidebar-link"><span class="sidebar-link-ikon">👤</span>Profil Saya</a>
        </div>
      </nav>
    </aside>

    <div class="konten-dashboard">
      <header class="header-dashboard">
        <div style="display:flex;align-items:center;gap:0.75rem;">
          <button id="tombolBukaSidebar" class="tombol-hamburger" style="display:flex;"><span></span><span></span><span></span></button>
          <div class="header-pengguna">
            <h3>Halo, <?= htmlspecialchars($nama_petugas) ?></h3>
            <span>Petugas Desa</span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;">
            <button type="submit" class="avatar-pengguna" title="Keluar" onclick="return confirm('Keluar dari sistem?');" style="cursor:pointer;">👤</button>
        </form>
      </header>

      <main class="area-konten">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
          <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;">Upload Dokumen Surat</h1>
        </div>

        <?= $pesan ?>

        <div class="kartu-tabel">
          <table class="tabel-data">
            <thead>
              <tr>
                <th>No</th>
                <th>Tanggal ACC</th>
                <th>Pemohon</th>
                <th>Jenis Surat</th>
                <th>Status</th>
                <th style="text-align:center;">Aksi</th>
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
                      <td><span class="badge" style="background:#dcfce7;color:#16a34a;">✔ Disetujui Kades</span></td>
                      <td style="text-align:center;">
                          <button onclick="bukaFormUpload(<?= $row['id_pengajuan'] ?>)" class="btn-primer btn-kecil" style="background:#3b82f6; border:none; cursor:pointer;">📤 Upload Dokumen</button>
                      </td>
                    </tr>
                    
                    <!-- BARIS TERSEMBUNYI UNTUK FORM UPLOAD -->
                    <tr id="baris-upload-<?= $row['id_pengajuan'] ?>" style="display:none;">
                        <td colspan="6">
                            <form method="POST" action="" enctype="multipart/form-data" class="form-upload aktif">
                                <input type="hidden" name="id_pengajuan" value="<?= $row['id_pengajuan'] ?>">
                                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; align-items:end;">
                                    <div class="grup-form" style="margin:0;">
                                        <label class="label-form" style="font-size:0.8rem;">Nomor Surat Terbit</label>
                                        <input type="text" name="nomor_surat" class="input-form" placeholder="Contoh: 145/DS-KOSAR/2026" required>
                                    </div>
                                    <div class="grup-form" style="margin:0;">
                                        <label class="label-form" style="font-size:0.8rem;">Tanggal Surat</label>
                                        <input type="date" name="tanggal_surat" class="input-form" required>
                                    </div>
                                    <div class="grup-form" style="margin:0;">
                                        <label class="label-form" style="font-size:0.8rem;">Pilih File (PDF)</label>
                                        <input type="file" name="file_pdf" accept="application/pdf" class="input-form" required style="padding-top:5px;">
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
                    <td colspan="6" style="text-align:center; padding: 20px;">Tidak ada surat yang menunggu untuk di-upload.</td>
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
    // Javascript untuk membuka/menutup form upload di bawah baris tabel
    function bukaFormUpload(id) {
        document.getElementById('baris-upload-' + id).style.display = 'table-row';
    }
    function tutupFormUpload(id) {
        document.getElementById('baris-upload-' + id).style.display = 'none';
    }
  </script>
</body>
</html>
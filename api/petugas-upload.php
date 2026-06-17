<?php
require 'koneksi.php';

if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'petugas') {
    header("Location: login.php");
    exit;
}
$nama_petugas = $_COOKIE['nama'];
$pesan = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_pengajuan'])) {
    $id_pengajuan = (int)$_POST['id_pengajuan'];
    $no_surat = $conn->real_escape_string($_POST['nomor_surat']);
    $tgl_surat = $conn->real_escape_string($_POST['tanggal_surat']);
    
    if (!empty($no_surat) && !empty($tgl_surat) && isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] == 0) {
        $nama_file = $conn->real_escape_string($_FILES['file_pdf']['name']);
        $ekstensi = pathinfo($nama_file, PATHINFO_EXTENSION);
        if (strtolower($ekstensi) == 'pdf') {
            $query_update = "UPDATE Pengajuan_Surat SET status = 'selesai', no_surat = '$no_surat', tanggal_surat = '$tgl_surat', file_surat = '$nama_file' WHERE id_pengajuan = $id_pengajuan";
            if ($conn->query($query_update)) {
                $pesan = "<div class='alert alert-sukses'><strong>Berhasil!</strong> Dokumen PDF diterbitkan.</div>";
            }
        } else {
            $pesan = "<div class='alert alert-bahaya'><strong>Error!</strong> File harus berformat PDF.</div>";
        }
    } else {
        $pesan = "<div class='alert alert-bahaya'><strong>Error!</strong> Form tidak lengkap.</div>";
    }
}

$query = "SELECT ps.*, u.nama_lengkap AS nama_warga, js.nama_surat FROM Pengajuan_Surat ps JOIN Users u ON ps.id_user = u.id_user JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis WHERE ps.status = 'disetujui' ORDER BY ps.tanggal_pengajuan ASC"; 
$data_surat = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Upload Surat - Desa Kosar</title>
  <link rel="stylesheet" href="css/style.css" />
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
            <span>Petugas Administrasi</span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;"><button type="submit" class="avatar-pengguna">👤</button></form>
      </header>

      <main class="area-konten">
        <h1 style="font-family:var(--font-judul);font-size:1.8rem;font-weight:700;margin-bottom:1.5rem;">Antrean Upload Surat (ACC Kades)</h1>
        <?= $pesan ?>

        <div class="kartu-tabel">
          <table class="tabel-data">
            <thead>
              <tr style="background-color: #d1d5db;">
                <th>#</th>
                <th>Pemohon</th>
                <th>Jenis Surat</th>
                <th>Tanggal ACC</th>
                <th style="text-align:center;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($data_surat->num_rows > 0): ?>
                  <?php $no = 1; while($row = $data_surat->fetch_assoc()): ?>
                    <tr>
                      <td><?= $no++ ?></td>
                      <td><strong><?= htmlspecialchars($row['nama_warga']) ?></strong></td>
                      <td><?= htmlspecialchars($row['nama_surat']) ?></td>
                      <td><?= date('d - m - Y', strtotime($row['tanggal_pengajuan'])) ?></td>
                      <td style="text-align:center;">
                          <button onclick="bukaFormUpload(<?= $row['id_pengajuan'] ?>)" class="btn-primer btn-kecil" style="background:#000; border:none; border-radius:6px; cursor:pointer;">📄 Upload Surat</button>
                      </td>
                    </tr>
                    
                    <!-- FORM UPLOAD SESUAI UI FIGMA -->
                    <tr id="baris-upload-<?= $row['id_pengajuan'] ?>" style="display:none; background-color:#f1f5f9;">
                        <td colspan="5" style="padding: 1.5rem;">
                            <div style="background:#cbd5e1; padding:2rem; border-radius:12px; max-width:600px; margin:0 auto;">
                                <h2 style="font-size:1.5rem; font-weight:700; margin-bottom:1.5rem;">Upload Surat</h2>
                                <form method="POST" action="" enctype="multipart/form-data">
                                    <input type="hidden" name="id_pengajuan" value="<?= $row['id_pengajuan'] ?>">
                                    <div class="grup-form">
                                        <label style="font-size:1rem; color:#374151;">Nomor Surat</label>
                                        <input type="text" name="nomor_surat" class="input-form" style="border-radius:8px; padding:10px;" required>
                                    </div>
                                    <div class="grup-form">
                                        <label style="font-size:1rem; color:#374151;">Tanggal Surat</label>
                                        <input type="date" name="tanggal_surat" class="input-form" style="border-radius:8px; padding:10px;" required>
                                    </div>
                                    <div class="grup-form">
                                        <label style="font-size:1rem; color:#374151;">Upload Surat (PDF)</label>
                                        <input type="file" name="file_pdf" accept="application/pdf" class="input-form" style="border-radius:8px; padding:7px; background:white;" required>
                                    </div>
                                    <div style="margin-top:1.5rem; display:flex; gap:10px;">
                                        <button type="submit" class="btn-primer" style="background:#000; font-weight:bold; border-radius:8px; padding:10px 20px;">Submit</button>
                                        <button type="button" onclick="tutupFormUpload(<?= $row['id_pengajuan'] ?>)" class="btn-sekunder" style="border-radius:8px; padding:10px 20px;">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </td>
                    </tr>
                  <?php endwhile; ?>
              <?php else: ?>
                  <tr><td colspan="5" style="text-align:center; padding: 20px;">Tidak ada surat yang menunggu untuk di-upload.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>
  <script src="js/main.js"></script>
  <script>
    function bukaFormUpload(id) { document.getElementById('baris-upload-' + id).style.display = 'table-row'; }
    function tutupFormUpload(id) { document.getElementById('baris-upload-' + id).style.display = 'none'; }
  </script>
</body>
</html>
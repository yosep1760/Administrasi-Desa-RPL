<?php
require 'koneksi.php';

// Memanggil file helper email
require_once 'kirim_email.php';

if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'warga') {
    header("Location: login.php");
    exit;
}

$id_user = (int)$_COOKIE['user_id'];
$nama_warga = $_COOKIE['nama'];
$pesan = "";

// Ambil data warga
$user_query = $conn->query("SELECT * FROM Users WHERE id_user = $id_user");
$user_data = $user_query->fetch_assoc();

$id_jenis = isset($_GET['id_jenis']) ? (int)$_GET['id_jenis'] : 1;

// Tentukan Judul Surat MVP
$judul_surat = "Surat Keterangan Domisili";
if ($id_jenis == 2) $judul_surat = "Surat Keterangan Usaha (SKU)";
if ($id_jenis == 3) $judul_surat = "Surat Pengantar SKCK";
if ($id_jenis == 4) $judul_surat = "Surat Keterangan Kehilangan";
if ($id_jenis == 5) $judul_surat = "Surat Keterangan Penghasilan";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $keperluan = $conn->real_escape_string($_POST['keperluan']);
    $tgl_sekarang = date('Y-m-d H:i:s');
    
    // 1. Simpan ke Pengajuan_Surat Utama
    $query_pengajuan = "INSERT INTO Pengajuan_Surat (id_user, id_jenis, tanggal_pengajuan, keperluan, status) 
                        VALUES ($id_user, $id_jenis, '$tgl_sekarang', '$keperluan', 'menunggu_verifikasi')";
    
    if ($conn->query($query_pengajuan) === TRUE) {
        $id_pengajuan = $conn->insert_id;

        // 2. Simpan Lampiran (KTP & KK)
        $dokumen = ['file_ktp' => 'KTP', 'file_kk' => 'KK'];
        foreach ($dokumen as $input_name => $jenis_dok) {
            if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] == 0) {
                $nama_file = $conn->real_escape_string($_FILES[$input_name]['name']);
                $conn->query("INSERT INTO Dokumen_Pengajuan (id_pengajuan, jenis_dokumen, nama_file, file_dokumen) 
                              VALUES ($id_pengajuan, '$jenis_dok', '$nama_file', 'uploads/$nama_file')");
            }
        }

        // 3. SIMPAN DATA DINAMIS KE Detail_Pengajuan
        $field_dinamis = ['lama_tinggal', 'alamat_domisili', 'nama_usaha', 'jenis_usaha', 'lama_usaha', 'barang_hilang', 'tanggal_hilang', 'kronologi', 'pekerjaan', 'tempat_kerja', 'penghasilan_perbulan'];
        
        foreach ($field_dinamis as $field) {
            if (isset($_POST[$field]) && !empty($_POST[$field])) {
                $nilai = $conn->real_escape_string($_POST[$field]);
                $nama_field = $conn->real_escape_string($field);
                $conn->query("INSERT INTO Detail_Pengajuan (id_pengajuan, nama_field, nilai_field) 
                              VALUES ($id_pengajuan, '$nama_field', '$nilai')");
            }
        }

        // ==========================================
        // 4. KIRIM EMAIL NOTIFIKASI KE PETUGAS
        // ==========================================
        $q_petugas = $conn->query("SELECT email FROM Users WHERE role='petugas' LIMIT 1");
        if ($q_petugas->num_rows > 0) {
            $email_petugas = $q_petugas->fetch_assoc()['email'];
            $judul = "Notifikasi Surat Baru Masuk - Desa Kosar";
            $pesan_email = "<h3>Halo Petugas Desa,</h3>
                      <p>Ada pengajuan <b>$judul_surat</b> baru dari warga atas nama <b>$nama_warga</b>.</p>
                      <p>Silakan login ke sistem untuk melakukan verifikasi berkas dan lampiran dokumen.</p>
                      <br><p>Salam,<br>Sistem Desa Kosar</p>";
            
            // Eksekusi kirim email
            kirimEmail($email_petugas, $judul, $pesan_email);
        }

        // Pesan Sukses untuk User
        $pesan = "<div class='alert alert-sukses'><strong>Berhasil!</strong> Permohonan $judul_surat telah diajukan. Pantau di menu Riwayat.</div>";
    } else {
        $pesan = "<div class='alert alert-bahaya'><strong>Gagal!</strong> Error: " . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pengajuan <?= $judul_surat ?> - Desa Kosar</title>
  <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
  <div class="layout-dashboard">
    <!-- PANGGIL SIDEBAR MASTER -->
    <?php include 'sidebar.php'; ?>

    <div class="konten-dashboard">
      <header class="header-dashboard">
        <div style="display:flex;align-items:center;gap:0.75rem;">
          <button id="tombolBukaSidebar" class="tombol-hamburger" style="display:flex;"><span></span><span></span><span></span></button>
          <div class="header-pengguna">
            <h3>Halo, <?= htmlspecialchars($nama_warga) ?></h3>
            <span>Warga Desa</span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;"><button type="submit" class="avatar-pengguna" title="Keluar">👤</button></form>
      </header>

      <main class="area-konten">
        <h1 style="font-family:var(--font-judul);font-size:1.8rem;font-weight:700;margin-bottom:1rem;">Permohonan <?= $judul_surat ?></h1>
        <?= $pesan ?>

        <form method="POST" enctype="multipart/form-data">
            <!-- BAGIAN 1: DATA DIRI UMUM -->
            <h3 style="font-size:1.1rem; font-weight:700; margin-bottom:1rem;">Isi Data Diri</h3>
            <div class="kartu-form" style="background:#e2e8f0; border:none; margin-bottom:2rem;">
                <div class="grid-form-2">
                    <div class="grup-form">
                        <label>Nama Lengkap</label>
                        <input type="text" class="input-form" value="<?= htmlspecialchars($user_data['nama_lengkap']) ?>" readonly style="background:#cbd5e1; cursor:not-allowed;">
                    </div>
                    <div class="grup-form">
                        <label>NIK</label>
                        <input type="text" class="input-form" value="<?= htmlspecialchars($user_data['NIK']) ?>" readonly style="background:#cbd5e1; cursor:not-allowed;">
                    </div>
                    <div class="grup-form">
                        <label>No. HP / WhatsApp</label>
                        <input type="text" class="input-form" value="<?= htmlspecialchars($user_data['no_hp']) ?>" readonly style="background:#cbd5e1; cursor:not-allowed;">
                    </div>
                    <div class="grup-form">
                        <label>Keperluan Pengajuan (Umum)</label>
                        <input type="text" name="keperluan" class="input-form" placeholder="Contoh: Syarat melamar kerja..." required>
                    </div>
                    <div class="grup-form">
                        <label>Upload KTP (PDF/JPG)</label>
                        <input type="file" name="file_ktp" class="input-form" accept=".pdf,.jpg,.png" required style="padding-top:7px;">
                    </div>
                    <div class="grup-form">
                        <label>Upload KK (PDF/JPG)</label>
                        <input type="file" name="file_kk" class="input-form" accept=".pdf,.jpg,.png" required style="padding-top:7px;">
                    </div>
                </div>
            </div>

            <!-- BAGIAN 2: FORM DINAMIS SESUAI MVP -->
            <h3 style="font-size:1.1rem; font-weight:700; margin-bottom:1rem;">Isi Data Spesifik Surat</h3>
            <div class="kartu-form" style="background:#e2e8f0; border:none; margin-bottom:2rem;">
                <div class="grid-form-2">
                    <?php if ($id_jenis == 1): // DOMISILI ?>
                        <div class="grup-form"><label>Lama Tinggal (Tahun/Bulan)</label><input type="text" name="lama_tinggal" class="input-form" required></div>
                        <div class="grup-form"><label>Alamat Domisili Saat Ini</label><input type="text" name="alamat_domisili" class="input-form" required></div>
                    
                    <?php elseif ($id_jenis == 2): // USAHA (SKU) ?>
                        <div class="grup-form"><label>Nama Usaha</label><input type="text" name="nama_usaha" class="input-form" required></div>
                        <div class="grup-form"><label>Jenis Usaha</label><input type="text" name="jenis_usaha" class="input-form" placeholder="Contoh: Kuliner, Jasa..." required></div>
                        <div class="grup-form"><label>Lama Usaha Berdiri</label><input type="text" name="lama_usaha" class="input-form" required></div>

                    <?php elseif ($id_jenis == 4): // KEHILANGAN ?>
                        <div class="grup-form"><label>Barang / Dokumen yang Hilang</label><input type="text" name="barang_hilang" class="input-form" required></div>
                        <div class="grup-form"><label>Tanggal Kehilangan</label><input type="date" name="tanggal_hilang" class="input-form" required></div>
                        <div class="grup-form" style="grid-column: span 2;"><label>Kronologi Singkat</label><textarea name="kronologi" class="input-form" rows="3" required></textarea></div>

                    <?php elseif ($id_jenis == 5): // PENGHASILAN ?>
                        <div class="grup-form"><label>Pekerjaan Saat Ini</label><input type="text" name="pekerjaan" class="input-form" required></div>
                        <div class="grup-form"><label>Nama Tempat Kerja / Instansi</label><input type="text" name="tempat_kerja" class="input-form" required></div>
                        <div class="grup-form"><label>Rata-rata Penghasilan per Bulan</label><input type="number" name="penghasilan_perbulan" class="input-form" placeholder="Contoh: 3000000" required></div>
                    
                    <?php else: // SKCK / DEFAULT ?>
                        <div class="grup-form" style="grid-column: span 2;">
                            <p style="font-size:0.85rem; color:#64748b;">*Tidak ada data spesifik tambahan yang diperlukan untuk surat ini. Silakan cek ulang data diri dan langsung klik Ajukan Surat.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div style="display:flex; gap:1rem;">
                <button type="submit" class="btn-primer" style="background:#000;">Ajukan Surat</button>
                <button type="reset" class="btn-sekunder">Reset Data</button>
            </div>
        </form>

      </main>
    </div>
  </div>
  <script src="../js/main.js"></script>
</body>
</html>
<?php
require 'koneksi.php';

// Load Library PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Lindungi halaman dengan COOKIE
if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'warga') {
    header("Location: login.php");
    exit;
}

$id_user = (int)$_COOKIE['user_id'];
$nama_warga = $_COOKIE['nama'];
$pesan = "";

// Ambil data detail warga
$user_query = $conn->query("SELECT * FROM Users WHERE id_user = $id_user");
$user_data = $user_query->fetch_assoc();

$id_jenis_param = isset($_GET['id_jenis']) ? (int)$_GET['id_jenis'] : 1;
$jenis_surat_query = $conn->query("SELECT * FROM Jenis_Surat");

// LOGIKA SUBMIT PENGAJUAN
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_jenis = (int)$_POST['id_jenis'];
    
    // Menyusun Keterangan / Keperluan Custom sesuai Form Dinamis
    $keperluan_custom = "";
    if(isset($_POST['custom_nama_pasangan']) && $_POST['custom_nama_pasangan'] != '') {
        $keperluan_custom .= "[Nama Pasangan: " . $_POST['custom_nama_pasangan'] . "] \n";
    }
    if(isset($_POST['custom_nama_usaha']) && $_POST['custom_nama_usaha'] != '') {
        $keperluan_custom .= "[Nama Usaha: " . $_POST['custom_nama_usaha'] . " | Bidang: " . $_POST['custom_bidang_usaha'] . "] \n";
    }
    if(isset($_POST['custom_alasan_sktm']) && $_POST['custom_alasan_sktm'] != '') {
        $keperluan_custom .= "[Keperluan SKTM: " . $_POST['custom_alasan_sktm'] . "] \n";
    }
    
    // Gabung form custom dengan keperluan dasar
    $keperluan = $conn->real_escape_string($keperluan_custom . "Catatan Tambahan: " . $_POST['keperluan']);
    $tgl_sekarang = date('Y-m-d H:i:s');
    $status_awal = 'menunggu_verifikasi'; 

    $query_pengajuan = "INSERT INTO Pengajuan_Surat (id_user, id_jenis, tanggal_pengajuan, keperluan, status) 
                        VALUES ($id_user, $id_jenis, '$tgl_sekarang', '$keperluan', '$status_awal')";
    
    if ($conn->query($query_pengajuan) === TRUE) {
        $id_pengajuan = $conn->insert_id; 

        // Upload Dokumen KTP
        if (isset($_FILES['file_ktp']) && $_FILES['file_ktp']['error'] == 0) {
            $nama_file_ktp = $conn->real_escape_string($_FILES['file_ktp']['name']);
            $conn->query("INSERT INTO Dokumen_Pengajuan (id_pengajuan, jenis_dokumen, nama_file, file_dokumen) 
                          VALUES ($id_pengajuan, 'KTP', '$nama_file_ktp', 'uploads/$nama_file_ktp')");
        }
        // Upload Dokumen KK
        if (isset($_FILES['file_kk']) && $_FILES['file_kk']['error'] == 0) {
            $nama_file_kk = $conn->real_escape_string($_FILES['file_kk']['name']);
            $conn->query("INSERT INTO Dokumen_Pengajuan (id_pengajuan, jenis_dokumen, nama_file, file_dokumen) 
                          VALUES ($id_pengajuan, 'KK', '$nama_file_kk', 'uploads/$nama_file_kk')");
        }

        // ==========================================
        // KIRIM EMAIL NOTIFIKASI DENGAN PHPMAILER
        // ==========================================
        $to_petugas = "petugas@desakosar.dpdns.org"; // Ganti dengan email penerima (petugas)
        $subject = "Pengajuan Surat Baru - SiKosar";
        $message = "Halo Petugas,<br><br>Ada pengajuan surat baru dari warga bernama: <strong>$nama_warga</strong>.<br>Mohon segera login ke dashboard SiKosar untuk melakukan verifikasi dokumen.<br><br>Terima Kasih.";

        $mail = new PHPMailer(true);
        try {
            // Pengaturan Server SMTP Rumahweb
            $mail->isSMTP();
            $mail->Host       = 'mail.desakosar.dpdns.org';  // Server SMTP
            $mail->SMTPAuth   = true;
            $mail->Username   = 'sistem@desakosar.dpdns.org'; // Username Email
            $mail->Password   = 'kelompok5isthebest';         // Password Email
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;  // SSL
            $mail->Port       = 465;                          // Port SSL

            // Pengirim & Penerima
            $mail->setFrom('sistem@desakosar.dpdns.org', 'Sistem SiKosar');
            $mail->addAddress($to_petugas);

            // Konten Email
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;

            $mail->send();
        } catch (Exception $e) {
            // Email gagal kirim (bisa diabaikan agar proses sistem tetap jalan)
        }
        // ==========================================

        $pesan = "<div style='background: #dcfce7; color: #16a34a; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #bbf7d0;'>
                    <strong>Pengajuan Berhasil!</strong> Surat Anda telah dikirim dan sistem telah mengirim notifikasi email ke petugas desa. Silakan cek menu Riwayat.
                  </div>";
    } else {
        $pesan = "<div style='background: #fee2e2; color: #dc2626; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #fecaca;'>
                    <strong>Gagal!</strong> Error: " . $conn->error . "
                  </div>";
    }
}

$judul_halaman = "Pengajuan Surat";
$jenis_surat_query->data_seek(0);
while($j = $jenis_surat_query->fetch_assoc()) {
    if ($j['id_jenis'] == $id_jenis_param) {
        $judul_halaman = $j['nama_surat'];
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pengajuan <?= htmlspecialchars($judul_halaman) ?> - SiKosar</title>
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
          <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;">Permohonan <?= htmlspecialchars($judul_halaman) ?></h1>
          <p style="color:var(--warna-teks-muda);margin-top:0.25rem;">Lengkapi formulir yang diminta sesuai dengan syarat desa</p>
        </div>

        <?= $pesan ?>

        <div class="kartu-form">
          <form method="POST" action="" enctype="multipart/form-data">
            
            <div style="margin-bottom:1.5rem; border-bottom:1px solid var(--warna-border); padding-bottom:0.5rem;">
              <h3 style="font-size:1rem; font-weight:700;">Informasi Pemohon</h3>
            </div>

            <div class="grup-form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.25rem;">
              <div class="grup-form">
                <label class="label-form">Nama Lengkap</label>
                <input type="text" class="input-form" value="<?= htmlspecialchars($user_data['nama_lengkap']) ?>" readonly style="background:#f3f4f6; cursor:not-allowed;" />
              </div>
              <div class="grup-form">
                <label class="label-form">NIK Terdaftar</label>
                <input type="text" class="input-form" value="<?= htmlspecialchars($user_data['NIK']) ?>" readonly style="background:#f3f4f6; cursor:not-allowed;" />
              </div>
            </div>

            <div class="grup-form" style="margin-bottom:1.25rem;">
              <label class="label-form" for="id_jenis">Pilih Jenis Surat</label>
              <select id="id_jenis" name="id_jenis" class="input-form" onchange="renderDynamicForm()" required>
                <?php 
                  $jenis_surat_query->data_seek(0);
                  while($j = $jenis_surat_query->fetch_assoc()): 
                ?>
                  <option value="<?= $j['id_jenis'] ?>" <?= ($j['id_jenis'] == $id_jenis_param) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($j['nama_surat']) ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>

            <!-- Wadah Form Dinamis -->
            <div id="wadahFormDinamis" style="margin-bottom:1.25rem;"></div>

            <div class="grup-form" style="margin-bottom:1.5rem;">
              <label class="label-form" for="keperluan">Keterangan Tambahan (Opsional)</label>
              <input type="text" id="keperluan" name="keperluan" class="input-form" placeholder="Jelaskan apabila ada pesan/keterangan lain..." />
            </div>

            <div style="margin-bottom:1.5rem; border-bottom:1px solid var(--warna-border); padding-bottom:0.5rem; margin-top:2rem;">
              <h3 style="font-size:1rem; font-weight:700;">Unggah Dokumen Pendukung</h3>
            </div>

            <div class="grup-form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:2rem;">
              <div class="grup-form">
                <label class="label-form">Scan KTP Asli (PDF/JPG/PNG)</label>
                <input type="file" name="file_ktp" class="input-form" accept=".pdf, .jpg, .jpeg, .png" required style="padding-top:7px;" />
              </div>
              <div class="grup-form">
                <label class="label-form">Scan Kartu Keluarga (PDF/JPG/PNG)</label>
                <input type="file" name="file_kk" class="input-form" accept=".pdf, .jpg, .jpeg, .png" required style="padding-top:7px;" />
              </div>
            </div>

            <div style="display:flex; gap:1rem;">
              <button type="submit" class="btn-primer">Kirim Pengajuan</button>
              <button type="reset" class="btn-sekunder">Bersihkan Form</button>
            </div>
          </form>
        </div>
      </main>
    </div>
  </div>

  <script src="../js/main.js"></script>
  <script>
    function renderDynamicForm() {
        const jenisId = document.getElementById('id_jenis').value;
        const wadah = document.getElementById('wadahFormDinamis');
        let html = '';

        if (jenisId == '1') { 
            html = `
            <div style="padding:15px; background:#f0f9ff; border-left:4px solid #0284c7; margin-bottom:15px; border-radius:4px;">
              <h4 style="margin-bottom:10px; color:#0369a1;">Data Tambahan Pernikahan</h4>
              <div class="grup-form">
                <label class="label-form">Nama Lengkap Pasangan</label>
                <input type="text" name="custom_nama_pasangan" class="input-form" placeholder="Masukkan nama pasangan..." required />
              </div>
            </div>`;
        } else if (jenisId == '2') { 
            html = `
            <div style="padding:15px; background:#f0f9ff; border-left:4px solid #0284c7; margin-bottom:15px; border-radius:4px;">
              <h4 style="margin-bottom:10px; color:#0369a1;">Detail Usaha</h4>
              <div class="grup-form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">
                  <div class="grup-form">
                    <label class="label-form">Nama Usaha / Toko</label>
                    <input type="text" name="custom_nama_usaha" class="input-form" placeholder="Contoh: Warung Berkah" required />
                  </div>
                  <div class="grup-form">
                    <label class="label-form">Bidang Usaha</label>
                    <input type="text" name="custom_bidang_usaha" class="input-form" placeholder="Contoh: Kuliner / Sembako" required />
                  </div>
              </div>
            </div>`;
        } else if (jenisId == '4') { 
            html = `
            <div style="padding:15px; background:#fefce8; border-left:4px solid #ca8a04; margin-bottom:15px; border-radius:4px;">
              <h4 style="margin-bottom:10px; color:#a16207;">Keterangan SKTM</h4>
              <div class="grup-form">
                <label class="label-form">Tujuan Pembuatan SKTM</label>
                <select name="custom_alasan_sktm" class="input-form" required>
                    <option value="">Pilih Tujuan...</option>
                    <option value="Keringanan Biaya Pendidikan (Sekolah/Kuliah)">Pendidikan (Sekolah / Kampus)</option>
                    <option value="Keringanan Biaya Rumah Sakit">Kesehatan (Rumah Sakit)</option>
                    <option value="Bantuan Sosial Desa/Pemerintah">Bantuan Sosial Desa</option>
                </select>
              </div>
            </div>`;
        }

        wadah.innerHTML = html;
    }

    window.onload = renderDynamicForm;
  </script>
</body>
</html>
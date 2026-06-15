<?php
require 'koneksi.php';

// Lindungi halaman dengan COOKIE
if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'warga') {
    header("Location: login.php");
    exit;
}

$id_user = (int)$_COOKIE['user_id'];
$nama_warga = $_COOKIE['nama'];
$pesan = "";

// Ambil data detail warga yang sedang login dari tabel Users
$user_query = $conn->query("SELECT * FROM Users WHERE id_user = $id_user");
$user_data = $user_query->fetch_assoc();

// Tangkap parameter ID Jenis dari URL (Default: 1)
$id_jenis_param = isset($_GET['id_jenis']) ? (int)$_GET['id_jenis'] : 1;

// Ambil daftar Jenis Surat dari database PDM
$jenis_surat_query = $conn->query("SELECT * FROM Jenis_Surat");

// LOGIKA SUBMIT PENGJUAN BARU
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_jenis = (int)$_POST['id_jenis'];
    $keperluan = $conn->real_escape_string($_POST['keperluan']);
    $tgl_sekarang = date('Y-m-d H:i:s');
    $status_awal = 'menunggu_verifikasi'; // Sesuai tipe ENUM di PDM

    // 1. Masukkan ke tabel Pengajuan_Surat
    $query_pengajuan = "INSERT INTO Pengajuan_Surat (id_user, id_jenis, tanggal_pengajuan, keperluan, status) 
                        VALUES ($id_user, $id_jenis, '$tgl_sekarang', '$keperluan', '$status_awal')";
    
    if ($conn->query($query_pengajuan) === TRUE) {
        $id_pengajuan = $conn->insert_id; // Ambil ID pengajuan yang baru saja dibuat

        // 2. Masukkan Dokumen KTP ke tabel Dokumen_Pengajuan
        if (isset($_FILES['file_ktp']) && $_FILES['file_ktp']['error'] == 0) {
            $nama_file_ktp = $conn->real_escape_string($_FILES['file_ktp']['name']);
            // (Catatan: Simulasi Vercel, kita simpan nama file ke DB sesuai PDM)
            $conn->query("INSERT INTO Dokumen_Pengajuan (id_pengajuan, jenis_dokumen, nama_file, file_dokumen) 
                          VALUES ($id_pengajuan, 'KTP', '$nama_file_ktp', 'uploads/$nama_file_ktp')");
        }

        // 3. Masukkan Dokumen KK ke tabel Dokumen_Pengajuan
        if (isset($_FILES['file_kk']) && $_FILES['file_kk']['error'] == 0) {
            $nama_file_kk = $conn->real_escape_string($_FILES['file_kk']['name']);
            $conn->query("INSERT INTO Dokumen_Pengajuan (id_pengajuan, jenis_dokumen, nama_file, file_dokumen) 
                          VALUES ($id_pengajuan, 'KK', '$nama_file_kk', 'uploads/$nama_file_kk')");
        }

        $pesan = "<div style='background: #dcfce7; color: #16a34a; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #bbf7d0;'>
                    <strong>Pengajuan Berhasil!</strong> Surat Anda beserta dokumen lampiran telah dikirim. Silakan cek menu Riwayat.
                  </div>";
    } else {
        $pesan = "<div style='background: #fee2e2; color: #dc2626; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #fecaca;'>
                    <strong>Gagal!</strong> Error Sistem: " . $conn->error . "
                  </div>";
    }
}

// Menentukan Judul Halaman berdasarkan URL Parameter
$judul_halaman = "Pengajuan Surat";
$jenis_surat_query->data_seek(0); // Reset pointer
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
  <title>Pengajuan <?= htmlspecialchars($judul_halaman) ?> - NamaWeb</title>
  <link rel="stylesheet" href="../css/style.css" />
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
        <div class="sidebar-label">Ajukan Surat <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="pengajuan.php?id_jenis=1" class="sidebar-link <?= ($id_jenis_param == 1) ? 'aktif' : '' ?>">
            <span class="sidebar-link-ikon">✉</span>Surat Pengantar Nikah
          </a>
          <a href="pengajuan.php?id_jenis=2" class="sidebar-link <?= ($id_jenis_param == 2) ? 'aktif' : '' ?>">
            <span class="sidebar-link-ikon">✉</span>Surat Keterangan Usaha
          </a>
          <a href="pengajuan.php?id_jenis=3" class="sidebar-link <?= ($id_jenis_param == 3) ? 'aktif' : '' ?>">
            <span class="sidebar-link-ikon">✉</span>Surat Keter. Domisili
          </a>
          <a href="pengajuan.php?id_jenis=4" class="sidebar-link <?= ($id_jenis_param == 4) ? 'aktif' : '' ?>">
            <span class="sidebar-link-ikon">✉</span>Surat Lainnya
          </a>
        </div>

        <div class="sidebar-label">Informasi <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="riwayat.php" class="sidebar-link"><span class="sidebar-link-ikon">🕐</span>Riwayat Pengajuan</a>
          <a href="profil.php" class="sidebar-link"><span class="sidebar-link-ikon">👤</span>Profil Saya</a>
        </div>
      </nav>
    </aside>

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
          <p style="color:var(--warna-teks-muda);margin-top:0.25rem;">Lengkapi formulir dan unggah dokumen persyaratan (Sesuai PDM Database)</p>
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
              <label class="label-form" for="id_jenis">Pilih Jenis Surat (Dari Database)</label>
              <select id="id_jenis" name="id_jenis" class="input-form" required>
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

            <div class="grup-form" style="margin-bottom:1.5rem;">
              <label class="label-form" for="keperluan">Keperluan / Alasan Pengajuan</label>
              <input type="text" id="keperluan" name="keperluan" class="input-form" placeholder="Jelaskan secara singkat tujuan pembuatan surat ini..." required />
            </div>

            <div style="margin-bottom:1.5rem; border-bottom:1px solid var(--warna-border); padding-bottom:0.5rem; margin-top:2rem;">
              <h3 style="font-size:1rem; font-weight:700;">Unggah Dokumen Pendukung</h3>
            </div>

            <div class="grup-form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:2rem;">
              <div class="grup-form">
                <label class="label-form">Scan KTP (PDF/JPG/PNG)</label>
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
</body>
</html>
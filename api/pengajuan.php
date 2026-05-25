<?php
require 'koneksi.php';

// Lindungi halaman dengan COOKIE
if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'warga') {
    header("Location: login.php");
    exit;
}

$id_warga = $_COOKIE['user_id'];
$nama_warga = $_COOKIE['nama'];
$pesan = "";

// 1. TANGKAP PARAMETER DARI URL (Contoh: pengajuan.php?jenis=nikah)
$jenis_param = isset($_GET['jenis']) ? $_GET['jenis'] : 'usaha'; // Default ke usaha

// 2. TENTUKAN JUDUL OTOMATIS BERDASARKAN URL
$judul_surat = "Surat Keterangan Usaha"; 
if ($jenis_param == 'nikah') {
    $judul_surat = "Surat Pengantar Nikah";
} elseif ($jenis_param == 'domisili') {
    $judul_surat = "Surat Keterangan Domisili";
} elseif ($jenis_param == 'lainnya') {
    $judul_surat = "Surat Lainnya (Lorem Ipsum)";
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nik = $conn->real_escape_string($_POST['nik']);
    $jenis_surat = $conn->real_escape_string($_POST['jenis_surat']);
    $keterangan = $conn->real_escape_string($_POST['keterangan']);

    // Simpan ke database
    $query = "INSERT INTO surat (id_warga, nik, jenis_surat, keterangan) VALUES ($id_warga, '$nik', '$jenis_surat', '$keterangan')";
    
    if ($conn->query($query) === TRUE) {
        $pesan = "<div style='background: #dcfce7; color: #16a34a; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #bbf7d0;'><strong>Berhasil!</strong> Pengajuan $jenis_surat telah dikirim. Silakan cek menu Riwayat.</div>";
    } else {
        $pesan = "<div style='background: #fee2e2; color: #dc2626; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #fecaca;'><strong>Gagal!</strong> Error: " . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pengajuan <?= $judul_surat ?> - NamaWeb</title>
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
        <a href="dashboard.php" class="sidebar-link">
          <span class="sidebar-link-ikon">📊</span>Dashboard
        </a>
        
        <div class="sidebar-label">Ajukan Surat <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="pengajuan.php?jenis=nikah" class="sidebar-link <?= ($jenis_param == 'nikah') ? 'aktif' : '' ?>">
            <span class="sidebar-link-ikon">✉</span>Surat Pengantar Nikah
          </a>
          <a href="pengajuan.php?jenis=usaha" class="sidebar-link <?= ($jenis_param == 'usaha') ? 'aktif' : '' ?>">
            <span class="sidebar-link-ikon">✉</span>Surat Keterangan Usaha
          </a>
          <a href="pengajuan.php?jenis=domisili" class="sidebar-link <?= ($jenis_param == 'domisili') ? 'aktif' : '' ?>">
            <span class="sidebar-link-ikon">✉</span>Surat Keterangan Domisili
          </a>
          <a href="pengajuan.php?jenis=lainnya" class="sidebar-link <?= ($jenis_param == 'lainnya') ? 'aktif' : '' ?>">
            <span class="sidebar-link-ikon">✉</span>Surat lorem ipsum
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
            <span>Warga</span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;">
            <button type="submit" class="avatar-pengguna" title="Keluar" onclick="return confirm('Yakin ingin keluar?');" style="cursor:pointer;">👤</button>
        </form>
      </header>

      <main class="area-konten">
        <div style="margin-bottom:1.5rem;">
          <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;">Permohonan <?= $judul_surat ?></h1>
          <p style="color:var(--warna-teks-muda);margin-top:0.25rem;">Isi formulir berikut dengan data yang benar dan lengkap</p>
        </div>

        <?= $pesan ?>

        <div class="kartu-form">
          <form method="POST" action="">
            <div style="margin-bottom:1.5rem; border-bottom:1px solid var(--warna-border); padding-bottom:0.5rem;">
              <h3 style="font-size:1rem; font-weight:700;">Isi Data Diri</h3>
            </div>

            <div class="grup-form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.25rem;">
              <div class="grup-form">
                <label class="label-form">Nama Lengkap</label>
                <input type="text" class="input-form" value="<?= htmlspecialchars($nama_warga) ?>" readonly style="background:#f3f4f6;" />
              </div>
              <div class="grup-form">
                <label class="label-form" for="nik">NIK</label>
                <input type="text" id="nik" name="nik" class="input-form" placeholder="Masukkan 16 digit NIK" required pattern="[0-9]{16}" title="NIK harus berupa 16 digit angka" />
              </div>
            </div>

            <div class="grup-form" style="margin-bottom:1.25rem;">
              <label class="label-form" for="jenis_surat">Jenis Surat</label>
              <select id="jenis_surat" name="jenis_surat" class="input-form" required>
                <option value="Surat Pengantar Nikah" <?= ($jenis_param == 'nikah') ? 'selected' : '' ?>>Surat Pengantar Nikah</option>
                <option value="Surat Keterangan Usaha" <?= ($jenis_param == 'usaha') ? 'selected' : '' ?>>Surat Keterangan Usaha</option>
                <option value="Surat Keterangan Domisili" <?= ($jenis_param == 'domisili') ? 'selected' : '' ?>>Surat Keterangan Domisili</option>
                <option value="Surat Lainnya" <?= ($jenis_param == 'lainnya') ? 'selected' : '' ?>>Surat Lainnya</option>
              </select>
            </div>

            <div class="grup-form" style="margin-bottom:2rem;">
              <label class="label-form" for="keterangan">Keperluan Pengajuan</label>
              <input type="text" id="keterangan" name="keterangan" class="input-form" placeholder="Contoh: Mengurus izin usaha / Pindah domisili / Syarat KUA" required />
            </div>

            <div style="display:flex; gap:1rem;">
              <button type="submit" class="btn-primer">Kirim Pengajuan</button>
              <button type="reset" class="btn-sekunder">Reset</button>
            </div>
          </form>
        </div>
      </main>
    </div>
  </div>

  <script src="../js/main.js"></script>
</body>
</html>
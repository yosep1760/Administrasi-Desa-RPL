<?php
session_start();
require 'koneksi.php';

// Lindungi halaman
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'warga') {
    header("Location: login.php");
    exit;
}

$id_warga = $_SESSION['user_id'];
$nama_warga = $_SESSION['nama'];
$pesan = "";

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nik = $conn->real_escape_string($_POST['nik']);
    $jenis_surat = $conn->real_escape_string($_POST['jenis_surat']);
    $keterangan = $conn->real_escape_string($_POST['keterangan']);

    // Simpan ke database
    $query = "INSERT INTO surat (id_warga, nik, jenis_surat, keterangan) VALUES ($id_warga, '$nik', '$jenis_surat', '$keterangan')";
    
    if ($conn->query($query) === TRUE) {
        $pesan = "<div style='background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>Pengajuan surat berhasil dikirim!</div>";
        // Opsional: Langsung redirect ke riwayat
        // header("Location: riwayat.php"); exit;
    } else {
        $pesan = "<div style='background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>Error: " . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pengajuan Surat - NamaWeb</title>
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
          <a href="pengajuan.php?jenis=nikah" class="sidebar-link">
            <span class="sidebar-link-ikon">✉</span>Surat Pengantar Nikah
          </a>
          <a href="pengajuan.php?jenis=usaha" class="sidebar-link aktif">
            <span class="sidebar-link-ikon">✉</span>Surat Keterangan Usaha
          </a>
          <a href="pengajuan.php?jenis=domisili" class="sidebar-link">
            <span class="sidebar-link-ikon">✉</span>Surat Keterangan Domisili
          </a>
          <a href="pengajuan.php?jenis=lainnya" class="sidebar-link">
            <span class="sidebar-link-ikon">✉</span>Surat lorem ipsum
          </a>
        </div>
        <div class="sidebar-label">Informasi <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="riwayat.php" class="sidebar-link">
            <span class="sidebar-link-ikon">🕐</span>Riwayat Pengajuan
          </a>
          <a href="profil.php" class="sidebar-link">
            <span class="sidebar-link-ikon">👤</span>Profil Saya
          </a>
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
            <button type="submit" class="avatar-pengguna" title="Keluar dari akun" aria-label="Logout" onclick="return confirm('Yakin ingin keluar?');" style="cursor: pointer;">👤</button>
        </form>
      </header>

      <main class="area-konten">
        <div class="halaman-judul">
          <div>
            <h1>Permohonan Surat Keterangan Usaha</h1>
            <p>Isi formulir berikut dengan data yang benar dan lengkap</p>
          </div>
        </div>

        <?= $pesan ?>

        <form method="POST">
          
          <div class="kartu-form">
            <h3>Isi Data Diri</h3>
            <div class="grid-form-2">
              <div class="grup-form">
                <label>Nama Lengkap</label>
                <input type="text" class="input-form" value="<?= htmlspecialchars($nama_warga) ?>" disabled />
              </div>
              <div class="grup-form">
                <label>NIK</label>
                <input type="text" name="nik" class="input-form" placeholder="Masukkan 16 digit NIK" maxlength="16" pattern="[0-9]{16}" required />
              </div>
              <div class="grup-form" style="grid-column:1/-1;">
                <label>Jenis Surat</label>
                <select name="jenis_surat" class="input-form" required>
                    <option value="Surat Keterangan Usaha">Surat Keterangan Usaha</option>
                    <option value="Surat Pengantar Nikah">Surat Pengantar Nikah</option>
                    <option value="Surat Keterangan Domisili">Surat Keterangan Domisili</option>
                </select>
              </div>
              <div class="grup-form" style="grid-column:1/-1;">
                <label>Keperluan Pengajuan</label>
                <input type="text" name="keterangan" class="input-form" placeholder="Contoh: Mengurus izin usaha / Pindah domisili" required />
              </div>
            </div>
          </div>

          <div class="form-aksi">
            <button type="submit" class="btn-primer">Ajukan Surat</button>
            <button type="reset" class="btn-sekunder">Reset</button>
          </div>
        </form>

      </main>
    </div>
  </div>

  <script src="../js/main.js"></script>
</body>
</html>
<?php
require 'koneksi.php';

// Lindungi halaman: Pastikan yang login HANYA PETUGAS
if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'petugas') {
    header("Location: login.php");
    exit;
}

$nama_petugas = $_COOKIE['nama'];
$pesan = "";

// LOGIKA 1: Tambah Data Warga Baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_warga'])) {
    // [UPDATE PDM] Sesuaikan dengan kolom tabel Users di PDM
    $nama_lengkap = $conn->real_escape_string($_POST['nama_lengkap']);
    $nik = $conn->real_escape_string($_POST['nik']);
    $email = $conn->real_escape_string($_POST['email']);
    $no_hp = $conn->real_escape_string($_POST['no_hp']);
    $jenis_kelamin = $conn->real_escape_string($_POST['jenis_kelamin']);
    $password = $conn->real_escape_string($_POST['password']);
    
    // Cek apakah NIK sudah dipakai orang lain
    $cek = $conn->query("SELECT * FROM Users WHERE NIK='$nik'");
    if ($cek->num_rows > 0) {
        $pesan = "<div style='background: #fee2e2; color: #dc2626; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #fecaca;'><strong>Gagal!</strong> NIK tersebut sudah terdaftar di database.</div>";
    } else {
        $query_insert = "INSERT INTO Users (nama_lengkap, NIK, email, no_hp, jenis_kelamin, password, role) 
                         VALUES ('$nama_lengkap', '$nik', '$email', '$no_hp', '$jenis_kelamin', '$password', 'warga')";
        if ($conn->query($query_insert) === TRUE) {
            $pesan = "<div style='background: #dcfce7; color: #16a34a; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #bbf7d0;'><strong>Berhasil!</strong> Akun warga baru telah ditambahkan.</div>";
        } else {
            $pesan = "<div style='background: #fee2e2; color: #dc2626; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #fecaca;'><strong>Error:</strong> " . $conn->error . "</div>";
        }
    }
}

// LOGIKA 2: Hapus Data Warga
if (isset($_GET['hapus_id'])) {
    $id_hapus = (int)$_GET['hapus_id'];
    // [UPDATE PDM] Karena ada ON DELETE CASCADE di database, menghapus Warga akan otomatis menghapus semua suratnya! Sangat praktis.
    $conn->query("DELETE FROM Users WHERE id_user = $id_hapus");
    
    header("Location: petugas-warga.php");
    exit;
}

// [UPDATE PDM] Ambil SEMUA data akun yang rolenya 'warga'
$data_warga = $conn->query("SELECT * FROM Users WHERE role='warga' ORDER BY id_user DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kelola Data Warga - NamaWeb</title>
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
        <div class="sidebar-label">Dashboard <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="dashboard-petugas.php" class="sidebar-link"><span class="sidebar-link-ikon">🏠</span>Home</a>
        </div>
        <div class="sidebar-label">Layanan <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="petugas-masuk.php" class="sidebar-link"><span class="sidebar-link-ikon">📩</span>Surat Masuk</a>
          <a href="petugas-diproses.php" class="sidebar-link"><span class="sidebar-link-ikon">⏳</span>Sedang Diproses</a>
          <a href="petugas-upload.php" class="sidebar-link"><span class="sidebar-link-ikon">📤</span>Upload Surat (Selesai)</a>
          <a href="petugas-ditolak.php" class="sidebar-link"><span class="sidebar-link-ikon">❌</span>Surat Ditolak</a>
        </div>
        <div class="sidebar-label">Kelola Data <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="petugas-warga.php" class="sidebar-link aktif"><span class="sidebar-link-ikon">👥</span>Data Warga</a>
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
          <button id="tombolBukaSidebar" class="tombol-hamburger" style="display:flex;">
            <span></span><span></span><span></span>
          </button>
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
        <div style="margin-bottom:1.5rem;">
          <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;">Manajemen Data Warga</h1>
          <p style="color:var(--warna-teks-muda);margin-top:0.25rem;">Tambah, pantau, atau hapus akun warga desa</p>
        </div>

        <?= $pesan ?>

        <div class="kartu-form" style="margin-bottom:2rem;">
            <h3 style="font-size:1rem; font-weight:700; margin-bottom:1rem; border-bottom:1px solid var(--warna-border); padding-bottom:0.5rem;">Tambah Akun Warga Baru</h3>
            <form method="POST" action="">
                <input type="hidden" name="tambah_warga" value="1">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.25rem;">
                    <div class="grup-form">
                        <label class="label-form">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="input-form" placeholder="Contoh: Budi Santoso" required />
                    </div>
                    <div class="grup-form">
                        <label class="label-form">NIK (16 Digit)</label>
                        <input type="text" name="nik" class="input-form" placeholder="16 digit angka" required pattern="[0-9]{16}" title="Harus 16 digit angka" />
                    </div>
                    <div class="grup-form">
                        <label class="label-form">Email</label>
                        <input type="email" name="email" class="input-form" placeholder="email@contoh.com" required />
                    </div>
                    <div class="grup-form">
                        <label class="label-form">Nomor Handphone</label>
                        <input type="text" name="no_hp" class="input-form" placeholder="08xx..." required />
                    </div>
                    <div class="grup-form">
                        <label class="label-form">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="input-form" required>
                            <option value="laki-laki">Laki-laki</option>
                            <option value="perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="grup-form">
                        <label class="label-form">Password Login</label>
                        <input type="text" name="password" class="input-form" placeholder="Minimal 6 karakter" required minlength="6" />
                    </div>
                </div>
                <button type="submit" class="btn-primer">➕ Daftarkan Warga</button>
            </form>
        </div>

        <div class="kartu-tabel">
          <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--warna-border);">
            <h3 style="font-size:1rem; font-weight:700;">Daftar Akun Terdaftar</h3>
          </div>
          <table class="tabel-data">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>NIK</th>
                <th>No. HP</th>
                <th>Gender</th>
                <th style="text-align:center;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($data_warga->num_rows > 0): ?>
                  <?php $no = 1; while($row = $data_warga->fetch_assoc()): ?>
                    <tr>
                      <td><?= $no++ ?></td>
                      <td><strong><?= htmlspecialchars($row['nama_lengkap']) ?></strong></td>
                      <td><?= htmlspecialchars($row['NIK']) ?></td>
                      <td><?= htmlspecialchars($row['no_hp']) ?></td>
                      <td style="text-transform: capitalize;"><?= htmlspecialchars($row['jenis_kelamin']) ?></td>
                      <td style="text-align:center;">
                          <a href="?hapus_id=<?= $row['id_user'] ?>" class="btn-sekunder btn-kecil" style="color:#ef4444; border-color:#ef4444; text-decoration:none;" onclick="return confirm('PERINGATAN! Menghapus akun ini juga akan menghapus SEMUA riwayat surat miliknya. Yakin lanjutkan?');">🗑️ Hapus</a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
              <?php else: ?>
                  <tr>
                    <td colspan="6" style="text-align:center; padding: 20px;">Belum ada data warga terdaftar.</td>
                  </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </main>
    </div>
  </div>

  <script src="../js/main.js"></script>
</body>
</html>
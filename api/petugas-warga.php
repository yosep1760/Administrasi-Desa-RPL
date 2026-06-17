<?php
require 'koneksi.php';

// Lindungi halaman: Pastikan yang login HANYA PETUGAS
if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'petugas') {
    header("Location: login.php");
    exit;
}

$nama_petugas = $_COOKIE['nama'];
$pesan = "";

// LOGIKA: Hapus Data Warga
if (isset($_GET['hapus_id'])) {
    $id_hapus = (int)$_GET['hapus_id'];
    // Karena ada ON DELETE CASCADE di database, menghapus Warga akan otomatis menghapus semua suratnya!
    $conn->query("DELETE FROM Users WHERE id_user = $id_hapus");
    
    header("Location: petugas-warga.php?pesan=hapus_sukses");
    exit;
}

if (isset($_GET['pesan']) && $_GET['pesan'] == 'hapus_sukses') {
    $pesan = "<div style='background: #dcfce7; color: #16a34a; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #bbf7d0;'><strong>Berhasil!</strong> Data warga telah dihapus dari sistem.</div>";
}
if (isset($_GET['pesan']) && $_GET['pesan'] == 'tambah_sukses') {
    $pesan = "<div style='background: #dcfce7; color: #16a34a; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #bbf7d0;'><strong>Berhasil!</strong> Akun warga baru berhasil ditambahkan.</div>";
}

// Ambil SEMUA data akun yang rolenya 'warga'
$data_warga = $conn->query("SELECT * FROM Users WHERE role='warga' ORDER BY id_user DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Data Warga - SiKosar</title>
  <link rel="stylesheet" href="../css/style.css" />
  <style>
      .tabel-warga { width: 100%; border-collapse: collapse; margin-top: 1rem; }
      .tabel-warga th, .tabel-warga td { padding: 12px 15px; border-bottom: 1px solid #e2e8f0; text-align: left; }
      .tabel-warga th { background-color: #f8fafc; font-weight: 600; color: #475569; }
      .tabel-warga tr:hover { background-color: #f1f5f9; }
  </style>
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
            <h3>Halo, <?= htmlspecialchars($nama_petugas) ?></h3>
            <span>Petugas Layanan</span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;">
            <button type="submit" class="avatar-pengguna" title="Keluar">👤</button>
        </form>
      </header>

      <main class="area-konten">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <div>
              <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;">Kelola Data Warga</h1>
              <p style="color:var(--warna-teks-muda);margin-top:0.25rem;">Daftar akun warga yang terdaftar di sistem SiKosar</p>
            </div>
            
            <a href="petugas-warga-tambah.php" class="btn-primer"><span>➕</span> Tambah Warga Baru</a>
        </div>

        <?= $pesan ?>

        <div class="kartu-form" style="overflow-x:auto;">
          <table class="tabel-warga" id="tabelDataWarga">
            <thead>
              <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama Lengkap</th>
                <th>No. HP</th>
                <th>Gender</th>
                <th style="text-align:center;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if($data_warga->num_rows > 0): $no=1; while($w = $data_warga->fetch_assoc()): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><strong><?= htmlspecialchars($w['NIK']) ?></strong></td>
                <td><?= htmlspecialchars($w['nama_lengkap']) ?></td>
                <td><?= htmlspecialchars($w['no_hp']) ?></td>
                <td><?= htmlspecialchars($w['jenis_kelamin']) ?></td>
                <td style="text-align:center;">
                    <a href="?hapus_id=<?= $w['id_user'] ?>" onclick="return confirm('Yakin ingin menghapus warga ini? SEMUA SURAT terkait juga akan hilang permanen!');" class="btn-sekunder btn-kecil" style="color:#dc2626; border-color:#fca5a5; background:#fef2f2;">🗑️ Hapus</a>
                </td>
              </tr>
              <?php endwhile; else: ?>
              <tr>
                  <td colspan="6" style="text-align:center; padding: 20px; color:#64748b;">Belum ada data warga terdaftar.</td>
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
<?php
require 'koneksi.php';

// Lindungi halaman dengan COOKIE
if (!isset($_COOKIE['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = (int)$_COOKIE['user_id'];

// Ambil data user secara lengkap dari tabel Users
$query = $conn->query("SELECT * FROM Users WHERE id_user = $id_user");
$user = $query->fetch_assoc();

// Ambil huruf pertama dari nama untuk ikon avatar
$huruf_awal = strtoupper(substr($user['nama_lengkap'], 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Profil Saya - SiKosar</title>
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
            <h3>Halo, <?= htmlspecialchars($user['nama_lengkap']) ?></h3>
            <span>Profil Pengguna</span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;">
            <button type="submit" class="avatar-pengguna" title="Keluar" onclick="return confirm('Yakin ingin keluar?');" style="cursor:pointer;"><?= $huruf_awal ?></button>
        </form>
      </header>

      <main class="area-konten">
        <div style="margin-bottom:1.5rem;">
          <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;">Informasi Akun Anda</h1>
          <p style="color:var(--warna-teks-muda);margin-top:0.25rem;">Data profil sesuai yang terdaftar pada sistem layanan SiKosar</p>
        </div>

        <div style="display:grid; grid-template-columns:1fr; gap:1.5rem;">
          
          <div class="kartu-form" style="display:flex; flex-direction:column; align-items:center; text-align:center;">
             <div style="width:100px; height:100px; border-radius:50%; background:var(--warna-primer); color:white; font-size:3rem; font-weight:bold; display:flex; justify-content:center; align-items:center; margin-bottom:1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                <?= $huruf_awal ?>
             </div>
             <h2 style="font-size:1.5rem; margin-bottom:0.25rem;"><?= htmlspecialchars($user['nama_lengkap']) ?></h2>
             <span class="badge badge-disetujui" style="margin-bottom:1rem; padding:6px 12px; font-size:0.9rem;">
                 <?php 
                    if($user['role'] == 'warga') echo 'Warga Desa';
                    elseif($user['role'] == 'petugas') echo 'Petugas Pelayanan';
                    elseif($user['role'] == 'kepala_desa') echo 'Kepala Desa';
                 ?>
             </span>
             
             <div style="width:100%; text-align:left; border-top:1px solid var(--warna-border); padding-top:1.5rem; margin-top:1rem;">
                 <div class="grup-form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.25rem;">
                    <div class="grup-form">
                        <label class="label-form">Nomor Induk Kependudukan (NIK)</label>
                        <input type="text" class="input-form" value="<?= htmlspecialchars($user['NIK']) ?>" readonly style="background:#f3f4f6; cursor:not-allowed;" />
                    </div>
                    <div class="grup-form">
                        <label class="label-form">Alamat Email</label>
                        <input type="text" class="input-form" value="<?= htmlspecialchars($user['email']) ?>" readonly style="background:#f3f4f6; cursor:not-allowed;" />
                    </div>
                 </div>

                 <div class="grup-form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.25rem;">
                    <div class="grup-form">
                        <label class="label-form">Nomor Handphone</label>
                        <input type="text" class="input-form" value="<?= htmlspecialchars($user['no_hp']) ?>" readonly style="background:#f3f4f6; cursor:not-allowed;" />
                    </div>
                    <div class="grup-form">
                        <label class="label-form">Jenis Kelamin</label>
                        <input type="text" class="input-form" value="<?= htmlspecialchars($user['jenis_kelamin']) ?>" readonly style="background:#f3f4f6; cursor:not-allowed;" />
                    </div>
                 </div>

                 <div class="grup-form" style="margin-bottom:1.25rem;">
                    <label class="label-form">Alamat Lengkap</label>
                    <textarea class="input-form" rows="3" readonly style="background:#f3f4f6; cursor:not-allowed;"><?= htmlspecialchars($user['alamat']) ?></textarea>
                 </div>
             </div>
             
             <div style="background:#fefce8; color:#a16207; padding:12px; border-radius:6px; border:1px solid #fef08a; width:100%; text-align:left; font-size:0.9rem;">
                 <strong>Catatan:</strong> Untuk melakukan perubahan data (misal pindah alamat / ganti nomor HP), silakan hubungi langsung Petugas di Kantor Desa.
             </div>
          </div>

        </div>

      </main>
    </div>
  </div>

  <script src="../js/main.js"></script>
</body>
</html>
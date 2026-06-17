<?php
require 'koneksi.php';

if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'petugas') {
    header("Location: login.php");
    exit;
}

$nama_petugas = $_COOKIE['nama'];
$pesan = "";

// LOGIKA: Tambah Data Warga Baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_warga'])) {
    $nama_lengkap = $conn->real_escape_string($_POST['nama_lengkap']);
    $nik = $conn->real_escape_string($_POST['nik']);
    $email = $conn->real_escape_string($_POST['email']);
    $no_hp = $conn->real_escape_string($_POST['no_hp']);
    $jenis_kelamin = $conn->real_escape_string($_POST['jenis_kelamin']);
    $password = $conn->real_escape_string($_POST['password']);
    
    $cek = $conn->query("SELECT * FROM Users WHERE NIK='$nik'");
    if ($cek->num_rows > 0) {
        $pesan = "<div style='background: #fee2e2; color: #dc2626; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #fecaca;'><strong>Gagal!</strong> NIK tersebut sudah terdaftar di database.</div>";
    } else {
        $query_insert = "INSERT INTO Users (nama_lengkap, NIK, email, no_hp, jenis_kelamin, password, role) 
                         VALUES ('$nama_lengkap', '$nik', '$email', '$no_hp', '$jenis_kelamin', '$password', 'warga')";
        if ($conn->query($query_insert) === TRUE) {
            // Redirect kembali ke halaman tabel dengan pesan sukses
            header("Location: petugas-warga.php?pesan=tambah_sukses");
            exit;
        } else {
            $pesan = "<div style='background: #fee2e2; color: #dc2626; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #fecaca;'><strong>Error:</strong> " . $conn->error . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tambah Warga Baru - SiKosar</title>
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
              <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;">Tambah Akun Warga Baru</h1>
              <p style="color:var(--warna-teks-muda);margin-top:0.25rem;">Isi formulir di bawah ini untuk mendaftarkan warga.</p>
            </div>
            
            <a href="petugas-warga.php" class="btn-sekunder">Kembali ke Data Warga</a>
        </div>

        <?= $pesan ?>

        <div class="kartu-form">
            <form method="POST" action="">
                <input type="hidden" name="tambah_warga" value="1">
                <div class="grup-form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.25rem;">
                    <div class="grup-form">
                        <label class="label-form">Nomor Induk Kependudukan (NIK)</label>
                        <input type="text" name="nik" class="input-form" placeholder="16 Digit NIK KTP..." required minlength="16" maxlength="16" />
                    </div>
                    <div class="grup-form">
                        <label class="label-form">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="input-form" placeholder="Sesuai KTP..." required />
                    </div>
                </div>

                <div class="grup-form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.25rem;">
                    <div class="grup-form">
                        <label class="label-form">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="input-form" required>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="grup-form">
                        <label class="label-form">Nomor HP / WhatsApp</label>
                        <input type="text" name="no_hp" class="input-form" placeholder="Contoh: 0812345..." required />
                    </div>
                </div>

                <div class="grup-form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:2rem;">
                    <div class="grup-form">
                        <label class="label-form">Email (Opsional - Untuk Notif)</label>
                        <input type="email" name="email" class="input-form" placeholder="warga@domain.com" />
                    </div>
                    <div class="grup-form">
                        <label class="label-form">Password Akun</label>
                        <input type="password" name="password" class="input-form" placeholder="Minimal 6 Karakter..." required minlength="6" />
                    </div>
                </div>

                <div style="display:flex; gap:1rem;">
                    <button type="submit" class="btn-primer">Simpan Data Warga</button>
                    <button type="reset" class="btn-sekunder">Reset Form</button>
                </div>
            </form>
        </div>

      </main>
    </div>
  </div>

  <script src="../js/main.js"></script>
</body>
</html>
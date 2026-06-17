<?php
require 'koneksi.php';

// Lindungi halaman dengan COOKIE
if (!isset($_COOKIE['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = (int)$_COOKIE['user_id'];

// [UPDATE PDM] Ambil data user secara lengkap dari tabel Users
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
  <title>Profil Saya - NamaWeb</title>
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
            <span style="text-transform: capitalize;"><?= str_replace('_', ' ', htmlspecialchars($user['role'])) ?></span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;">
            <button type="submit" class="avatar-pengguna" title="Keluar dari akun" aria-label="Logout" onclick="return confirm('Yakin ingin keluar?');" style="cursor: pointer;">👤</button>
        </form>
      </header>

      <main class="area-konten">
        <div class="kartu-profil">
          <div class="profil-header">
            <h2>Profil Data Diri</h2>
            <button class="btn-primer btn-kecil" id="tombolEditProfil" onclick="toggleEditProfil()">
              ✏ Edit Profil
            </button>
          </div>

          <div class="profil-avatar">
            <div class="avatar-lingkaran" id="avatarLingkaran"><?= $huruf_awal ?></div>
            <div>
              <div class="profil-nama"><?= htmlspecialchars($user['nama_lengkap']) ?></div>
              <div class="profil-peran" style="text-transform: capitalize;">Role: <?= str_replace('_', ' ', htmlspecialchars($user['role'])) ?></div>
            </div>
          </div>

          <h3 style="font-size:0.95rem;font-weight:700;margin-bottom:1rem;color:var(--warna-teks);">
            Informasi Akun
          </h3>

          <table class="tabel-profil" id="tabelProfil">
            <tbody>
              <tr>
                <td>NIK Kependudukan:</td>
                <td><strong><?= htmlspecialchars($user['NIK']) ?></strong></td>
              </tr>
              <tr>
                <td>Email Akun:</td>
                <td><?= htmlspecialchars($user['email']) ?></td>
              </tr>
              <tr>
                <td>Nomor Handphone:</td>
                <td><?= htmlspecialchars($user['no_hp']) ?></td>
              </tr>
              <tr>
                <td>Jenis Kelamin:</td>
                <td style="text-transform: capitalize;"><?= htmlspecialchars($user['jenis_kelamin']) ?></td>
              </tr>
              <tr>
                <td>Alamat Lengkap:</td>
                <td style="color: <?= $user['alamat'] ? 'inherit' : 'var(--warna-teks-muda)' ?>;">
                    <?= $user['alamat'] ? htmlspecialchars($user['alamat']) : "Belum diatur. Silakan lengkapi profil Anda." ?>
                </td>
              </tr>
              <tr>
                <td>Password:</td>
                <td>
                  <span id="tampilPassword">••••••••••••</span>
                  <button class="btn-primer btn-kecil" style="margin-left:0.5rem;" onclick="togglePassword()" id="tombolLihatPassword">Lihat</button>
                </td>
              </tr>
            </tbody>
          </table>

        </div>
      </main>
    </div>
  </div>

  <script src="../js/main.js"></script>
  <script>
    var passwordAsli = '<?= addslashes($user['password']) ?>';
    var passwordTersembunyi = true;

    function togglePassword() {
      const elPassword = document.getElementById('tampilPassword');
      const tombol = document.getElementById('tombolLihatPassword');

      if (passwordTersembunyi) {
        elPassword.textContent = passwordAsli;
        tombol.textContent = 'Sembunyikan';
        passwordTersembunyi = false;
      } else {
        elPassword.textContent = '••••••••••••';
        tombol.textContent = 'Lihat';
        passwordTersembunyi = true;
      }
    }

    function toggleEditProfil() {
      alert('Fitur edit profil (menambahkan Alamat dan Tempat Lahir) silakan kembangkan di tahap selanjutnya!');
    }
  </script>
</body>
</html>
<?php
require 'koneksi.php';

// Lindungi halaman dengan COOKIE
if (!isset($_COOKIE['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_COOKIE['user_id'];

// Ambil data user secara lengkap dari database
$query = $conn->query("SELECT * FROM pengguna WHERE id = $id_user");
$user = $query->fetch_assoc();

// Ambil huruf pertama dari nama untuk ikon avatar
$huruf_awal = strtoupper(substr($user['nama'], 0, 1));
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
    <div id="overlaySidebar" class="overlay-sidebar"></div>

    <aside id="sidebar" class="sidebar">
      <div class="sidebar-header">*Logo + NamaWeb</div>
      <div class="sidebar-cari">
        <input type="search" class="input-cari" placeholder="Search" />
      </div>
      
      <nav class="sidebar-nav">
        
        <?php if ($user['role'] == 'warga'): ?>
            <a href="dashboard.php" class="sidebar-link">
              <span class="sidebar-link-ikon">📊</span>Dashboard
            </a>
            <div class="sidebar-label">Ajukan Surat <span class="sidebar-label-ikon">∧</span></div>
            <div class="sidebar-sub">
              <a href="pengajuan.php?jenis=nikah" class="sidebar-link"><span class="sidebar-link-ikon">✉</span>Surat Pengantar Nikah</a>
              <a href="pengajuan.php?jenis=usaha" class="sidebar-link"><span class="sidebar-link-ikon">✉</span>Surat Keterangan Usaha</a>
              <a href="pengajuan.php?jenis=domisili" class="sidebar-link"><span class="sidebar-link-ikon">✉</span>Surat Keterangan Domisili</a>
              <a href="pengajuan.php?jenis=lainnya" class="sidebar-link"><span class="sidebar-link-ikon">✉</span>Surat lorem ipsum</a>
            </div>
            <div class="sidebar-label">Informasi <span class="sidebar-label-ikon">∧</span></div>
            <div class="sidebar-sub">
              <a href="riwayat.php" class="sidebar-link"><span class="sidebar-link-ikon">🕐</span>Riwayat Pengajuan</a>
              <a href="profil.php" class="sidebar-link aktif"><span class="sidebar-link-ikon">👤</span>Profil Saya</a>
            </div>

        <?php elseif ($user['role'] == 'petugas'): ?>
            <a href="dashboard-petugas.php" class="sidebar-link">
              <span class="sidebar-link-ikon">🏠</span>Dashboard Petugas
            </a>
            <div class="sidebar-label">Pengaturan <span class="sidebar-label-ikon">∧</span></div>
            <div class="sidebar-sub">
              <a href="profil.php" class="sidebar-link aktif"><span class="sidebar-link-ikon">👤</span>Profil Saya</a>
            </div>

        <?php elseif ($user['role'] == 'kades'): ?>
            <div class="sidebar-label">Dashboard <span class="sidebar-label-ikon">∧</span></div>
            <div class="sidebar-sub">
              <a href="dashboard-kades.php" class="sidebar-link"><span class="sidebar-link-ikon">🏠</span>Home</a>
            </div>
            <div class="sidebar-label">Layanan <span class="sidebar-label-ikon">∧</span></div>
            <div class="sidebar-sub">
              <a href="#" class="sidebar-link"><span class="sidebar-link-ikon">📩</span>Request Surat</a>
              <a href="#" class="sidebar-link"><span class="sidebar-link-ikon">✅</span>Surat Disetujui</a>
              <a href="#" class="sidebar-link"><span class="sidebar-link-ikon">❌</span>Surat Ditolak</a>
            </div>
            <div class="sidebar-label">Pengaturan <span class="sidebar-label-ikon">∧</span></div>
            <div class="sidebar-sub">
              <a href="profil.php" class="sidebar-link aktif"><span class="sidebar-link-ikon">👤</span>Profil Saya</a>
            </div>
        <?php endif; ?>

      </nav>
    </aside>

    <div class="konten-dashboard">
      <header class="header-dashboard">
        <div style="display:flex;align-items:center;gap:0.75rem;">
          <button id="tombolBukaSidebar" class="tombol-hamburger" style="display:flex;">
            <span></span><span></span><span></span>
          </button>
          <div class="header-pengguna">
            <h3>Halo, <?= htmlspecialchars($user['nama']) ?></h3>
            <span style="text-transform: capitalize;"><?= htmlspecialchars($user['role']) ?></span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;">
            <button type="submit" class="avatar-pengguna" title="Keluar dari akun" aria-label="Logout" onclick="return confirm('Yakin ingin keluar?');" style="cursor: pointer;">👤</button>
        </form>
      </header>

      <main class="area-konten">
        <div class="kartu-profil">
          <div class="profil-header">
            <h2>Profil Saya</h2>
            <button class="btn-primer btn-kecil" id="tombolEditProfil" onclick="toggleEditProfil()">
              ✏ Edit Profil
            </button>
          </div>

          <div class="profil-avatar">
            <div class="avatar-lingkaran" id="avatarLingkaran"><?= $huruf_awal ?></div>
            <div>
              <div class="profil-nama"><?= htmlspecialchars($user['nama']) ?></div>
              <div class="profil-peran" style="text-transform: capitalize;">Role: <?= htmlspecialchars($user['role']) ?></div>
            </div>
          </div>

          <h3 style="font-size:0.95rem;font-weight:700;margin-bottom:1rem;color:var(--warna-teks);">
            Informasi Akun
          </h3>

          <table class="tabel-profil" id="tabelProfil">
            <tbody>
              <tr>
                <td>Username:</td>
                <td><strong><?= htmlspecialchars($user['username']) ?></strong></td>
              </tr>
              <tr>
                <td>Nomor Telepon:</td>
                <td style="color:var(--warna-teks-muda);">Belum diatur (Contoh: 0812-3456-7890)</td>
              </tr>
              <tr>
                <td>Alamat:</td>
                <td style="color:var(--warna-teks-muda);">Belum diatur</td>
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
      alert('Fitur edit profil belum terhubung ke database. Silakan kembangkan di tahap selanjutnya!');
    }
  </script>
</body>
</html>
<?php
// Halaman Publik
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tentang Kami - Desa Kosar</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>

  <header id="navbar" class="navbar">
    <a href="index.php" class="navbar-logo">🏛️ Desa Kosar</a>
    <ul id="menuNavbar" class="navbar-menu">
      <li><a href="index.php">Home</a></li>
      <li><a href="tentang.php" style="color:var(--warna-teks);">Tentang</a></li>
      <li><a href="index.php#surat">Surat</a></li>
      <li><a href="index.php#alur">Alur</a></li>
      <li><a href="index.php#faq">FaQ</a></li>
      <li><a href="kontak.php">Kontak</a></li>
    </ul>
    <div class="navbar-aksi">
      <?php if(isset($_COOKIE['user_id'])): ?>
          <?php 
             $link_dashboard = 'dashboard.php';
             if($_COOKIE['role'] == 'kepala_desa') $link_dashboard = 'dashboard-kades.php';
             elseif($_COOKIE['role'] == 'petugas') $link_dashboard = 'dashboard-petugas.php';
          ?>
          <a href="<?= $link_dashboard ?>" class="btn-primer">Dashboard Saya</a>
      <?php else: ?>
          <a href="login.php" class="btn-primer">Login</a>
      <?php endif; ?>
    </div>
  </header>

  <section class="hero-tentang padding-halaman">
    <h1>Tentang Aplikasi Desa Kosar</h1>
    <p>Platform digital administrasi yang hadir untuk memudahkan masyarakat dalam mengakses layanan desa secara online, cepat, dan transparan.</p>
  </section>

  <section style="padding:5rem 2rem;background:var(--warna-bg);">
    <div class="kontainer">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:3rem;margin-bottom:4rem;">
        <div style="background:var(--warna-bg-kartu);border:1px solid var(--warna-border);border-radius:var(--radius-sedang);padding:2rem;">
          <div style="font-size:2.5rem;margin-bottom:1rem;">🎯</div>
          <h2 style="font-family:var(--font-judul);font-size:1.4rem;font-weight:700;margin-bottom:1rem;">Visi Kami</h2>
          <p style="color:var(--warna-teks-muda);line-height:1.75;font-size:0.925rem;">
            Mewujudkan Desa Kosar sebagai desa digital yang modern, transparan, dan memberikan pelayanan publik terbaik kepada seluruh warganya.
          </p>
        </div>

        <div style="background:#1e293b;border-radius:var(--radius-sedang);padding:2rem;">
          <div style="font-size:2.5rem;margin-bottom:1rem;">🚀</div>
          <h2 style="font-family:var(--font-judul);font-size:1.4rem;font-weight:700;margin-bottom:1rem;color:white;">Misi Kami</h2>
          <ul style="color:rgba(255,255,255,0.75);line-height:1.75;font-size:0.925rem;list-style:none;display:flex;flex-direction:column;gap:0.5rem;">
            <li>✓ Memangkas antrean di Balai Desa Kosar</li>
            <li>✓ Mempermudah proses pengajuan surat dari rumah</li>
            <li>✓ Memberikan tracking dokumen secara real-time</li>
            <li>✓ Membantu pengarsipan data penduduk yang lebih rapi</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <script src="js/main.js"></script>
</body>
</html>
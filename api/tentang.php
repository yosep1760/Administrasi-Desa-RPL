<?php
// Gunakan pengecekan cookie
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tentang Kami - NamaWeb</title>
  <link rel="stylesheet" href="../css/style.css" />
</head>
<body>

  <header id="navbar" class="navbar">
    <a href="index.php" class="navbar-logo">NamaWeb</a>
    <ul id="menuNavbar" class="navbar-menu">
      <li><a href="index.php">Home</a></li>
      <li><a href="tentang.php">Tentang</a></li>
      <li><a href="index.php#surat">Surat</a></li>
      <li><a href="index.php#alur">Alur</a></li>
      <li><a href="index.php#faq">FaQ</a></li>
      <li><a href="kontak.php">Kontak</a></li>
    </ul>
    
    <div class="navbar-aksi">
      <?php if(isset($_COOKIE['user_id'])): ?>
          <?php 
             $link_dashboard = 'dashboard.php';
             if(isset($_COOKIE['role'])) {
                 if($_COOKIE['role'] == 'kepala_desa') $link_dashboard = 'dashboard-kades.php';
                 elseif($_COOKIE['role'] == 'petugas') $link_dashboard = 'dashboard-petugas.php';
             }
          ?>
          <a href="<?= $link_dashboard ?>" class="btn-primer">Dashboard Saya</a>
      <?php else: ?>
          <a href="login.php" class="btn-primer">Login</a>
      <?php endif; ?>
      <button id="tombolHamburger" class="tombol-hamburger" aria-label="Buka menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </header>

  <section class="hero-tentang padding-halaman">
    <h1>Tentang NamaWeb</h1>
    <p>
      Platform digital yang hadir untuk memudahkan masyarakat dalam mengakses
      layanan administrasi desa secara online, cepat, dan transparan.
    </p>
  </section>

  <section style="padding:5rem 2rem;background:var(--warna-bg);">
    <div class="kontainer">

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:3rem;margin-bottom:4rem;">

        <div style="background:var(--warna-bg-kartu);border:1px solid var(--warna-border);border-radius:var(--radius-sedang);padding:2rem;">
          <div style="font-size:2.5rem;margin-bottom:1rem;">🎯</div>
          <h2 style="font-family:var(--font-judul);font-size:1.4rem;font-weight:700;margin-bottom:1rem;">
            Visi Kami
          </h2>
          <p style="color:var(--warna-teks-muda);line-height:1.75;font-size:0.925rem;">
            Mewujudkan desa digital yang modern, transparan, dan memberikan pelayanan terbaik
            kepada seluruh warga melalui teknologi informasi yang mudah diakses oleh semua kalangan.
          </p>
        </div>

        <div style="background:var(--warna-primer);border-radius:var(--radius-sedang);padding:2rem;">
          <div style="font-size:2.5rem;margin-bottom:1rem;">🚀</div>
          <h2 style="font-family:var(--font-judul);font-size:1.4rem;font-weight:700;margin-bottom:1rem;color:white;">
            Misi Kami
          </h2>
          <ul style="color:rgba(255,255,255,0.75);line-height:1.75;font-size:0.925rem;list-style:none;display:flex;flex-direction:column;gap:0.5rem;">
            <li>✓ Mempermudah proses pengajuan surat secara digital</li>
            <li>✓ Meningkatkan transparansi dan akuntabilitas pelayanan</li>
            <li>✓ Mengurangi birokrasi yang memakan waktu lama</li>
            <li>✓ Memberikan notifikasi status secara real-time</li>
          </ul>
        </div>

      </div>

      <div class="judul-seksi">
        <h2>Tim Pengembang</h2>
        <p>Dikembangkan dengan penuh semangat untuk kemajuan layanan administrasi desa</p>
      </div>

      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;">

        <div style="background:var(--warna-bg-kartu);border:1px solid var(--warna-border);border-radius:var(--radius-sedang);padding:1.5rem;text-align:center;">
          <div style="width:70px;height:70px;background:var(--warna-primer);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:1.8rem;margin:0 auto 1rem;">👤</div>
          <h3 style="font-weight:700;font-size:1rem;margin-bottom:0.25rem;">Anggota Satu</h3>
          <p style="color:var(--warna-teks-muda);font-size:0.825rem;">Frontend Developer</p>
        </div>

        <div style="background:var(--warna-bg-kartu);border:1px solid var(--warna-border);border-radius:var(--radius-sedang);padding:1.5rem;text-align:center;">
          <div style="width:70px;height:70px;background:var(--warna-aksen);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--warna-primer);font-size:1.8rem;margin:0 auto 1rem;">👤</div>
          <h3 style="font-weight:700;font-size:1rem;margin-bottom:0.25rem;">Anggota Dua</h3>
          <p style="color:var(--warna-teks-muda);font-size:0.825rem;">Backend Developer</p>
        </div>

        <div style="background:var(--warna-bg-kartu);border:1px solid var(--warna-border);border-radius:var(--radius-sedang);padding:1.5rem;text-align:center;">
          <div style="width:70px;height:70px;background:var(--warna-primer);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:1.8rem;margin:0 auto 1rem;">👤</div>
          <h3 style="font-weight:700;font-size:1rem;margin-bottom:0.25rem;">Anggota Tiga</h3>
          <p style="color:var(--warna-teks-muda);font-size:0.825rem;">UI/UX Designer</p>
        </div>

      </div>

    </div>
  </section>

  <footer>
    <div class="footer-grid">
      <div>
        <div class="footer-logo">NamaWeb</div>
        <p class="footer-deskripsi">Mendukung pelayanan administrasi desa yang lebih praktis, transparan, dan mudah diakses oleh masyarakat.</p>
        <p class="footer-copyright">© 2026 NamaWeb. Seluruh hak cipta dilindungi.</p>
      </div>
      <div>
        <div class="footer-judul-kolom">Navigasi</div>
        <ul class="footer-link">
          <li><a href="index.php">Beranda</a></li>
          <li><a href="tentang.php">Tentang Kami</a></li>
          <li><a href="index.php#surat">Jenis Surat</a></li>
          <li><a href="index.php#alur">Alur Pengajuan</a></li>
          <li><a href="index.php#faq">FaQ</a></li>
          <li><a href="kontak.php">Hubungi Kami</a></li>
        </ul>
      </div>
      <div>
        <div class="footer-judul-kolom">Kontak Kami</div>
        <ul class="footer-kontak">
          <li><span>📞</span> 08xx-xxxx-xxxx</li>
          <li><span>✉️</span> admin@namadesa.id</li>
          <li><span>📍</span> Kantor Desa [Nama Desa]</li>
        </ul>
      </div>
    </div>
    <hr class="footer-garis" style="max-width:1100px;margin:0 auto 1rem;" />
    <div class="footer-bawah">
      <p>NamaWeb © 2026</p>
      <button class="btn-atas" onclick="scrollKeAtas()">↑</button>
    </div>
  </footer>

  <script src="../js/main.js"></script>
</body>
</html>
<?php
// Halaman Publik
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kontak Kami - Desa Kosar</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>

  <header id="navbar" class="navbar">
    <a href="index.php" class="navbar-logo">🏛️ Desa Kosar</a>
    <ul id="menuNavbar" class="navbar-menu">
      <li><a href="index.php">Home</a></li>
      <li><a href="tentang.php">Tentang</a></li>
      <li><a href="index.php#surat">Surat</a></li>
      <li><a href="index.php#alur">Alur</a></li>
      <li><a href="index.php#faq">FaQ</a></li>
      <li><a href="kontak.php" style="color:var(--warna-teks);">Kontak</a></li>
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
    <h1>Hubungi Desa Kosar</h1>
    <p>Kami siap melayani Anda. Silakan hubungi kami melalui berbagai saluran yang tersedia di bawah ini.</p>
  </section>

  <section style="padding:5rem 2rem;background:var(--warna-bg);">
    <div class="kontainer">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:3rem;align-items:start;">
        <div>
          <h2 style="font-family:var(--font-judul);font-size:1.8rem;font-weight:700;margin-bottom:1.5rem;">Informasi Kontak</h2>

          <div style="background:var(--warna-bg-kartu);border:1px solid var(--warna-border);border-radius:var(--radius-sedang);padding:1.25rem;margin-bottom:1rem;display:flex;gap:1rem;align-items:flex-start;">
            <div style="font-size:1.5rem;">💬</div>
            <div>
              <div style="font-weight:700;margin-bottom:0.25rem;font-size:0.9rem;">WhatsApp Balai Desa</div>
              <p style="color:var(--warna-teks-muda);font-size:0.85rem;">0812-3456-7890</p>
              <p style="color:var(--warna-teks-muda);font-size:0.8rem;">Senin - Jumat, 08.00 - 16.00 WIB</p>
            </div>
          </div>

          <div style="background:var(--warna-bg-kartu);border:1px solid var(--warna-border);border-radius:var(--radius-sedang);padding:1.25rem;margin-bottom:1rem;display:flex;gap:1rem;align-items:flex-start;">
            <div style="font-size:1.5rem;">✉️</div>
            <div>
              <div style="font-weight:700;margin-bottom:0.25rem;font-size:0.9rem;">Email Resmi</div>
              <p style="color:var(--warna-teks-muda);font-size:0.85rem;">sistem@desakosar.dpdns.org</p>
              <p style="color:var(--warna-teks-muda);font-size:0.8rem;">Respon maksimal 1x24 jam kerja</p>
            </div>
          </div>

          <div style="background:var(--warna-bg-kartu);border:1px solid var(--warna-border);border-radius:var(--radius-sedang);padding:1.25rem;display:flex;gap:1rem;align-items:flex-start;">
            <div style="font-size:1.5rem;">📍</div>
            <div>
              <div style="font-weight:700;margin-bottom:0.25rem;font-size:0.9rem;">Kantor Desa Kosar</div>
              <p style="color:var(--warna-teks-muda);font-size:0.85rem;">Jl. Raya Balai Desa No. 1, Kecamatan Makmur Jaya</p>
              <p style="color:var(--warna-teks-muda);font-size:0.8rem;">Kode Pos: 12345</p>
            </div>
          </div>
        </div>

        <div style="background:var(--warna-bg-kartu);border:1px solid var(--warna-border);border-radius:var(--radius-sedang);padding:2rem;">
          <h2 style="font-family:var(--font-judul);font-size:1.4rem;font-weight:700;margin-bottom:1.5rem;">Kirim Pesan Cepat</h2>
          <form onsubmit="kirimPesan(event)">
            <div class="grup-form" style="margin-bottom:1rem;">
              <label style="display:block;font-size:0.825rem;font-weight:600;margin-bottom:0.35rem;">Nama Lengkap</label>
              <input type="text" class="input-form" required />
            </div>
            <div class="grup-form" style="margin-bottom:1rem;">
              <label style="display:block;font-size:0.825rem;font-weight:600;margin-bottom:0.35rem;">Email</label>
              <input type="email" class="input-form" required />
            </div>
            <div class="grup-form" style="margin-bottom:1.25rem;">
              <label style="display:block;font-size:0.825rem;font-weight:600;margin-bottom:0.35rem;">Pesan / Pertanyaan</label>
              <textarea class="input-form" rows="5" required style="resize:vertical;"></textarea>
            </div>
            <button type="submit" class="btn-primer" style="width:100%; background:#000;">Kirim Pesan</button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <script src="js/main.js"></script>
  <script>
    function kirimPesan(e) {
      e.preventDefault();
      alert('Terima kasih! Pesan Anda telah terkirim ke Pemerintah Desa Kosar.');
      e.target.reset();
    }
  </script>
</body>
</html>
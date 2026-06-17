<?php
// Gunakan pengecekan cookie
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kontak - NamaWeb</title>
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
    <h1>Hubungi Kami</h1>
    <p>Kami siap membantu Anda. Silakan hubungi kami melalui berbagai saluran yang tersedia.</p>
  </section>

  <section style="padding:5rem 2rem;background:var(--warna-bg);">
    <div class="kontainer">

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:3rem;align-items:start;">

        <div>
          <h2 style="font-family:var(--font-judul);font-size:1.8rem;font-weight:700;margin-bottom:1.5rem;">
            Informasi Kontak
          </h2>

          <div style="background:var(--warna-bg-kartu);border:1px solid var(--warna-border);border-radius:var(--radius-sedang);padding:1.25rem;margin-bottom:1rem;display:flex;gap:1rem;align-items:flex-start;">
            <div style="font-size:1.5rem;">💬</div>
            <div>
              <div style="font-weight:700;margin-bottom:0.25rem;font-size:0.9rem;">WhatsApp</div>
              <p style="color:var(--warna-teks-muda);font-size:0.85rem;">08xx-xxxx-xxxx</p>
              <p style="color:var(--warna-teks-muda);font-size:0.8rem;">Senin - Jumat, 08.00 - 16.00</p>
            </div>
          </div>

          <div style="background:var(--warna-bg-kartu);border:1px solid var(--warna-border);border-radius:var(--radius-sedang);padding:1.25rem;margin-bottom:1rem;display:flex;gap:1rem;align-items:flex-start;">
            <div style="font-size:1.5rem;">✉️</div>
            <div>
              <div style="font-weight:700;margin-bottom:0.25rem;font-size:0.9rem;">Email</div>
              <p style="color:var(--warna-teks-muda);font-size:0.85rem;">admin@namadesa.id</p>
              <p style="color:var(--warna-teks-muda);font-size:0.8rem;">Respon dalam 1-2 hari kerja</p>
            </div>
          </div>

          <div style="background:var(--warna-bg-kartu);border:1px solid var(--warna-border);border-radius:var(--radius-sedang);padding:1.25rem;display:flex;gap:1rem;align-items:flex-start;">
            <div style="font-size:1.5rem;">📍</div>
            <div>
              <div style="font-weight:700;margin-bottom:0.25rem;font-size:0.9rem;">Kantor Desa</div>
              <p style="color:var(--warna-teks-muda);font-size:0.85rem;">Jl. Nama Desa No. 1, Kecamatan, Kabupaten</p>
              <p style="color:var(--warna-teks-muda);font-size:0.8rem;">Kode Pos: 00000</p>
            </div>
          </div>
        </div>

        <div style="background:var(--warna-bg-kartu);border:1px solid var(--warna-border);border-radius:var(--radius-sedang);padding:2rem;">
          <h2 style="font-family:var(--font-judul);font-size:1.4rem;font-weight:700;margin-bottom:1.5rem;">
            Kirim Pesan
          </h2>

          <form onsubmit="kirimPesan(event)">

            <div class="grup-form" style="margin-bottom:1rem;">
              <label for="konNama" style="display:block;font-size:0.825rem;font-weight:600;margin-bottom:0.35rem;">Nama Lengkap</label>
              <input type="text" id="konNama" class="input-form" placeholder="Nama Anda" required />
            </div>

            <div class="grup-form" style="margin-bottom:1rem;">
              <label for="konEmail" style="display:block;font-size:0.825rem;font-weight:600;margin-bottom:0.35rem;">Email</label>
              <input type="email" id="konEmail" class="input-form" placeholder="email@contoh.com" required />
            </div>

            <div class="grup-form" style="margin-bottom:1rem;">
              <label for="konSubjek" style="display:block;font-size:0.825rem;font-weight:600;margin-bottom:0.35rem;">Subjek</label>
              <input type="text" id="konSubjek" class="input-form" placeholder="Perihal pesan Anda" required />
            </div>

            <div class="grup-form" style="margin-bottom:1.25rem;">
              <label for="konPesan" style="display:block;font-size:0.825rem;font-weight:600;margin-bottom:0.35rem;">Pesan</label>
              <textarea id="konPesan" class="input-form" rows="5" placeholder="Tulis pesan Anda di sini..." required style="resize:vertical;"></textarea>
            </div>

            <button type="submit" class="btn-primer" style="width:100%;">Kirim Pesan</button>

          </form>
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
  <script>
    function kirimPesan(e) {
      e.preventDefault();
      if (typeof tampilkanLoading === "function") {
          tampilkanLoading(e.submitter);
      }
      setTimeout(function () {
        alert('Pesan Anda berhasil dikirim! Kami akan menghubungi Anda segera.');
        e.target.reset();
        if (e.submitter) {
          e.submitter.textContent = 'Kirim Pesan';
          e.submitter.disabled = false;
        }
      }, 800);
    }
  </script>
</body>
</html>
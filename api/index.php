<?php
// Tidak menggunakan session_start() lagi karena kita menggunakan $_COOKIE
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Desa Kosar - Sistem Layanan Administrasi</title>
  <link rel="stylesheet" href="../css/style.css" />
  <!-- Menambahkan Swiper CSS untuk Carousel -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <style>
    /* Styling khusus untuk Swiper di Homepage */
    .swiper { width: 100%; padding-bottom: 3rem; }
    .swiper-slide { height: auto; }
    .swiper-pagination-bullet-active { background-color: var(--warna-aksen); }
  </style>
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
    </div>
  </header>

  <section class="hero">
    <div class="hero-kiri">
      <div class="hero-kiri-konten">
        <span class="hero-label">Selamat Datang di Desa Kosar</span>
        <h1>Melayani Kebutuhan<br /><span>Administrasi Desa</span><br />dengan Lebih Praktis</h1>
        <div class="hero-tombol">
          <a href="login.php" class="btn-aksen">Ajukan Surat</a>
          <a href="tentang.php" class="btn-sekunder" style="border-color:rgba(255,255,255,0.4);color:white;">Selengkapnya</a>
        </div>
      </div>
    </div>
    <div class="hero-kanan">
      <div class="hero-kanan-konten">
        <div class="hero-gambar">
          <span style="font-size:4rem;">🏡</span>
        </div>
        <h2>Layanan Desa<br />Lebih Mudah</h2>
        <p>Website ini hadir untuk membantu masyarakat Desa Kosar dalam mengakses layanan administrasi desa secara lebih praktis, cepat, dan terarah.</p>
      </div>
    </div>
  </section>

  <section id="surat" class="seksi-layanan">
    <div class="kontainer">
      <div class="judul-seksi">
        <h2>Temukan Layanan Surat Sesuai Kebutuhan</h2>
        <p>Pilih jenis surat yang ingin diajukan dan lakukan proses pengurusan dengan mudah. (Geser untuk melihat semua)</p>
      </div>
      
      <!-- SWIPER CAROUSEL (POIN 2) -->
      <div class="swiper mySwiper">
        <div class="swiper-wrapper">
          <!-- Slide 1 -->
          <div class="swiper-slide">
            <div class="kartu-layanan" onclick="window.location.href='login.php'">
              <div class="kartu-layanan-gambar">✉️</div>
              <h3>Surat Keterangan Domisili</h3>
              <p>Bukti resmi domisili warga di Desa Kosar untuk berbagai keperluan administrasi.</p>
            </div>
          </div>
          <!-- Slide 2 -->
          <div class="swiper-slide">
            <div class="kartu-layanan" onclick="window.location.href='login.php'">
              <div class="kartu-layanan-gambar">🏪</div>
              <h3>Surat Keterangan Usaha (SKU)</h3>
              <p>Keterangan resmi menjalankan usaha di wilayah desa, untuk izin atau perbankan.</p>
            </div>
          </div>
          <!-- Slide 3 -->
          <div class="swiper-slide">
            <div class="kartu-layanan" onclick="window.location.href='login.php'">
              <div class="kartu-layanan-gambar">📋</div>
              <h3>Surat Pengantar SKCK</h3>
              <p>Surat pengantar desa sebagai syarat pembuatan SKCK di kepolisian.</p>
            </div>
          </div>
          <!-- Slide 4 -->
          <div class="swiper-slide">
            <div class="kartu-layanan" onclick="window.location.href='login.php'">
              <div class="kartu-layanan-gambar">🔍</div>
              <h3>Surat Ket. Kehilangan</h3>
              <p>Surat pengantar untuk melaporkan kehilangan barang/dokumen ke pihak berwajib.</p>
            </div>
          </div>
          <!-- Slide 5 -->
          <div class="swiper-slide">
            <div class="kartu-layanan" onclick="window.location.href='login.php'">
              <div class="kartu-layanan-gambar">💰</div>
              <h3>Surat Ket. Penghasilan</h3>
              <p>Keterangan resmi mengenai rata-rata penghasilan warga per bulan.</p>
            </div>
          </div>
        </div>
        <div class="swiper-pagination"></div>
      </div>
      
    </div>
  </section>

  <!-- Swiper JS Script -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
    var swiper = new Swiper(".mySwiper", {
      slidesPerView: 1,
      spaceBetween: 20,
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
      breakpoints: {
        768: { slidesPerView: 2, spaceBetween: 30 },
        1024: { slidesPerView: 3, spaceBetween: 30 }
      }
    });
  </script>
</body>
</html>
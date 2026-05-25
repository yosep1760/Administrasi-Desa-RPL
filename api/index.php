<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NamaWeb - Sistem Layanan Administrasi Desa</title>
  <meta name="description" content="Layanan administrasi desa secara online yang mudah, cepat, dan transparan." />
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
      <?php if(isset($_SESSION['user_id'])): ?>
          <a href="dashboard.php" class="btn-primer">Dashboard</a>
      <?php else: ?>
          <a href="login.php" class="btn-primer">Login</a>
      <?php endif; ?>
      
      <button id="tombolHamburger" class="tombol-hamburger" aria-label="Buka menu" aria-expanded="false">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
  </header>

  <section class="hero">
    <div class="hero-kiri">
      <div class="hero-kiri-konten">
        <span class="hero-label">Selamat Datang di NamaWeb</span>
        <h1>Melayani Kebutuhan<br /><span>Administrasi Desa</span><br />dengan Lebih Praktis</h1>
        <div class="hero-tombol">
          <a href="dashboard.php" class="btn-aksen">Ajukan Surat</a>
          <a href="tentang.php" class="btn-sekunder" style="border-color:rgba(255,255,255,0.4);color:white;">Selengkapnya</a>
        </div>
      </div>
    </div>
    <div class="hero-kanan">
      <div class="hero-kanan-konten">
        <div class="hero-gambar">
          <span style="font-size:3rem;">🏘️</span>
        </div>
        <h2>Layanan Desa<br />Lebih Mudah</h2>
        <p>Website ini hadir untuk membantu masyarakat dalam mengakses layanan administrasi desa secara lebih praktis, cepat, dan terarah.</p>
        <ul class="daftar-fitur">
          <li><span class="ikon-centang">✓</span> Mempermudah pengajuan surat secara online</li>
          <li><span class="ikon-centang">✓</span> Menyediakan layanan yang lebih transparan</li>
          <li><span class="ikon-centang">✓</span> Mendukung proses administrasi yang efisien</li>
        </ul>
      </div>
    </div>
  </section>

  <section id="surat" class="seksi-layanan">
    <div class="kontainer">
      <div class="judul-seksi">
        <h2>Temukan Layanan Surat Sesuai<br />Kebutuhan Anda</h2>
        <p>Pilih jenis surat yang ingin diajukan dan lakukan proses pengurusan dengan lebih mudah, cepat, dan terarah.</p>
      </div>
      <div class="grid-layanan">
        <div class="kartu-layanan" onclick="window.location.href='dashboard.php'">
          <div class="kartu-layanan-gambar">💍</div>
          <h3>Surat Pengantar Nikah</h3>
          <p>Surat keterangan pengantar dari desa untuk keperluan pernikahan di KUA atau catatan sipil.</p>
        </div>
        <div class="kartu-layanan" onclick="window.location.href='dashboard.php'">
          <div class="kartu-layanan-gambar">🏪</div>
          <h3>Surat Keterangan Usaha</h3>
          <p>Keterangan resmi bahwa warga menjalankan usaha di wilayah desa, diperlukan untuk izin atau perbankan.</p>
        </div>
        <div class="kartu-layanan" onclick="window.location.href='dashboard.php'">
          <div class="kartu-layanan-gambar">🏠</div>
          <h3>Surat Keterangan Domisili</h3>
          <p>Bukti resmi bahwa warga berdomisili di desa tersebut, digunakan untuk keperluan administrasi.</p>
        </div>
      </div>
      <div class="pagination-titik">
        <span class="aktif"></span><span></span><span></span><span></span><span></span>
      </div>
    </div>
  </section>

  <section id="alur" class="seksi-alur">
    <div class="kontainer">
      <div class="grid-alur">
        <div class="alur-judul">
          <h2>Proses Pengajuan Surat Kini Lebih Mudah dan Terstruktur</h2>
          <p>Setiap pengajuan surat diproses melalui tahapan yang jelas, mulai dari pengisian data, verifikasi dokumen, hingga persetujuan dan penyelesaian surat.</p>
          <div class="alur-gambar" style="height:180px;">
            <span style="font-size:3rem;">📋</span>
          </div>
        </div>
        <div>
          <div class="item-timeline aktif">
            <div class="timeline-kiri"><div class="titik-timeline"></div><div class="garis-timeline"></div></div>
            <div class="konten-timeline">
              <h4>Isi Pengajuan</h4>
              <p>Warga mengisi formulir pengajuan sesuai jenis surat yang dibutuhkan, kemudian mengunggah data atau dokumen pendukung melalui sistem.</p>
            </div>
          </div>
          <div class="item-timeline">
            <div class="timeline-kiri"><div class="titik-timeline"></div><div class="garis-timeline"></div></div>
            <div class="konten-timeline">
              <h4>Verifikasi Petugas</h4>
              <p>Petugas desa memeriksa kelengkapan dan keabsahan data serta dokumen yang telah diunggah oleh warga.</p>
            </div>
          </div>
          <div class="item-timeline">
            <div class="timeline-kiri"><div class="titik-timeline"></div><div class="garis-timeline"></div></div>
            <div class="konten-timeline">
              <h4>Approval Kepala Desa</h4>
              <p>Kepala desa meninjau dan memberikan persetujuan resmi atas pengajuan yang telah diverifikasi petugas.</p>
            </div>
          </div>
          <div class="item-timeline">
            <div class="timeline-kiri"><div class="titik-timeline"></div><div class="garis-timeline"></div></div>
            <div class="konten-timeline">
              <h4>Terima Notifikasi</h4>
              <p>Warga mendapat notifikasi status pengajuan secara real-time melalui dashboard.</p>
            </div>
          </div>
          <div class="item-timeline">
            <div class="timeline-kiri"><div class="titik-timeline"></div></div>
            <div class="konten-timeline">
              <h4>Cetak Surat</h4>
              <p>Setelah disetujui, warga dapat mengunduh dan mencetak surat langsung dari sistem.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="faq" class="seksi-faq">
    <div class="kontainer">
      <div class="grid-faq">
        <div class="faq-judul">
          <h2>Hal Yang Sering Ditanyakan</h2>
          <p>Berikut beberapa pertanyaan umum seputar proses pengajuan surat, persyaratan dokumen, hingga informasi status layanan yang sering ditanyakan oleh masyarakat.</p>
        </div>
        <div class="daftar-faq">
          <div class="item-faq">
            <button class="pertanyaan-faq">Bagaimana cara mendaftar akun di NamaWeb? <span class="ikon-faq">∨</span></button>
            <div class="jawaban-faq"><p>Klik tombol "Login" di pojok kanan atas, lalu pilih "Sign Up" untuk membuat akun baru.</p></div>
          </div>
          <div class="item-faq">
            <button class="pertanyaan-faq">Dokumen apa saja yang diperlukan untuk pengajuan surat? <span class="ikon-faq">∨</span></button>
            <div class="jawaban-faq"><p>Dokumen yang umumnya diperlukan adalah foto KTP dan Kartu Keluarga (KK) dalam format PDF atau gambar.</p></div>
          </div>
          <div class="item-faq">
            <button class="pertanyaan-faq">Berapa lama proses pengajuan surat diselesaikan? <span class="ikon-faq">∨</span></button>
            <div class="jawaban-faq"><p>Proses pengajuan surat biasanya diselesaikan dalam 1-3 hari kerja.</p></div>
          </div>
          <div class="item-faq">
            <button class="pertanyaan-faq">Bagaimana cara mengetahui status pengajuan surat saya? <span class="ikon-faq">∨</span></button>
            <div class="jawaban-faq"><p>Login ke akun Anda, lalu buka menu "Riwayat Pengajuan" di sidebar.</p></div>
          </div>
          <div class="item-faq">
            <button class="pertanyaan-faq">Apa yang harus dilakukan jika pengajuan saya ditolak? <span class="ikon-faq">∨</span></button>
            <div class="jawaban-faq"><p>Anda akan menerima notifikasi beserta alasan penolakan. Silakan ajukan ulang dengan benar.</p></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="seksi-kontak">
    <div class="kontainer">
      <div class="kartu-kontak">
        <h2>Masih Bingung? Kami Siap<br />Membantu Anda</h2>
        <p>Jika Anda memiliki pertanyaan, silakan hubungi kami melalui WhatsApp.</p>
        <a href="https://wa.me/0812345678" class="btn-whatsapp" target="_blank" rel="noopener"><span>💬</span> Hubungi Sekarang</a>
      </div>
    </div>
  </section>

  <footer>
    <div class="footer-grid">
      <div>
        <div class="footer-logo">NamaWeb</div>
        <p class="footer-deskripsi">Mendukung pelayanan administrasi desa yang lebih praktis, transparan, dan mudah diakses oleh masyarakat.</p>
        <p class="footer-copyright">© 2026 NamaWeb — Sistem Layanan Administrasi Desa.<br />Seluruh hak cipta dilindungi.</p>
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
      <p>NamaWeb © 2026 — Dibuat untuk kemajuan layanan desa</p>
      <button class="btn-atas" onclick="scrollKeAtas()" aria-label="Kembali ke atas">↑</button>
    </div>
  </footer>

  <script src="../js/main.js"></script>
</body>
</html>
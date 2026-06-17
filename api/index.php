<?php
// Cek sesi login untuk tombol navbar
$is_logged_in = isset($_COOKIE['user_id']);
$link_dashboard = 'login.php';
if ($is_logged_in) {
    if($_COOKIE['role'] == 'kepala_desa') $link_dashboard = 'dashboard-kades.php';
    elseif($_COOKIE['role'] == 'petugas') $link_dashboard = 'dashboard-petugas.php';
    else $link_dashboard = 'dashboard.php';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NamaWeb - Administrasi Desa</title>
    <!-- Menggunakan font Poppins agar identik dengan desain Figma -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-body: #F7F5F0; /* Krem lembut */
            --bg-box: #E6E6E6; /* Abu-abu kotak */
            --bg-img: #D9D9D9; /* Abu-abu placeholder gambar */
            --btn-maroon: #6A443B; /* Cokelat/Marun tombol */
            --bg-footer: #D6C7B8; /* Cokelat muda footer */
            --text-dark: #111111;
            --text-muted: #555555;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background-color: var(--bg-body); color: var(--text-dark); overflow-x: hidden; }
        a { text-decoration: none; color: inherit; }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 0 30px; }

        /* NAVBAR */
        nav { display: flex; justify-content: space-between; align-items: center; padding: 25px 0; }
        .logo { font-weight: 700; font-size: 1.25rem; }
        .nav-links { display: flex; gap: 30px; font-size: 0.9rem; font-weight: 500; }
        .nav-links a:hover { color: var(--btn-maroon); }
        .btn-nav { background-color: var(--btn-maroon); color: white; padding: 8px 25px; border-radius: 20px; font-size: 0.9rem; font-weight: 500; }

        /* HERO SECTION */
        .hero { background-color: var(--bg-box); border-radius: 20px; padding: 80px 60px; margin-top: 10px; display: flex; flex-direction: column; justify-content: center; min-height: 400px; }
        .hero p { font-size: 0.9rem; color: var(--text-muted); margin-bottom: 15px; font-weight: 500; }
        .hero h1 { font-size: 2.4rem; font-weight: 700; line-height: 1.3; max-width: 600px; margin-bottom: 30px; color: #000; }
        .hero-btns { display: flex; gap: 15px; }
        .btn-solid { background-color: var(--btn-maroon); color: white; padding: 10px 25px; border-radius: 25px; font-size: 0.9rem; font-weight: 500; border: 1px solid var(--btn-maroon); cursor: pointer; }
        .btn-outline { background-color: transparent; color: var(--btn-maroon); padding: 10px 25px; border-radius: 25px; font-size: 0.9rem; font-weight: 500; border: 1px solid var(--btn-maroon); cursor: pointer; }

        /* SPLIT SECTION (Layanan & Proses) */
        .split-section { display: flex; gap: 60px; align-items: center; margin-top: 100px; }
        .split-left { flex: 1; }
        .split-right { flex: 1; background-color: var(--bg-img); height: 350px; border-radius: 20px; }
        .section-title { font-size: 1.8rem; font-weight: 700; line-height: 1.3; margin-bottom: 15px; color: #000; }
        .section-desc { font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 25px; max-width: 90%; }
        
        .check-list { list-style: none; display: flex; flex-direction: column; gap: 12px; }
        .check-list li { display: flex; align-items: center; gap: 12px; font-size: 0.85rem; font-weight: 500; }
        .check-icon { width: 18px; height: 18px; border: 1.5px solid #000; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold; }

        /* GRID SURAT */
        .surat-section { margin-top: 100px; text-align: center; }
        .surat-section .section-title { margin-bottom: 10px; }
        .surat-section .section-desc { margin: 0 auto 40px; max-width: 600px; }
        .surat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; text-align: left; }
        .surat-card-img { background-color: var(--bg-img); height: 220px; border-radius: 15px; margin-bottom: 15px; }
        .surat-card h3 { font-size: 1.1rem; font-weight: 700; margin-bottom: 8px; }
        .surat-card p { font-size: 0.8rem; color: var(--text-muted); line-height: 1.5; }
        .dots { display: flex; justify-content: center; gap: 6px; margin-top: 30px; }
        .dot { width: 6px; height: 6px; background-color: #CCC; border-radius: 50%; }
        .dot.active { background-color: #333; }

        /* TIMELINE */
        .timeline { margin-top: 20px; position: relative; }
        .timeline-line { position: absolute; left: 6px; top: 15px; bottom: 15px; width: 1.5px; background-color: #000; z-index: 1; }
        .time-item { display: flex; gap: 20px; align-items: flex-start; position: relative; z-index: 2; margin-bottom: 15px; }
        .time-dot { width: 14px; height: 14px; border-radius: 50%; border: 1.5px solid #000; background-color: var(--bg-body); margin-top: 3px; flex-shrink: 0; }
        .time-content { flex: 1; padding: 10px 15px; border-radius: 10px; }
        .time-item.active .time-content { background-color: var(--bg-box); }
        .time-content h4 { font-size: 0.9rem; font-weight: 600; margin-bottom: 5px; }
        .time-content p { font-size: 0.75rem; color: var(--text-muted); line-height: 1.5; }

        /* FAQ */
        .faq-section { display: flex; gap: 60px; margin-top: 100px; align-items: flex-start; }
        .faq-left { flex: 1; }
        .faq-right { flex: 1; display: flex; flex-direction: column; gap: 15px; }
        .faq-box { background: #FFF; border: 1px solid #EAEAEA; border-radius: 10px; padding: 15px 20px; cursor: pointer; }
        .faq-header { display: flex; justify-content: space-between; align-items: center; font-size: 0.9rem; font-weight: 600; }
        .faq-body { font-size: 0.8rem; color: var(--text-muted); margin-top: 10px; line-height: 1.5; display: none; }
        .faq-box.active .faq-body { display: block; }

        /* CTA BOX */
        .cta-box { background-color: var(--bg-box); border-radius: 20px; padding: 50px 20px; text-align: center; margin-top: 100px; margin-bottom: 100px; }
        .cta-box h2 { font-size: 1.6rem; font-weight: 700; margin-bottom: 15px; }
        .cta-box p { font-size: 0.85rem; color: var(--text-muted); max-width: 600px; margin: 0 auto 25px; line-height: 1.6; }
        .btn-black { background-color: #000; color: #FFF; padding: 12px 30px; border-radius: 25px; font-size: 0.9rem; font-weight: 500; display: inline-flex; align-items: center; gap: 10px; }

        /* FOOTER */
        footer { background-color: var(--bg-footer); padding: 50px 0; color: #000; }
        .footer-content { display: flex; justify-content: space-between; align-items: flex-start; gap: 40px; }
        .foot-col-1 { max-width: 300px; }
        .foot-col-1 h3 { font-size: 1.5rem; font-weight: 700; margin-bottom: 15px; }
        .foot-col-1 p { font-size: 0.75rem; line-height: 1.6; margin-bottom: 15px; }
        .foot-title { font-size: 0.85rem; font-weight: 700; margin-bottom: 15px; text-transform: capitalize; }
        .foot-links { list-style: none; display: flex; flex-direction: column; gap: 10px; font-size: 0.75rem; }
        .btn-up { width: 30px; height: 30px; background-color: #000; color: #FFF; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; cursor: pointer; border: none; }
    </style>
</head>
<body>

<div class="container">
    <!-- Navigation -->
    <nav>
        <div class="logo">Nama Web</div>
        <div class="nav-links">
            <a href="#">Home</a>
            <a href="#">Tentang</a>
            <a href="#">Surat</a>
            <a href="#">Alur</a>
            <a href="#">FaQ</a>
            <a href="#">Kontak</a>
        </div>
        <a href="<?= $link_dashboard ?>" class="btn-nav"><?= $is_logged_in ? 'Dashboard' : 'Login' ?></a>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <p>Selamat Datang di Namaweb</p>
        <h1>Melayani Kebutuhan Administrasi Desa dengan Lebih Praktis</h1>
        <div class="hero-btns">
            <a href="<?= $link_dashboard ?>" class="btn-solid">Ajukan Surat</a>
            <a href="#" class="btn-outline">Selengkapnya</a>
        </div>
    </section>

    <!-- Split Section 1 -->
    <section class="split-section">
        <div class="split-left">
            <h2 class="section-title">Layanan Desa Lebih Mudah</h2>
            <p class="section-desc">Website ini hadir untuk membantu masyarakat dalam mengakses layanan administrasi desa secara lebih praktis, cepat, dan terarah. Melalui sistem digital, proses pengajuan surat dapat dilakukan secara online tanpa harus selalu datang langsung ke kantor desa. Kami berupaya menghadirkan pelayanan yang lebih efisien, transparan, dan mudah dijangkau oleh seluruh warga.</p>
            <ul class="check-list">
                <li><span class="check-icon">✔</span> Mempermudah pengajuan surat secara online</li>
                <li><span class="check-icon">✔</span> Menyediakan layanan yang lebih transparan</li>
                <li><span class="check-icon">✔</span> Mendukung proses administrasi desa</li>
            </ul>
        </div>
        <div class="split-right"></div>
    </section>

    <!-- Surat Grid Section -->
    <section class="surat-section">
        <h2 class="section-title">Temukan Layanan Surat Sesuai<br>Kebutuhan Anda</h2>
        <p class="section-desc">Pilih jenis surat yang ingin diajukan dan lakukan proses pengurusan dengan lebih mudah, cepat, dan terarah.</p>
        
        <div class="surat-grid">
            <div class="surat-card">
                <div class="surat-card-img"></div>
                <h3>Surat Lorem Ipsum</h3>
                <p>Lorem ipsum dolor sit amet consectetur. Maecenas pellentesque a enim quis.</p>
            </div>
            <div class="surat-card">
                <div class="surat-card-img"></div>
                <h3>Surat Lorem Ipsum</h3>
                <p>Lorem ipsum dolor sit amet consectetur. Maecenas pellentesque a enim quis.</p>
            </div>
            <div class="surat-card">
                <div class="surat-card-img"></div>
                <h3>Surat Lorem Ipsum</h3>
                <p>Lorem ipsum dolor sit amet consectetur. Maecenas pellentesque a enim quis.</p>
            </div>
        </div>
        
        <div class="dots">
            <div class="dot active"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </section>

    <!-- Split Section 2 (Alur) -->
    <section class="split-section">
        <div class="split-left">
            <h2 class="section-title">Proses Pengajuan Surat Kini Lebih Mudah dan Terstruktur</h2>
            <p class="section-desc">Setiap pengajuan surat diproses melalui tahapan yang jelas, mulai dari pengisian data, verifikasi dokumen, hingga persetujuan dan penyelesaian surat.</p>
            
            <div class="timeline">
                <div class="timeline-line"></div>
                <div class="time-item active">
                    <div class="time-dot"></div>
                    <div class="time-content">
                        <h4>1. Isi Pengajuan</h4>
                        <p>Warga mengisi formulir pengajuan sesuai jenis surat yang dibutuhkan, kemudian mengunggah dokumen-dokumen pendukung melalui sistem.</p>
                    </div>
                </div>
                <div class="time-item">
                    <div class="time-dot"></div>
                    <div class="time-content"><h4>Verifikasi Petugas</h4></div>
                </div>
                <div class="time-item">
                    <div class="time-dot"></div>
                    <div class="time-content"><h4>Approval kepala desa</h4></div>
                </div>
                <div class="time-item">
                    <div class="time-dot"></div>
                    <div class="time-content"><h4>Terima Notifikasi</h4></div>
                </div>
                <div class="time-item">
                    <div class="time-dot"></div>
                    <div class="time-content"><h4>Cetak Surat</h4></div>
                </div>
            </div>
        </div>
        <div class="split-right"></div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="faq-left">
            <h2 class="section-title">Hal Yang Sering<br>Ditanyakan</h2>
            <p class="section-desc">Berikut beberapa pertanyaan umum seputar proses pengajuan surat, persyaratan dokumen, hingga informasi status layanan yang sering ditanyakan oleh masyarakat.</p>
        </div>
        <div class="faq-right">
            <div class="faq-box active" onclick="this.classList.toggle('active')">
                <div class="faq-header"><span>Title</span> <span>^</span></div>
                <div class="faq-body">Lorem ipsum dolor sit amet consectetur adipisicing elit. Adipisci corrupti suscipit quam.</div>
            </div>
            <div class="faq-box" onclick="this.classList.toggle('active')">
                <div class="faq-header"><span>Title</span> <span>v</span></div>
                <div class="faq-body">Penjelasan untuk FAQ ini ada di sini.</div>
            </div>
            <div class="faq-box" onclick="this.classList.toggle('active')">
                <div class="faq-header"><span>Title</span> <span>v</span></div>
                <div class="faq-body">Penjelasan untuk FAQ ini ada di sini.</div>
            </div>
            <div class="faq-box" onclick="this.classList.toggle('active')">
                <div class="faq-header"><span>Title</span> <span>v</span></div>
                <div class="faq-body">Penjelasan untuk FAQ ini ada di sini.</div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-box">
        <h2>Masih Bingung? Kami Siap<br>Membantu Anda</h2>
        <p>Jika Anda memiliki pertanyaan, mengalami kendala, atau membutuhkan informasi lebih lanjut terkait layanan di Administrasi Desa, silakan hubungi kami melalui Whatsapp. Tim kami siap membantu Anda dengan lebih cepat dan ramah.</p>
        <a href="#" class="btn-black">💬 Hubungi Sekarang</a>
    </section>
</div>

<!-- FOOTER -->
<footer>
    <div class="container footer-content">
        <div class="foot-col-1">
            <h3>NamaWeb</h3>
            <p>Mendukung pelayanan administrasi desa yang lebih praktis, transparan, dan mudah diakses oleh masyarakat.</p>
            <p>&copy; 2026 NamaWeb - Sistem Layanan Administrasi Desa. Seluruh hak cipta dilindungi.</p>
        </div>
        <div>
            <h4 class="foot-title">Navigasi</h4>
            <ul class="foot-links">
                <li>Beranda</li>
                <li>Tentang Kami</li>
                <li>Jenis Surat</li>
                <li>Alur Pengajuan</li>
                <li>FaQ</li>
                <li>Hubungi Kami</li>
            </ul>
        </div>
        <div>
            <h4 class="foot-title">Kontak Kami</h4>
            <ul class="foot-links">
                <li>📞 08xx-xxxx-xxxx</li>
                <li>✉️ Email: admin@namaweb.id</li>
                <li>📍 Kantor Desa (Nama Desa)</li>
            </ul>
        </div>
        <div>
            <button class="btn-up" onclick="window.scrollTo({top: 0, behavior: 'smooth'});">^</button>
        </div>
    </div>
</footer>

</body>
</html>
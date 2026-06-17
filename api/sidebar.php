<?php
// Deteksi role user dan halaman aktif untuk memberikan warna menu yang sesuai
$role_user = isset($_COOKIE['role']) ? $_COOKIE['role'] : '';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div id="overlaySidebar" class="overlay-sidebar"></div>

<aside id="sidebar" class="sidebar">
  <div class="sidebar-header" style="display: flex; align-items: center; gap: 10px;">
    <span style="font-size:1.5rem;">🏘️</span> SiKosar
  </div>
  <div class="sidebar-cari">
    <input type="search" id="inputCariSidebar" class="input-cari" placeholder="Cari menu..." onkeyup="cariMenuSidebar()" />
  </div>
  <nav class="sidebar-nav" id="menuSidebarNav">
    
    <?php if($role_user == 'warga'): ?>
        <a href="dashboard.php" class="sidebar-link <?= ($current_page == 'dashboard.php') ? 'aktif' : '' ?>">
          <span class="sidebar-link-ikon">📊</span> Dashboard Saya
        </a>
        <div class="sidebar-label">Ajukan Surat <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="pengajuan.php?id_jenis=1" class="sidebar-link <?= ($current_page == 'pengajuan.php' && isset($_GET['id_jenis']) && $_GET['id_jenis']==1) ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">✉</span> Surat Pengantar Nikah</a>
          <a href="pengajuan.php?id_jenis=2" class="sidebar-link <?= ($current_page == 'pengajuan.php' && isset($_GET['id_jenis']) && $_GET['id_jenis']==2) ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">✉</span> Surat Keterangan Usaha</a>
          <a href="pengajuan.php?id_jenis=3" class="sidebar-link <?= ($current_page == 'pengajuan.php' && isset($_GET['id_jenis']) && $_GET['id_jenis']==3) ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">✉</span> Surat Keter. Domisili</a>
          <a href="pengajuan.php?id_jenis=4" class="sidebar-link <?= ($current_page == 'pengajuan.php' && isset($_GET['id_jenis']) && $_GET['id_jenis']==4) ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">✉</span> SKTM</a>
          <a href="pengajuan.php?id_jenis=5" class="sidebar-link <?= ($current_page == 'pengajuan.php' && isset($_GET['id_jenis']) && $_GET['id_jenis']==5) ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">✉</span> Surat Lainnya</a>
        </div>
        <div class="sidebar-label">Informasi <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="riwayat.php" class="sidebar-link <?= ($current_page == 'riwayat.php' || $current_page == 'detail-surat.php' || $current_page == 'cetak.php') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">🕐</span> Riwayat Pengajuan</a>
          <a href="profil.php" class="sidebar-link <?= ($current_page == 'profil.php') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">👤</span> Profil Saya</a>
        </div>

    <?php elseif($role_user == 'petugas'): ?>
        <div class="sidebar-label">Dashboard <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="dashboard-petugas.php" class="sidebar-link <?= ($current_page == 'dashboard-petugas.php') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">🏠</span> Home</a>
        </div>
        <div class="sidebar-label">Layanan <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="petugas-masuk.php" class="sidebar-link <?= ($current_page == 'petugas-masuk.php' || ($current_page == 'detail-surat.php' && isset($_GET['from']) && $_GET['from']=='masuk')) ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">📩</span> Surat Masuk</a>
          <a href="petugas-diproses.php" class="sidebar-link <?= ($current_page == 'petugas-diproses.php') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">⏳</span> Sedang Diproses</a>
          <a href="petugas-upload.php" class="sidebar-link <?= ($current_page == 'petugas-upload.php') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">📤</span> Upload Surat (Selesai)</a>
          <a href="petugas-ditolak.php" class="sidebar-link <?= ($current_page == 'petugas-ditolak.php') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">❌</span> Surat Ditolak</a>
        </div>
        <div class="sidebar-label">Kelola Data <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="petugas-warga.php" class="sidebar-link <?= ($current_page == 'petugas-warga.php' || $current_page == 'petugas-warga-tambah.php') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">👥</span> Data Warga</a>
        </div>
        <div class="sidebar-label">Pengaturan <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="profil.php" class="sidebar-link <?= ($current_page == 'profil.php') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">👤</span> Profil Saya</a>
        </div>

    <?php elseif($role_user == 'kepala_desa'): ?>
        <div class="sidebar-label">Dashboard <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="dashboard-kades.php" class="sidebar-link <?= ($current_page == 'dashboard-kades.php') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">🏠</span> Home</a>
        </div>
        <div class="sidebar-label">Layanan <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="kades-request.php" class="sidebar-link <?= ($current_page == 'kades-request.php' || $current_page == 'detail-surat.php') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">📩</span> Request Approval</a>
          <a href="kades-disetujui.php" class="sidebar-link <?= ($current_page == 'kades-disetujui.php') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">✅</span> Surat Disetujui</a>
          <a href="kades-ditolak.php" class="sidebar-link <?= ($current_page == 'kades-ditolak.php') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">❌</span> Surat Ditolak</a>
        </div>
        <div class="sidebar-label">Pengaturan <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="profil.php" class="sidebar-link <?= ($current_page == 'profil.php') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">👤</span> Profil Saya</a>
        </div>
    <?php endif; ?>

  </nav>
</aside>

<script>
// Fungsi JavaScript untuk Fitur Pencarian Sidebar (Fix Poin 6)
function cariMenuSidebar() {
    let input = document.getElementById('inputCariSidebar').value.toLowerCase();
    let links = document.querySelectorAll('#menuSidebarNav .sidebar-link');
    
    links.forEach(link => {
        let text = link.textContent.toLowerCase();
        if(text.includes(input)) {
            link.style.display = "flex";
            let parentSub = link.closest('.sidebar-sub');
            if(parentSub) {
                parentSub.style.display = "block";
            }
        } else {
            link.style.display = "none";
        }
    });

    let labels = document.querySelectorAll('#menuSidebarNav .sidebar-label');
    labels.forEach(label => {
        let sub = label.nextElementSibling;
        if(sub && sub.classList.contains('sidebar-sub')) {
            let visibleLinks = sub.querySelectorAll('.sidebar-link[style="display: flex;"], .sidebar-link:not([style*="display: none"])');
            if(visibleLinks.length === 0 && input !== "") {
                label.style.display = "none";
                sub.style.display = "none";
            } else {
                label.style.display = "flex";
                if(input !== "") sub.style.display = "block"; 
            }
        }
    });
    
    if(input === "") {
        links.forEach(link => link.style.display = "flex");
        labels.forEach(label => {
            label.style.display = "flex";
        });
    }
}
</script>
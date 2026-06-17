<?php
$role_sidebar = isset($_COOKIE['role']) ? $_COOKIE['role'] : '';

// Mengambil URL saat ini untuk menandai menu yang 'aktif'
$current_page = basename($_SERVER['PHP_SELF']);
$current_query = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
?>

<div id="overlaySidebar" class="overlay-sidebar"></div>

<aside id="sidebar" class="sidebar">
  <div class="sidebar-header" style="font-weight: 800; font-size: 1.25rem; color: var(--warna-primer);">
    🏛️ Desa Kosar
  </div>
  
  <div class="sidebar-cari">
    <input type="search" id="searchInput" class="input-cari" placeholder="Cari menu..." onkeyup="searchSidebar()" />
  </div>

  <nav class="sidebar-nav" id="sidebarNav">

    <?php if ($role_sidebar == 'warga'): ?>
        <a href="dashboard.php" class="sidebar-link <?= ($current_page == 'dashboard.php') ? 'aktif' : '' ?>">
          <span class="sidebar-link-ikon">📊</span> Dashboard Saya
        </a>
        
        <div class="sidebar-label">Ajukan Surat <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="pengajuan.php?id_jenis=1" class="sidebar-link <?= ($current_query == 'id_jenis=1') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">✉</span> Ket. Domisili</a>
          <a href="pengajuan.php?id_jenis=2" class="sidebar-link <?= ($current_query == 'id_jenis=2') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">🏪</span> Ket. Usaha (SKU)</a>
          <a href="pengajuan.php?id_jenis=3" class="sidebar-link <?= ($current_query == 'id_jenis=3') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">📋</span> Pengantar SKCK</a>
          <a href="pengajuan.php?id_jenis=4" class="sidebar-link <?= ($current_query == 'id_jenis=4') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">🔍</span> Ket. Kehilangan</a>
          <a href="pengajuan.php?id_jenis=5" class="sidebar-link <?= ($current_query == 'id_jenis=5') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">💰</span> Ket. Penghasilan</a>
        </div>
        
        <div class="sidebar-label">Informasi <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="riwayat.php" class="sidebar-link <?= ($current_page == 'riwayat.php') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">🕐</span> Riwayat & Tracking</a>
          <a href="profil.php" class="sidebar-link <?= ($current_page == 'profil.php') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">👤</span> Profil Saya</a>
        </div>

    <?php elseif ($role_sidebar == 'petugas'): ?>
        <a href="dashboard-petugas.php" class="sidebar-link <?= ($current_page == 'dashboard-petugas.php') ? 'aktif' : '' ?>">
          <span class="sidebar-link-ikon">🏠</span> Dashboard Petugas
        </a>
        
        <div class="sidebar-label">Layanan Surat <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="petugas-masuk.php" class="sidebar-link <?= ($current_page == 'petugas-masuk.php') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">📩</span> Surat Masuk</a>
          <a href="petugas-diproses.php" class="sidebar-link <?= ($current_page == 'petugas-diproses.php') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">⏳</span> Sedang Diproses</a>
          <a href="petugas-upload.php" class="sidebar-link <?= ($current_page == 'petugas-upload.php') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">📤</span> Upload Surat (ACC)</a>
          <a href="petugas-ditolak.php" class="sidebar-link <?= ($current_page == 'petugas-ditolak.php') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">❌</span> Surat Ditolak</a>
        </div>
        
        <div class="sidebar-label">Kelola Data <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="petugas-warga.php" class="sidebar-link <?= ($current_page == 'petugas-warga.php' || $current_page == 'tambah-warga.php') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">👥</span> Data Warga</a>
        </div>
        
        <div class="sidebar-label">Pengaturan <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="profil.php" class="sidebar-link <?= ($current_page == 'profil.php') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">👤</span> Profil Saya</a>
        </div>

    <?php elseif ($role_sidebar == 'kepala_desa'): ?>
        <a href="dashboard-kades.php" class="sidebar-link <?= ($current_page == 'dashboard-kades.php') ? 'aktif' : '' ?>">
          <span class="sidebar-link-ikon">🏠</span> Dashboard Kades
        </a>
        
        <div class="sidebar-label">Persetujuan <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="kades-request.php" class="sidebar-link <?= ($current_page == 'kades-request.php') ? 'aktif' : '' ?>"><span class="sidebar-link-ikon">📩</span> Request Approval</a>
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
function searchSidebar() {
    let input = document.getElementById('searchInput').value.toLowerCase();
    let links = document.querySelectorAll('#sidebarNav .sidebar-link');
    let labels = document.querySelectorAll('#sidebarNav .sidebar-label');
    
    // Sembunyikan label kategori jika sedang mencari
    labels.forEach(label => {
        label.style.display = input === "" ? "flex" : "none";
    });

    links.forEach(link => {
        let text = link.textContent.toLowerCase();
        if (text.includes(input)) {
            link.style.display = "flex";
            // Tampilkan parent (sub-menu) agar link yang ketemu tidak tersembunyi
            if(link.parentElement.classList.contains('sidebar-sub')) {
                link.parentElement.style.display = "block";
            }
        } else {
            link.style.display = "none";
        }
    });

    // Reset layout jika input kosong
    if(input === "") {
        let subs = document.querySelectorAll('#sidebarNav .sidebar-sub');
        subs.forEach(sub => sub.style.display = ""); // kembali ke default css
    }
}
</script>
<?php
require 'koneksi.php';

// Lindungi halaman: Pastikan yang login HANYA KADES
if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'kades') {
    header("Location: login.php");
    exit;
}

$nama_kades = $_COOKIE['nama'];

// Logika Kades: Menyetujui surat
if (isset($_GET['approve_id'])) {
    $id_surat = (int)$_GET['approve_id'];
    $conn->query("UPDATE surat SET status='Selesai' WHERE id=$id_surat");
    header("Location: kades-request.php");
    exit;
}

// Logika Kades: Menolak Surat DENGAN ALASAN (Fitur Extend Diagram)
if (isset($_GET['reject_id']) && isset($_GET['alasan'])) {
    $id_surat = (int)$_GET['reject_id'];
    $alasan = $conn->real_escape_string($_GET['alasan']);
    
    // Trik Cerdas: Gabungkan alasan penolakan Kades ke dalam keterangan
    $conn->query("UPDATE surat SET status='Ditolak', keterangan = CONCAT(keterangan, ' | ❌ DITOLAK KADES: ', '$alasan') WHERE id=$id_surat");
    
    header("Location: kades-request.php");
    exit;
}

// Ambil HANYA pengajuan yang butuh persetujuan Kades
$query = "SELECT surat.*, pengguna.nama AS nama_warga 
          FROM surat 
          JOIN pengguna ON surat.id_warga = pengguna.id 
          WHERE surat.status = 'Persetujuan Kades'
          ORDER BY surat.tanggal ASC"; 
$data_surat = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Request Surat - NamaWeb</title>
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
        <div class="sidebar-label">Dashboard <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="dashboard-kades.php" class="sidebar-link"><span class="sidebar-link-ikon">🏠</span>Home</a>
        </div>
        <div class="sidebar-label">Layanan <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="kades-request.php" class="sidebar-link aktif"><span class="sidebar-link-ikon">📩</span>Request Surat</a>
          <a href="kades-disetujui.php" class="sidebar-link"><span class="sidebar-link-ikon">✅</span>Surat Disetujui</a>
          <a href="kades-ditolak.php" class="sidebar-link"><span class="sidebar-link-ikon">❌</span>Surat Ditolak</a>
        </div>
        <div class="sidebar-label">Pengaturan <span class="sidebar-label-ikon">∧</span></div>
        <div class="sidebar-sub">
          <a href="profil.php" class="sidebar-link"><span class="sidebar-link-ikon">👤</span>Profil Saya</a>
        </div>
      </nav>
    </aside>

    <div class="konten-dashboard">
      <header class="header-dashboard">
        <div style="display:flex;align-items:center;gap:0.75rem;">
          <button id="tombolBukaSidebar" class="tombol-hamburger" style="display:flex;">
            <span></span><span></span><span></span>
          </button>
          <div class="header-pengguna">
            <h3>Halo, <?= htmlspecialchars($nama_kades) ?></h3>
            <span>Kepala Desa</span>
          </div>
        </div>
        <form action="logout.php" method="POST" style="margin: 0;">
            <button type="submit" class="avatar-pengguna" title="Keluar" onclick="return confirm('Keluar dari sistem?');" style="cursor:pointer;">👤</button>
        </form>
      </header>

      <main class="area-konten">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
          <h1 style="font-family:var(--font-judul);font-size:1.5rem;font-weight:700;">Antrean Request Surat</h1>
        </div>

        <div class="kartu-tabel">
          <table class="tabel-data">
            <thead>
              <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Pemohon (Warga)</th>
                <th>Jenis Surat</th>
                <th>Keterangan</th>
                <th style="text-align:center;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($data_surat->num_rows > 0): ?>
                  <?php $no = 1; while($row = $data_surat->fetch_assoc()): ?>
                    <tr>
                      <td><?= $no++ ?></td>
                      <td><?= date('d M Y, H:i', strtotime($row['tanggal'])) ?></td>
                      <td><strong><?= htmlspecialchars($row['nama_warga']) ?></strong></td>
                      <td><?= htmlspecialchars($row['jenis_surat']) ?></td>
                      <td><?= htmlspecialchars($row['keterangan']) ?></td>
                      <td style="display:flex; gap:0.5rem; justify-content:center;">
                          <a href="?approve_id=<?= $row['id'] ?>" class="btn-primer btn-kecil" style="background:#22c55e; border:none; text-decoration:none;" onclick="return confirm('Yakin ingin menyetujui surat ini?');">✔ Setujui</a>
                          
                          <button onclick="tolakSuratKades(<?= $row['id'] ?>)" class="btn-sekunder btn-kecil" style="color:#ef4444; border-color:#ef4444; text-decoration:none; cursor:pointer;">✖ Tolak</button>
                      </td>
                    </tr>
                  <?php endwhile; ?>
              <?php else: ?>
                  <tr>
                    <td colspan="6" style="text-align:center; padding: 20px;">Belum ada surat yang memerlukan persetujuan saat ini.</td>
                  </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </main>
    </div>
  </div>

  <script src="../js/main.js"></script>
  
  <script>
    function tolakSuratKades(idSurat) {
        let alasan = prompt("Masukkan alasan Bapak/Ibu Kepala Desa menolak surat ini (wajib diisi):");
        
        if (alasan != null && alasan.trim() !== "") {
            window.location.href = "?reject_id=" + idSurat + "&alasan=" + encodeURIComponent(alasan);
        } else if (alasan != null) {
            alert("Proses dibatalkan! Alasan penolakan WAJIB diisi.");
        }
    }
  </script>
</body>
</html>
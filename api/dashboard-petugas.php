<?php
require 'koneksi.php';

// Lindungi halaman: Pastikan yang login HANYA PETUGAS (Gunakan COOKIE)
if (!isset($_COOKIE['user_id']) || $_COOKIE['role'] != 'petugas') {
    header("Location: login.php");
    exit;
}

$nama_petugas = $_COOKIE['nama'];

// Logika untuk mengubah status surat
if (isset($_GET['update_id']) && isset($_GET['status'])) {
    $id_surat = (int)$_GET['update_id'];
    $status_baru = $conn->real_escape_string($_GET['status']);
    
    // Update status di database TiDB
    $conn->query("UPDATE surat SET status='$status_baru' WHERE id=$id_surat");
    
    // Refresh halaman agar bersih dari link update
    header("Location: dashboard-petugas.php");
    exit;
}

// Hitung statistik khusus Petugas
$c_menunggu = $conn->query("SELECT COUNT(*) as c FROM surat WHERE status='Menunggu'")->fetch_assoc()['c'];
$c_diproses = $conn->query("SELECT COUNT(*) as c FROM surat WHERE status='Diproses'")->fetch_assoc()['c'];
$c_selesai = $conn->query("SELECT COUNT(*) as c FROM surat WHERE status='Selesai'")->fetch_assoc()['c'];

// Ambil SEMUA data surat dari database dan gabungkan dengan nama warga
$query = "SELECT surat.*, pengguna.nama AS nama_warga 
          FROM surat 
          JOIN pengguna ON surat.id_warga = pengguna.id 
          ORDER BY surat.id DESC";
$data_surat = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas Desa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">AdminDesa</a>
            <div class="d-flex text-white align-items-center">
                <span class="me-3 d-none d-md-block"><i class="fa-solid fa-user-shield me-1"></i> Halo, <?= htmlspecialchars($nama_petugas) ?></span>
                <form action="logout.php" method="POST" style="margin: 0;">
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Keluar dari sistem?');">
                        <i class="fa-solid fa-sign-out-alt"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block bg-white sidebar shadow-sm min-vh-100 p-3">
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <a class="nav-link active fw-bold text-primary bg-light rounded" href="dashboard-petugas.php">
                            <i class="fa-solid fa-home me-2"></i> Dashboard
                        </a>
                    </li>
                </ul>
            </nav>

            <main class="col-md-10 ms-sm-auto px-md-4 py-4">
                <h3 class="mb-4 fw-bold text-dark">Kelola Pengajuan Surat</h3>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-secondary mb-3 shadow-sm border-0">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fa-solid fa-inbox"></i> Menunggu Diproses</h6>
                                <h2 class="card-text fw-bold"><?= $c_menunggu ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-dark bg-warning mb-3 shadow-sm border-0">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fa-solid fa-spinner"></i> Sedang Diproses</h6>
                                <h2 class="card-text fw-bold"><?= $c_diproses ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-success mb-3 shadow-sm border-0">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fa-solid fa-check-circle"></i> Selesai</h6>
                                <h2 class="card-text fw-bold"><?= $c_selesai ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-bold text-primary py-3">
                        <i class="fa-solid fa-table me-1"></i> Daftar Antrean Pengajuan Warga
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-secondary">
                                    <tr>
                                        <th class="ps-4">No</th>
                                        <th>Tanggal</th>
                                        <th>NIK / Nama Warga</th>
                                        <th>Jenis Surat</th>
                                        <th>Keterangan</th>
                                        <th>Status</th>
                                        <th class="text-center">Aksi Petugas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($data_surat->num_rows > 0): ?>
                                        <?php $no = 1; while($row = $data_surat->fetch_assoc()): ?>
                                            <?php 
                                                // Warna badge status bootstrap
                                                $badgeClass = 'bg-secondary';
                                                if($row['status'] == 'Diproses' || $row['status'] == 'Persetujuan Kades') $badgeClass = 'bg-warning text-dark';
                                                if($row['status'] == 'Selesai') $badgeClass = 'bg-success';
                                                if($row['status'] == 'Ditolak') $badgeClass = 'bg-danger';
                                            ?>
                                            <tr>
                                                <td class="ps-4"><?= $no++ ?></td>
                                                <td><?= date('d M Y, H:i', strtotime($row['tanggal'])) ?></td>
                                                <td>
                                                    <strong><?= htmlspecialchars($row['nama_warga']) ?></strong><br>
                                                    <small class="text-muted"><?= htmlspecialchars($row['nik']) ?></small>
                                                </td>
                                                <td><?= htmlspecialchars($row['jenis_surat']) ?></td>
                                                <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                                <td><span class="badge <?= $badgeClass ?>"><?= $row['status'] ?></span></td>
                                                <td class="text-center">
                                                    <a href="?update_id=<?= $row['id'] ?>&status=Diproses" class="btn btn-sm btn-outline-warning mb-1" title="Proses">
                                                        <i class="fa-solid fa-spinner"></i>
                                                    </a>
                                                    <a href="?update_id=<?= $row['id'] ?>&status=Persetujuan Kades" class="btn btn-sm btn-outline-primary mb-1" title="Kirim ke Kades">
                                                        <i class="fa-solid fa-file-signature"></i>
                                                    </a>
                                                    <a href="?update_id=<?= $row['id'] ?>&status=Selesai" class="btn btn-sm btn-outline-success mb-1" title="Selesai">
                                                        <i class="fa-solid fa-check"></i>
                                                    </a>
                                                    <a href="?update_id=<?= $row['id'] ?>&status=Ditolak" class="btn btn-sm btn-outline-danger mb-1" title="Tolak">
                                                        <i class="fa-solid fa-times"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">Belum ada pengajuan masuk dari warga.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

</body>
</html>
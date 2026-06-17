<?php
require 'koneksi.php';

// Cek Cookie
if (!isset($_COOKIE['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user_login = (int)$_COOKIE['user_id'];
$role_user = $_COOKIE['role'];

if (!isset($_GET['id'])) {
    die("ID Surat tidak ditemukan.");
}

$id_pengajuan = (int)$_GET['id'];

// Ambil data surat
$query = $conn->query("
    SELECT ps.*, u.nama_lengkap AS nama_pemohon, u.NIK AS nik_pemohon, u.alamat, js.nama_surat 
    FROM Pengajuan_Surat ps 
    JOIN Users u ON ps.id_user = u.id_user 
    JOIN Jenis_Surat js ON ps.id_jenis = js.id_jenis 
    WHERE ps.id_pengajuan = $id_pengajuan
");

if ($query->num_rows == 0) {
    die("Data surat tidak ditemukan.");
}

$data = $query->fetch_assoc();

if ($role_user == 'warga' && $data['id_user'] != $id_user_login) {
    die("Akses Ditolak! Anda tidak berhak mencetak surat ini.");
}

if ($data['status'] != 'selesai') {
    die("Surat ini belum selesai diproses.");
}

// Data Output
$no_surat = htmlspecialchars($data['no_surat']);
$tgl_surat = date('d F Y', strtotime($data['tanggal_surat']));
$nama_pemohon = htmlspecialchars($data['nama_pemohon']);
$nik_pemohon = htmlspecialchars($data['nik_pemohon']);
$nama_surat = htmlspecialchars($data['nama_surat']);
$alamat_pemohon = htmlspecialchars($data['alamat']) ?: "Desa Kosar, Kec. Makmur Jaya";

// Ekstrak keperluan (menghilangkan bracket custom form)
$keperluan_raw = htmlspecialchars($data['keperluan']);
$keperluan_bersih = preg_replace('/\[.*?\]\s*/', '', $keperluan_raw);
$keperluan_bersih = str_replace("Catatan Tambahan: ", "", $keperluan_bersih);
if(empty(trim($keperluan_bersih))) $keperluan_bersih = "Keperluan Administrasi Umum";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak <?= $nama_surat ?> - SiKosar</title>
    <style>
        body {
            background: #e2e8f0;
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .kertas-a4 {
            background: white;
            width: 210mm;
            min-height: 297mm;
            padding: 25mm;
            box-sizing: border-box;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            position: relative;
        }
        .kop-surat { text-align: center; border-bottom: 4px double black; padding-bottom: 10px; margin-bottom: 25px; }
        .kop-surat h1 { margin: 0; font-size: 1.6rem; text-transform: uppercase; }
        .kop-surat h2 { margin: 5px 0 0 0; font-size: 1.3rem; text-transform: uppercase; }
        .kop-surat p { margin: 5px 0 0 0; font-size: 1rem; }
        .judul-surat { text-align: center; margin-bottom: 30px; }
        .judul-surat h3 { margin: 0; text-decoration: underline; text-transform: uppercase; font-size: 1.25rem; }
        .judul-surat p { margin: 5px 0 0 0; font-size: 1.1rem; }
        .isi-surat { line-height: 1.8; text-align: justify; font-size: 1.1rem; }
        .tabel-identitas { width: 100%; margin: 15px 0 15px 30px; font-size: 1.1rem; }
        .tabel-identitas td { padding: 4px; vertical-align: top; }
        .ttd-box { float: right; width: 250px; text-align: center; margin-top: 50px; font-size: 1.1rem;}
        .ttd-box p { margin: 0; }
        .ttd-space { height: 90px; } 
        .area-tombol { margin-bottom: 20px; }
        .btn-cetak { background: #16a34a; color: white; padding: 12px 25px; border: none; border-radius: 6px; cursor: pointer; font-size: 1.1rem; font-family: Arial, sans-serif; font-weight: bold;}
        
        @media print {
            body { background: white; padding: 0; }
            .kertas-a4 { box-shadow: none; width: 100%; min-height: auto; padding: 0; }
            .area-tombol { display: none; }
        }
    </style>
</head>
<body> 
    <div class="area-tombol">
        <button class="btn-cetak" onclick="window.print()">🖨️ Print / Simpan PDF</button>
    </div>

    <div class="kertas-a4">
        <div class="kop-surat">
            <h1>PEMERINTAH KABUPATEN INDONESIA</h1>
            <h2>KECAMATAN MAKMUR JAYA</h2>
            <h2>KANTOR KEPALA DESA KOSAR</h2>
            <p>Jl. Raya Balai Desa No. 1, Desa Kosar, Kec. Makmur Jaya, Kode Pos 12345</p>
        </div>

        <div class="judul-surat">
            <h3><?= strtoupper($nama_surat) ?></h3>
            <p>Nomor: <?= $no_surat ?></p>
        </div>

        <div class="isi-surat">
            <p>Yang bertanda tangan di bawah ini, Kepala Desa Kosar, Kecamatan Makmur Jaya, menerangkan dengan sebenarnya bahwa:</p>
            
            <table class="tabel-identitas">
                <tr><td style="width: 200px;">Nama Lengkap</td><td style="width: 15px;">:</td><td><strong><?= $nama_pemohon ?></strong></td></tr>
                <tr><td>Nomor Induk Kependudukan</td><td>:</td><td><?= $nik_pemohon ?></td></tr>
                <tr><td>Alamat Domisili</td><td>:</td><td><?= $alamat_pemohon ?></td></tr>
            </table>

            <p>Orang tersebut di atas adalah benar-benar warga Desa Kosar yang berdomisili di alamat tersebut. Berdasarkan pengamatan kami, surat ini diterbitkan untuk keperluan:</p>
            
            <p style="text-align: center; font-weight: bold; margin: 25px 0;">" <?= $keperluan_bersih ?> "</p>

            <p>Demikian surat keterangan ini dibuat dengan sebenarnya dan agar dapat dipergunakan sebagaimana mestinya oleh pihak yang berkepentingan.</p>
        </div>

        <div class="ttd-box">
            <p>Desa Kosar, <?= $tgl_surat ?></p>
            <p>Kepala Desa Kosar,</p>
            <div class="ttd-space"></div>
            <p style="text-decoration: underline; font-weight: bold;">Nama Kepala Desa</p>
            <p>NIP. 19800101 201001 1 001</p>
        </div>
    </div>
</body>
</html>
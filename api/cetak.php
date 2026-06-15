<?php
require 'koneksi.php';

// Cek Cookie
if (!isset($_COOKIE['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_COOKIE['user_id'];
$role_user = $_COOKIE['role'];

if (!isset($_GET['id'])) {
    die("ID Surat tidak ditemukan.");
}

$id_surat = (int)$_GET['id'];

// Ambil data surat
$query = $conn->query("SELECT surat.*, pengguna.nama AS nama_pemohon, pengguna.nik AS nik_pemohon 
                       FROM surat 
                       JOIN pengguna ON surat.id_warga = pengguna.id 
                       WHERE surat.id = $id_surat");

if ($query->num_rows == 0) {
    die("Data surat tidak valid.");
}

$data = $query->fetch_assoc();

// Pastikan surat sudah selesai
if ($data['status'] != 'Selesai') {
    die("Surat ini belum selesai diproses atau belum di-upload oleh petugas.");
}

// Ekstrak Nomor Surat dan Tanggal Surat dari kolom keterangan yang di-upload Petugas
$keterangan_asli = $data['keterangan'];
$no_surat = "145/___/DS-KOSAR/2026"; // Default jika kosong
$tgl_surat = date('Y-m-d');

if (strpos($keterangan_asli, '| 📄 NO SURAT:') !== false) {
    $parts = explode('|', $keterangan_asli);
    $keterangan_asli = trim($parts[0]); // Ambil keperluan awal warga
    
    foreach ($parts as $p) {
        if (strpos($p, 'NO SURAT:') !== false) {
            $no_surat = trim(str_replace('📄 NO SURAT:', '', $p));
        }
        if (strpos($p, 'TGL:') !== false) {
            $tgl_surat = trim(str_replace('TGL:', '', $p));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak <?= htmlspecialchars($data['jenis_surat']) ?></title>
    <style>
        /* Gaya Khusus Kertas A4 */
        body {
            background: #e2e8f0;
            font-family: 'Times New Roman', Times, serif; /* Font resmi surat menyurat */
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
        }
        .kertas-a4 {
            background: white;
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            box-sizing: border-box;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: relative;
        }
        /* Kop Surat */
        .kop-surat {
            text-align: center;
            border-bottom: 3px solid black;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .kop-surat h1 { margin: 0; font-size: 1.5rem; text-transform: uppercase; }
        .kop-surat h2 { margin: 5px 0 0 0; font-size: 1.2rem; text-transform: uppercase; }
        .kop-surat p { margin: 5px 0 0 0; font-size: 0.9rem; }
        
        /* Judul Surat */
        .judul-surat {
            text-align: center;
            margin-bottom: 30px;
        }
        .judul-surat h3 {
            margin: 0;
            text-decoration: underline;
            text-transform: uppercase;
            font-size: 1.2rem;
        }
        .judul-surat p { margin: 5px 0 0 0; }
        
        /* Isi Surat */
        .isi-surat {
            line-height: 1.6;
            text-align: justify;
        }
        .tabel-identitas {
            width: 100%;
            margin: 15px 0 15px 30px;
        }
        .tabel-identitas td {
            padding: 5px;
            vertical-align: top;
        }
        
        /* Tanda Tangan */
        .ttd-box {
            float: right;
            width: 250px;
            text-align: center;
            margin-top: 50px;
        }
        .ttd-box p { margin: 0; }
        .ttd-space { height: 80px; } /* Ruang untuk stempel/tanda tangan */
        
        /* Tombol Cetak (Sembunyi saat diprint) */
        .area-tombol {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-cetak {
            background: #16a34a;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-family: Arial, sans-serif;
        }
        
        /* CSS Print */
        @media print {
            body { background: white; padding: 0; }
            .kertas-a4 { box-shadow: none; width: 100%; min-height: auto; padding: 0; }
            .area-tombol { display: none; } /* Tombol akan hilang di PDF */
        }
    </style>
</head>
<body onload="window.print()"> <div>
        <div class="area-tombol">
            <button class="btn-cetak" onclick="window.print()">🖨️ Cetak / Simpan sebagai PDF</button>
        </div>

        <div class="kertas-a4">
            <div class="kop-surat">
                <h1>PEMERINTAH KABUPATEN INDONESIA</h1>
                <h2>KECAMATAN MAKMUR JAYA</h2>
                <h2>KANTOR KEPALA DESA KOSAR</h2>
                <p>Jl. Raya Balai Desa No. 1, Desa Kosar, Kec. Makmur Jaya, Kode Pos 12345</p>
            </div>

            <div class="judul-surat">
                <h3><?= strtoupper(htmlspecialchars($data['jenis_surat'])) ?></h3>
                <p>Nomor: <?= htmlspecialchars($no_surat) ?></p>
            </div>

            <div class="isi-surat">
                <p>Yang bertanda tangan di bawah ini, Kepala Desa Kosar, Kecamatan Makmur Jaya, menerangkan dengan sebenarnya bahwa:</p>
                
                <table class="tabel-identitas">
                    <tr>
                        <td style="width: 150px;">Nama Lengkap</td>
                        <td style="width: 10px;">:</td>
                        <td><strong><?= htmlspecialchars($data['nama_pemohon']) ?></strong></td>
                    </tr>
                    <tr>
                        <td>Nomor Induk Kependudukan (NIK)</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($data['nik_pemohon']) ?></td>
                    </tr>
                    <tr>
                        <td>Pekerjaan</td>
                        <td>:</td>
                        <td>Wiraswasta / Pegawai / Lainnya</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>Desa Kosar, RT 01 / RW 02, Kec. Makmur Jaya</td>
                    </tr>
                </table>

                <p>Orang tersebut di atas adalah benar-benar warga Desa Kosar yang berdomisili di alamat tersebut. Surat ini diterbitkan sebagai persyaratan untuk:</p>
                
                <p style="text-align: center; font-weight: bold; margin: 20px 0;">" <?= htmlspecialchars($keterangan_asli) ?> "</p>

                <p>Demikian surat <?= strtolower(htmlspecialchars($data['jenis_surat'])) ?> ini dibuat dengan sebenarnya agar dapat dipergunakan sebagaimana mestinya oleh pihak yang berkepentingan.</p>
            </div>

            <div class="ttd-box">
                <p>Desa Kosar, <?= date('d M Y', strtotime($tgl_surat)) ?></p>
                <p>Kepala Desa Kosar</p>
                <div class="ttd-space"></div>
                <p style="text-decoration: underline; font-weight: bold;">( Bpk/Ibu Kepala Desa )</p>
                <p>NIP. 19800101 201001 1 001</p>
            </div>
        </div>
    </div>

</body>
</html>
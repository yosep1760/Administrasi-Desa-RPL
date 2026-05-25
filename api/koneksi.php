<?php
// Konfigurasi Database TiDB
$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com';
$user = 'e1aHBgKkkYs5ecU.root';
$pass = 'j8fnX6U6qOYDDicd';
$db   = 'admin_desa';
$port = 4000;

// Fungsi koneksi khusus Vercel & TiDB (SSL)
$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
mysqli_real_connect($conn, $host, $user, $pass, '', $port, NULL, MYSQLI_CLIENT_SSL);

if ($conn->connect_error) {
    die("Koneksi Database Gagal: " . $conn->connect_error);
}

// 1. Buat Database otomatis jika belum ada
$conn->query("CREATE DATABASE IF NOT EXISTS $db");
$conn->select_db($db);

// 2. Buat Tabel Pengguna otomatis
$conn->query("CREATE TABLE IF NOT EXISTS pengguna (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    nama VARCHAR(100), 
    username VARCHAR(50) UNIQUE, 
    password VARCHAR(50), 
    role ENUM('warga', 'petugas', 'kades')
)");

// 3. Buat Tabel Surat otomatis
$conn->query("CREATE TABLE IF NOT EXISTS surat (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    id_warga INT,
    nik VARCHAR(20),
    jenis_surat VARCHAR(100),
    keterangan TEXT,
    status VARCHAR(50) DEFAULT 'Menunggu',
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// 4. Masukkan data awal (Akun default) otomatis
$cek = $conn->query("SELECT id FROM pengguna LIMIT 1");
if ($cek->num_rows == 0) {
    $conn->query("INSERT INTO pengguna (nama, username, password, role) VALUES 
        ('Bapak Kepala Desa', 'kades', 'admin123', 'kades'),
        ('Petugas Desa', 'petugas', 'admin123', 'petugas'),
        ('Warga Desa', 'warga', 'admin123', 'warga')");
}
?>
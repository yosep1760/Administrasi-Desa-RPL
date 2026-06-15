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
mysqli_real_connect($conn, $host, $user, $pass, $db, $port, NULL, MYSQLI_CLIENT_SSL);

if ($conn->connect_error) {
    die("Koneksi Database Gagal: " . $conn->connect_error);
}

// Catatan: Pembuatan tabel otomatis dihapus karena kita sudah menggunakan skema PDM relasional yang fix.
?>
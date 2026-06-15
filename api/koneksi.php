<?php
// Konfigurasi Database TiDB mengambil dari Environment Variables Vercel
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db   = getenv('DB_NAME');
$port = 4000;

// Fungsi koneksi khusus Vercel & TiDB (SSL)
$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
mysqli_real_connect($conn, $host, $user, $pass, $db, $port, NULL, MYSQLI_CLIENT_SSL);

if ($conn->connect_error) {
    die("Koneksi Database Gagal. Server sedang sibuk."); // Jangan tampilkan pesan error asli ke publik
}
?>
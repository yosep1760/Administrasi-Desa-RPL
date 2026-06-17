<?php
// Memanggil file rahasia (File ini TIDAK ADA di GitHub, nanti dibuat langsung di Rumahweb)
$env = require __DIR__ . '/env.php';

$host = $env['DB_HOST'];
$user = $env['DB_USER'];
$pass = $env['DB_PASS'];
$db   = $env['DB_NAME'];

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi Database Gagal. Server sedang sibuk.");
}
?>
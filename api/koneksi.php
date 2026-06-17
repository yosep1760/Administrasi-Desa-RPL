<?php
// Koneksi aman mengambil data dari env.php agar tidak bocor di GitHub
$env_path = __DIR__ . '/env.php';

if (!file_exists($env_path)) {
    die("Sistem Error: File env.php tidak ditemukan. Harap buat file env.php di server.");
}

$env = require $env_path;

$host = $env['DB_HOST'];
$user = $env['DB_USER'];
$pass = $env['DB_PASS'];
$db   = $env['DB_NAME'];

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi Database Gagal. Server sedang sibuk.");
}
?>
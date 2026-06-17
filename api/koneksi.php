<?php
$host = "localhost"; // Biasanya di Rumahweb tetap localhost
$user = "desk6523"; // Ganti dengan Username DB di cPanel
$pass = "YYxPkWh8NZKg65"; // Ganti dengan Password DB
$db   = "desk6523_admin_desa"; // Ganti dengan Nama DB di cPanel

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi Database Gagal: " . $conn->connect_error); 
}
?>
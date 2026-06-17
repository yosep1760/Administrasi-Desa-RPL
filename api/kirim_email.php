<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Memanggil file core PHPMailer dari folder yang baru saja Anda buat
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

function kirimEmail($email_tujuan, $judul_pesan, $isi_pesan_html) {
    // Panggil kredensial dari file rahasia env.php
    $env_path = __DIR__ . '/env.php';
    if (!file_exists($env_path)) return false;
    $env = require $env_path;

    $mail = new PHPMailer(true);

    try {
        // Pengaturan Server SMTP Rumahweb
        $mail->isSMTP();
        $mail->Host       = 'mail.desakosar.dpdns.org'; // Host cPanel Anda
        $mail->SMTPAuth   = true;
        $mail->Username   = $env['MAIL_USER']; // Email: sistem@desakosar.dpdns.org
        $mail->Password   = $env['MAIL_PASS']; // Password email
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL
        $mail->Port       = 465;

        // Pengirim & Penerima
        $mail->setFrom($env['MAIL_USER'], 'Sistem Desa Kosar');
        $mail->addAddress($email_tujuan);

        // Konten Email
        $mail->isHTML(true);
        $mail->Subject = $judul_pesan;
        $mail->Body    = $isi_pesan_html;

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Jika gagal, sistem tidak akan mati (error disembunyikan agar user tidak bingung)
        return false;
    }
}
?>
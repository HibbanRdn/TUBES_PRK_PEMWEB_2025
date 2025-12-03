<?php
// config/send_email.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Library PHPMailer
require_once __DIR__ . '/../PHPMailer/Exception.php';
require_once __DIR__ . '/../PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/SMTP.php';

// --- LOAD KREDENSIAL DARI FILE TERPISAH ---
// Cek apakah file secrets ada, jika tidak, stop eksekusi agar error jelas
if (!file_exists(__DIR__ . '/smtp_secrets.php')) {
    die("Error: File config/smtp_secrets.php belum dibuat. Silakan copy dari smtp_secrets.example.php");
}
require_once __DIR__ . '/smtp_secrets.php';

function sendResetEmail($recipientEmail, $recipientName, $token) {
    $mail = new PHPMailer(true);

    try {
        // --- KONFIGURASI SERVER SMTP GMAIL ---
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST; // Mengambil dari define
        $mail->SMTPAuth   = true;
        
        // MENGGUNAKAN VARIABEL DARI FILE SECRETS
        $mail->Username   = SMTP_USER; 
        $mail->Password   = SMTP_PASS; 
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = SMTP_PORT;

        // --- PENGIRIM & PENERIMA ---
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($recipientEmail, $recipientName);

        // --- URL RESET PASSWORD ---
        $baseURL = "http://localhost/TUBES_PRK_PEMWEB_2025/kelompok/kelompok_28"; 
        $resetLink = $baseURL . "/auth/reset_password.php?token=" . $token . "&email=" . urlencode($recipientEmail);
        
        // --- KONTEN EMAIL ---
        $mail->isHTML(true);
        $mail->Subject = 'Reset Password - DigiNiaga';
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; color: #333;'>
                <h3>Halo, $recipientName</h3>
                <p>Kami menerima permintaan untuk mereset password akun Owner Anda.</p>
                <p>Silakan klik tombol di bawah ini untuk membuat password baru:</p>
                <p>
                    <a href='$resetLink' style='background-color: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                        Reset Password Sekarang
                    </a>
                </p>
                <p style='margin-top: 20px; font-size: 12px; color: #666;'>
                    Link ini hanya berlaku selama 30 menit.<br>
                    Jika tombol tidak berfungsi, salin link ini ke browser: <br>
                    $resetLink
                </p>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
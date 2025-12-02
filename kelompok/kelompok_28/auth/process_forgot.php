<?php
// auth/process_forgot.php
session_start();
require_once '../config/database.php';

// Pastikan akses via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = trim($_POST['username']);

    // 1. Validasi Input Kosong
    if (empty($username)) {
        header("Location: forgot_password.php?status=empty");
        exit;
    }

    // 2. Cek apakah Username ada di Database
    $sql = "SELECT id, fullname FROM users WHERE username = ?";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 1) {
            // CASE: Username Ditemukan
            // Karena ini project offline/lokal tanpa SMTP Email server,
            // kita simulasikan sukses dan minta user hubungi admin.
            
            mysqli_stmt_bind_result($stmt, $id, $fullname);
            mysqli_stmt_fetch($stmt);

            // Redirect dengan pesan sukses
            // Kita kirim nama user di URL agar pesan lebih personal (URL Encode penting!)
            header("Location: forgot_password.php?status=success&name=" . urlencode($fullname));
            exit;

        } else {
            // CASE: Username Tidak Ditemukan
            header("Location: forgot_password.php?status=not_found");
            exit;
        }

        mysqli_stmt_close($stmt);
    } else {
        // Error Database
        header("Location: forgot_password.php?status=error");
        exit;
    }
} else {
    // Jika akses langsung ke file ini tanpa submit form
    header("Location: forgot_password.php");
    exit;
}

mysqli_close($conn);
?>
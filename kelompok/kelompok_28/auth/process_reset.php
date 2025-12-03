<?php
session_start();
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'] ?? '';
    $email = $_POST['email'] ?? '';
    $pass1 = $_POST['password'] ?? '';
    $pass2 = $_POST['conf_password'] ?? '';
    
    // 1. Validasi Input Kosong
    if (empty($token) || empty($email) || empty($pass1) || empty($pass2)) {
        header("Location: reset_password.php?token=$token&email=$email&error=empty");
        exit;
    }
    
    // 2. Validasi Password Sama
    if ($pass1 !== $pass2) {
        header("Location: reset_password.php?token=$token&email=$email&error=mismatch");
        exit;
    }
    
    // 3. Validasi Panjang Password (minimal 6 karakter)
    if (strlen($pass1) < 6) {
        header("Location: reset_password.php?token=$token&email=$email&error=short");
        exit;
    }
    
    // 4. Validasi Token Ulang (Double Check Security)
    $token_hash = hash('sha256', $token);
    $now = date("Y-m-d H:i:s");
    
    $sql = "SELECT id FROM owners WHERE email = ? AND reset_token_hash = ? AND reset_token_expires_at > ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "sss", $email, $token_hash, $now);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) == 1) {
            // 5. Update Password & Hapus Token (Supaya link tidak bisa dipakai 2x)
            $new_hash = password_hash($pass1, PASSWORD_DEFAULT);
            $sql_update = "UPDATE owners SET password = ?, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE email = ?";
            
            if ($stmt_up = mysqli_prepare($conn, $sql_update)) {
                mysqli_stmt_bind_param($stmt_up, "ss", $new_hash, $email);
                
                if (mysqli_stmt_execute($stmt_up)) {
                    // Sukses - Redirect ke login dengan pesan sukses
                    mysqli_stmt_close($stmt_up);
                    mysqli_stmt_close($stmt);
                    mysqli_close($conn);
                    
                    header("Location: login.php?reset=success");
                    exit;
                } else {
                    header("Location: reset_password.php?token=$token&email=$email&error=db");
                    exit;
                }
            }
        } else {
            // Token expired atau tidak valid
            header("Location: reset_password.php?token=$token&email=$email&error=invalid");
            exit;
        }
        
        mysqli_stmt_close($stmt);
    } else {
        header("Location: reset_password.php?token=$token&email=$email&error=db");
        exit;
    }
    
    mysqli_close($conn);
} else {
    // Jika bukan POST request
    header("Location: forgot_password.php");
    exit;
}
?>
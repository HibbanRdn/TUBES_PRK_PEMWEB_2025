<?php
// auth/process_register.php
session_start();
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Ambil & Sanitasi Data
    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // 2. Validasi Dasar
    if (empty($fullname) || empty($email) || empty($username) || empty($password)) {
        header("Location: register.php?error=empty");
        exit;
    }

    // 3. Cek Password Match
    if ($password !== $confirm) {
        header("Location: register.php?error=password_mismatch");
        exit;
    }

    // 4. Cek Apakah Username atau Email sudah dipakai di tabel OWNERS
    $sql_check = "SELECT id FROM owners WHERE username = ? OR email = ?";
    if ($stmt = mysqli_prepare($conn, $sql_check)) {
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            header("Location: register.php?error=username_taken"); // Atau email_taken
            exit;
        }
        mysqli_stmt_close($stmt);
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $sql_insert = "INSERT INTO owners (fullname, email, phone, username, password) VALUES (?, ?, ?, ?, ?)";
    
    if ($stmt = mysqli_prepare($conn, $sql_insert)) {
        mysqli_stmt_bind_param($stmt, "sssss", $fullname, $email, $phone, $username, $password_hash);
        
        if (mysqli_stmt_execute($stmt)) {
            // BERHASIL DAFTAR
            // Redirect ke Login dengan pesan sukses
            header("Location: login.php?registered=success");
            exit;
        } else {
            // Gagal
            header("Location: register.php?error=db_error");
            exit;
        }
        mysqli_stmt_close($stmt);
    } else {
        header("Location: register.php?error=db_error");
        exit;
    }

} else {
    header("Location: register.php");
    exit;
}

mysqli_close($conn);
?>
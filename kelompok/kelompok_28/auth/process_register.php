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
    // (Kita menggunakan Prepared Statement untuk keamanan)
    $sql_check = "SELECT id FROM owners WHERE username = ? OR email = ?";
    if ($stmt = mysqli_prepare($conn, $sql_check)) {
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            // Cek detail mana yang duplikat (opsional, tapi bagus untuk UX)
            // Untuk simplifikasi, kita asumsi username dulu
            header("Location: register.php?error=username_taken"); // Atau email_taken
            exit;
        }
        mysqli_stmt_close($stmt);
    }

    // 5. Insert Data Baru
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Query Insert (id auto increment)
    $sql_insert = "INSERT INTO owners (fullname, email, phone, username, password) VALUES (?, ?, ?, ?, ?)";
    
    if ($stmt = mysqli_prepare($conn, $sql_insert)) {
        mysqli_stmt_bind_param($stmt, "sssss", $fullname, $email, $phone, $username, $password_hash);
        
        if (mysqli_stmt_execute($stmt)) {
            // BERHASIL DAFTAR
            // Redirect ke Login dengan pesan sukses
            header("Location: login.php?registered=success");
            exit;
        } else {
            // Gagal Eksekusi
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
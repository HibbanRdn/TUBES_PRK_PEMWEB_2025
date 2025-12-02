<?php
// Mulai session (Wajib di baris paling atas)
session_start();

// Panggil file koneksi database
require_once '../config/database.php';

// Cek apakah form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari form
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validasi kosong
    if (empty($username) || empty($password)) {
        header("Location: login.php?error=empty");
        exit;
    }

    // QUERY KE DATABASE MENGGUNAKAN PREPARED STATEMENT (Security: Anti SQL Injection)
    $sql = "SELECT id, username, password, role, fullname FROM users WHERE username = ?";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind parameter (s = string)
        mysqli_stmt_bind_param($stmt, "s", $username);
        
        // Eksekusi query
        mysqli_stmt_execute($stmt);
        
        // Simpan hasil
        mysqli_stmt_store_result($stmt);
        
        // Cek jika user ditemukan (jumlah baris > 0)
        if (mysqli_stmt_num_rows($stmt) == 1) {
            // Bind hasil ke variabel
            mysqli_stmt_bind_result($stmt, $id, $db_username, $db_password, $role, $fullname);
            mysqli_stmt_fetch($stmt);

            // VERIFIKASI PASSWORD
            // Kita pakai password_verify() untuk mencocokkan input dengan Hash di DB
            if (password_verify($password, $db_password)) {
                
                // Jika password benar, set SESSION
                $_SESSION['loggedin'] = true;
                $_SESSION['id'] = $id;
                $_SESSION['username'] = $db_username;
                $_SESSION['role'] = $role;     // Penting untuk hak akses nanti
                $_SESSION['fullname'] = $fullname;

                // Redirect ke Dashboard (Folder pages)
                header("Location: ../pages/dashboard.php");
                exit;

            } else {
                // Password salah
                header("Location: login.php?error=invalid");
                exit;
            }
        } else {
            // Username tidak ditemukan
            header("Location: login.php?error=invalid");
            exit;
        }

        // Tutup statement
        mysqli_stmt_close($stmt);
    }
}

// Tutup koneksi
mysqli_close($conn);
?>
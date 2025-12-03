<?php
// Mulai session
session_start();

// Panggil file koneksi database
require_once '../config/database.php';

// Cek apakah form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil dan bersihkan data dari form
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validasi kosong
    if (empty($username) || empty($password)) {
        header("Location: login.php?error=empty");
        exit;
    }

    // --- TAHAP 1: CEK TABEL OWNERS ---
    $sql_owner = "SELECT id, username, password, fullname FROM owners WHERE username = ?";
    
    if ($stmt = mysqli_prepare($conn, $sql_owner)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        // Jika ditemukan di tabel owners
        if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $id, $db_username, $db_password, $fullname);
            mysqli_stmt_fetch($stmt);

            if (password_verify($password, $db_password)) {
                // Set Session Owner
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $db_username;
                $_SESSION['role'] = 'owner'; // Role manual karena owner tidak punya kolom role
                $_SESSION['fullname'] = $fullname;
                
                // Owner mungkin punya banyak toko, store_id biasanya dipilih nanti di dashboard
                // atau ambil toko pertama jika single-store:
                // $_SESSION['store_id'] = ... (opsional)

                header("Location: ../redirect.php");
                exit;
            } else {
                // Password salah (untuk owner)
                header("Location: login.php?error=invalid");
                exit;
            }
        }
        mysqli_stmt_close($stmt);
    }

    // --- TAHAP 2: JIKA BUKAN OWNER, CEK TABEL EMPLOYEES ---
    $sql_employee = "SELECT id, store_id, username, password, role, fullname, is_active FROM employees WHERE username = ?";
    
    if ($stmt = mysqli_prepare($conn, $sql_employee)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        // Jika ditemukan di tabel employees
        if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $id, $store_id, $db_username, $db_password, $role, $fullname, $is_active);
            mysqli_stmt_fetch($stmt);

            // Cek apakah akun karyawan aktif
            if ($is_active == 0) {
                header("Location: login.php?error=suspended");
                exit;
            }

            if (password_verify($password, $db_password)) {
                // Set Session Employee
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $id;
                $_SESSION['store_id'] = $store_id; // Karyawan terikat toko tertentu
                $_SESSION['username'] = $db_username;
                $_SESSION['role'] = $role; // 'admin_gudang' atau 'kasir'
                $_SESSION['fullname'] = $fullname;

                header("Location: ../pages/dashboard.php");
                exit;
            } else {
                header("Location: login.php?error=invalid");
                exit;
            }
        } else {
            // Username tidak ditemukan di Owner maupun Employee
            header("Location: login.php?error=invalid");
            exit;
        }
        mysqli_stmt_close($stmt);
    }
}

// Tutup koneksi
mysqli_close($conn);
?>
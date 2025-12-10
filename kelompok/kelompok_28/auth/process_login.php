<?php
session_start();
require_once '../config/database.php';

// Cek apakah form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validasi 
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
        
        if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $id, $db_username, $db_password, $fullname);
            mysqli_stmt_fetch($stmt);

            if (password_verify($password, $db_password)) {
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $db_username;
                $_SESSION['role'] = 'owner'; 
                $_SESSION['fullname'] = $fullname;
                
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
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $id;
                $_SESSION['store_id'] = $store_id; 
                $_SESSION['username'] = $db_username;
                $_SESSION['role'] = $role; 
                $_SESSION['fullname'] = $fullname;

                header("Location: ../redirect.php");
                exit;
            } else {
                header("Location: login.php?error=invalid");
                exit;
            }
        } else {
            header("Location: login.php?error=invalid");
            exit;
        }
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?>
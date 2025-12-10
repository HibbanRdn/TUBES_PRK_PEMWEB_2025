<?php
// FILE: process_users.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'owner') {
    header("Location: ../../auth/login.php");
    exit;
}

$owner_id = $_SESSION['user_id'];
$message = "";

// 1. Ambil Data Toko Punya Owner
$sql_store = "SELECT id, name FROM stores WHERE owner_id = ? LIMIT 1";
$stmt_store = mysqli_prepare($conn, $sql_store);
mysqli_stmt_bind_param($stmt_store, "i", $owner_id);
mysqli_stmt_execute($stmt_store);
$res_store = mysqli_stmt_get_result($stmt_store);
$store = mysqli_fetch_assoc($res_store);

if (!$store) {
    die("Error: Anda belum memiliki toko.");
}
$store_id = $store['id'];

// LOGIKA EDIT: Cek apakah sedang mode edit
$edit_mode = false;
$edit_data = null;

if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    // Ambil data karyawan spesifik (Pastikan milik toko owner ini agar aman)
    $sql_edit = "SELECT * FROM employees WHERE id = ? AND store_id = ?";
    $stmt_edit = mysqli_prepare($conn, $sql_edit);
    mysqli_stmt_bind_param($stmt_edit, "ii", $edit_id, $store_id);
    mysqli_stmt_execute($stmt_edit);
    $result_edit = mysqli_stmt_get_result($stmt_edit);
    
    if ($result_edit && mysqli_num_rows($result_edit) > 0) {
        $edit_mode = true;
        $edit_data = mysqli_fetch_assoc($result_edit);
    }
}

// 3. Handle Form Submission (Tambah ATAU Update)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action   = $_POST['action'];
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $role     = $_POST['role'];
    $password = $_POST['password']; // Bisa kosong jika mode edit

    // A. ADD USER
    if ($action == 'add') {
        if (!empty($fullname) && !empty($username) && !empty($password)) {
            // Cek username kembar di toko ini
            $check = mysqli_query($conn, "SELECT id FROM employees WHERE username = '$username' AND store_id = '$store_id'");
            if (mysqli_num_rows($check) > 0) {
                $message = "<div class='bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 animate-slideDown'><div class='flex items-center gap-3'><svg class='w-5 h-5' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z' clip-rule='evenodd'></path></svg><span class='font-semibold'>Username sudah digunakan!</span></div></div>";
            } else {
                $hash_pass = password_hash($password, PASSWORD_DEFAULT);
                $sql_insert = "INSERT INTO employees (store_id, fullname, username, password, role, is_active) VALUES (?, ?, ?, ?, ?, 1)";
                if ($stmt = mysqli_prepare($conn, $sql_insert)) {
                    mysqli_stmt_bind_param($stmt, "issss", $store_id, $fullname, $username, $hash_pass, $role);
                    if (mysqli_stmt_execute($stmt)) {
                        $message = "<div class='bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 animate-slideDown'><div class='flex items-center gap-3'><svg class='w-5 h-5' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z' clip-rule='evenodd'></path></svg><span class='font-semibold'>Karyawan berhasil ditambahkan!</span></div></div>";
                    }
                }
            }
        }
    }
    // B. UPDATE USER
    elseif ($action == 'update') {
        $emp_id = $_POST['emp_id'];
        
        // Cek apakah password diisi (Reset Password) atau kosong (Tetap password lama)
        if (!empty($password)) {
            // Update dengan Password Baru
            $hash_pass = password_hash($password, PASSWORD_DEFAULT);
            $sql_update = "UPDATE employees SET fullname=?, username=?, role=?, password=? WHERE id=? AND store_id=?";
            $stmt = mysqli_prepare($conn, $sql_update);
            mysqli_stmt_bind_param($stmt, "ssssii", $fullname, $username, $role, $hash_pass, $emp_id, $store_id);
        } else {
            // Update Tanpa Ganti Password
            $sql_update = "UPDATE employees SET fullname=?, username=?, role=? WHERE id=? AND store_id=?";
            $stmt = mysqli_prepare($conn, $sql_update);
            mysqli_stmt_bind_param($stmt, "sssii", $fullname, $username, $role, $emp_id, $store_id);
        }

        if (mysqli_stmt_execute($stmt)) {
            // Redirect agar form kembali bersih / mode edit hilang
            header("Location: users.php?msg=updated");
            exit;
        } else {
            $message = "<div class='bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6'><span class='font-semibold'>Gagal mengupdate data.</span></div>";
        }
    }
}

// Menangkap pesan sukses dari redirect update
if (isset($_GET['msg']) && $_GET['msg'] == 'updated') {
    $message = "<div class='bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded-lg mb-6 animate-slideDown'><div class='flex items-center gap-3'><svg class='w-5 h-5' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z' clip-rule='evenodd'></path></svg><span class='font-semibold'>Data karyawan berhasil diperbarui!</span></div></div>";
}

// 4. Handle Delete
if (isset($_GET['delete'])) {
    $emp_id = $_GET['delete'];
    $sql_del = "DELETE FROM employees WHERE id = ? AND store_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql_del)) {
        mysqli_stmt_bind_param($stmt, "ii", $emp_id, $store_id);
        mysqli_stmt_execute($stmt);
        header("Location: users.php");
        exit;
    }
}

// 5. Ambil Daftar Karyawan
$sql_employees = "SELECT * FROM employees WHERE store_id = ? ORDER BY created_at DESC";
$stmt_emp = mysqli_prepare($conn, $sql_employees);
mysqli_stmt_bind_param($stmt_emp, "i", $store_id);
mysqli_stmt_execute($stmt_emp);
$employees = mysqli_stmt_get_result($stmt_emp);

// Hitung statistik role (Looping pertama)
$total_kasir = 0;
$total_gudang = 0;
while($row = mysqli_fetch_assoc($employees)) {
    if($row['role'] == 'kasir') $total_kasir++;
    else $total_gudang++;
}
mysqli_data_seek($employees, 0);
?>
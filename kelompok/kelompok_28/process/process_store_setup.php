<?php
// FILE: process_store_setup.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../../config/database.php';

// 1. Cek Login & Role Owner
// Pastikan user yang mengakses adalah owner yang sah
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'owner') {
    header("Location: ../../auth/login.php");
    exit;
}

$owner_id = $_SESSION['user_id'];
$fullname = $_SESSION['fullname']; // Variabel ini akan dipakai di Frontend

// 2. Cek Apakah Sudah Punya Toko
// Jika sudah punya, redirect paksa ke dashboard (Mencegah double store)
$sql_check = "SELECT id FROM stores WHERE owner_id = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql_check);
mysqli_stmt_bind_param($stmt, "i", $owner_id);
mysqli_stmt_execute($stmt);
if (mysqli_num_rows(mysqli_stmt_get_result($stmt)) > 0) {
    header("Location: dashboard.php");
    exit;
}

$message = ""; // Variabel pesan untuk Frontend

// 3. Proses Form Submit (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $store_name = trim($_POST['name']);
    $phone      = trim($_POST['phone']);
    $address    = trim($_POST['address']);
    $category   = trim($_POST['category']); 

    // Validasi Input
    if (empty($store_name) || empty($phone) || empty($address) || empty($category)) {
        // Pesan Error HTML
        $message = "<div class='flex items-center p-4 mb-6 text-sm text-red-800 border border-red-300 rounded-xl bg-red-50 animate-fadeInUp shadow-sm' role='alert'>
                        <svg class='flex-shrink-0 inline w-5 h-5 me-3' fill='currentColor' viewBox='0 0 20 20'><path d='M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z'/></svg>
                        <span class='font-semibold'>Mohon lengkapi semua data usaha Anda.</span>
                    </div>";
    } else {
        // Insert ke Database
        $sql_insert = "INSERT INTO stores (owner_id, name, phone, address, category) VALUES (?, ?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($conn, $sql_insert)) {
            mysqli_stmt_bind_param($stmt, "issss", $owner_id, $store_name, $phone, $address, $category);
            
            if (mysqli_stmt_execute($stmt)) {
                // Sukses: Redirect ke Dashboard dengan parameter sukses
                header("Location: dashboard.php?setup=success");
                exit;
            } else {
                // Gagal Query
                $message = "<div class='flex items-center p-4 mb-6 text-sm text-red-800 border border-red-300 rounded-xl bg-red-50 animate-fadeInUp shadow-sm'>Terjadi kesalahan sistem: " . mysqli_error($conn) . "</div>";
            }
        }
    }
}
?>
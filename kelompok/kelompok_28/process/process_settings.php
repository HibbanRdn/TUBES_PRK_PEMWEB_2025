<?php
// FILE: process_settings.php

session_start();
require_once '../../config/database.php';

// 1. Cek Keamanan: Apakah User adalah Owner?
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'owner') {
    header("Location: ../../auth/login.php");
    exit;
}

$owner_id = $_SESSION['user_id'];
$fullname = $_SESSION['fullname']; // Ambil nama dari session untuk navbar
$message = ""; 

// ---------------------------------------------------------
// LOGIKA PENYIMPANAN DATA (HANDLE POST REQUEST)
// ---------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // A. UPDATE PROFIL OWNER
    if (isset($_POST['action']) && $_POST['action'] == 'update_profile') {
        $fullname_post = trim($_POST['fullname']);
        $phone    = trim($_POST['phone']);
        
        if (!empty($fullname_post)) {
            $sql_update = "UPDATE owners SET fullname = ?, phone = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql_update);
            mysqli_stmt_bind_param($stmt, "ssi", $fullname_post, $phone, $owner_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['fullname'] = $fullname_post; // Update session nama
                $message = "success_profile";
                $fullname = $fullname_post; // Update variabel nama untuk tampilan saat ini
            } else {
                $message = "error_db";
            }
        } else {
            $message = "error_empty";
        }
    }

    // B. UPDATE DATA TOKO
    elseif (isset($_POST['action']) && $_POST['action'] == 'update_store') {
        $store_name    = trim($_POST['store_name']);
        $store_phone   = trim($_POST['store_phone']);
        $store_address = trim($_POST['store_address']);
        
        if (!empty($store_name) && !empty($store_address)) {
            $sql_update = "UPDATE stores SET name = ?, phone = ?, address = ? WHERE owner_id = ?";
            $stmt = mysqli_prepare($conn, $sql_update);
            mysqli_stmt_bind_param($stmt, "sssi", $store_name, $store_phone, $store_address, $owner_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $message = "success_store";
            } else {
                $message = "error_db";
            }
        } else {
            $message = "error_empty";
        }
    }
    
    // Redirect ke settings.php (Frontend) untuk mencegah resubmit
    if (!empty($message)) {
        header("Location: settings.php?msg=" . $message);
        exit;
    }
}

// ---------------------------------------------------------
// AMBIL DATA DARI DATABASE (Untuk ditampilkan di Form)
// ---------------------------------------------------------
// 1. Ambil Data Owner
$sql_owner = "SELECT fullname, email, phone, username FROM owners WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql_owner);
mysqli_stmt_bind_param($stmt, "i", $owner_id);
mysqli_stmt_execute($stmt);
$owner = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// 2. Ambil Data Toko
$sql_store = "SELECT name, address, phone FROM stores WHERE owner_id = ?";
$stmt2 = mysqli_prepare($conn, $sql_store);
mysqli_stmt_bind_param($stmt2, "i", $owner_id);
mysqli_stmt_execute($stmt2);
$store = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2));

// Handle jika toko belum ada (untuk menghindari error undefined index di FE)
if (!$store) {
    $store = ['name' => '', 'address' => '', 'phone' => ''];
}
?>
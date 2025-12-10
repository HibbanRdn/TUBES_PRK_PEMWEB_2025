<?php
// FILE: process_owner.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../../config/database.php';

// 1. Cek Login & Role Owner
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'owner') {
    header("Location: ../../auth/login.php");
    exit;
}

$fullname = $_SESSION['fullname'];
$owner_id = $_SESSION['user_id'];

// 2. Ambil Store ID milik Owner
$sql_store = "SELECT id, name, address FROM stores WHERE owner_id = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql_store);
mysqli_stmt_bind_param($stmt, "i", $owner_id);
mysqli_stmt_execute($stmt);
$res_store = mysqli_stmt_get_result($stmt);
$store = mysqli_fetch_assoc($res_store);

// Default values (Persiapan variabel agar tidak error di FE jika null)
$store_id = $store['id'] ?? 0;
$store_name = $store['name'] ?? 'Nama Toko';     
$store_address = $store['address'] ?? 'Alamat';  
$has_store = ($store_id > 0); 

// Inisialisasi variabel statistik
$omzet_today = 0;
$trx_count = 0;
$low_stock = 0;
$recent_trx = [];     
$chart_labels = [];   
$chart_data = [];     

// 3. Hanya Jalankan Query Berat JIKA Toko Sudah Ada
if ($has_store) {
    // A. Hitung Omzet & Transaksi Hari Ini
    $today = date('Y-m-d');
    $sql_today = "SELECT SUM(total_price) as omzet, COUNT(id) as total_trx 
                  FROM transactions 
                  WHERE store_id = ? AND DATE(date) = ?";
    $stmt = mysqli_prepare($conn, $sql_today);
    mysqli_stmt_bind_param($stmt, "is", $store_id, $today);
    mysqli_stmt_execute($stmt);
    $res_today = mysqli_stmt_get_result($stmt);
    $data_today = mysqli_fetch_assoc($res_today);
    
    $omzet_today = $data_today['omzet'] ?? 0;
    $trx_count = $data_today['total_trx'] ?? 0;

    // B. Hitung Stok Menipis (Stok di bawah 5)
    $sql_stock = "SELECT COUNT(id) as low_count FROM products WHERE store_id = ? AND stock < 5";
    $stmt = mysqli_prepare($conn, $sql_stock);
    mysqli_stmt_bind_param($stmt, "i", $store_id);
    mysqli_stmt_execute($stmt);
    $low_stock = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['low_count'];

    // C. Ambil 5 Transaksi Terakhir (Join dengan tabel employees untuk nama kasir)
    $sql_recent = "SELECT t.invoice_code, t.date, t.total_price, e.fullname as kasir 
                   FROM transactions t
                   JOIN employees e ON t.employee_id = e.id
                   WHERE t.store_id = ? 
                   ORDER BY t.date DESC LIMIT 5";
    $stmt = mysqli_prepare($conn, $sql_recent);
    mysqli_stmt_bind_param($stmt, "i", $store_id);
    mysqli_stmt_execute($stmt);
    $recent_trx = mysqli_stmt_get_result($stmt);

    // D. Data Grafik (Looping mundur 7 Hari Terakhir)
    for ($i = 6; $i >= 0; $i--) {
        $date_loop = date('Y-m-d', strtotime("-$i days"));
        $day_name = date('D', strtotime($date_loop));
        
        $sql_chart = "SELECT SUM(total_price) as total FROM transactions WHERE store_id = ? AND DATE(date) = ?";
        $stmt = mysqli_prepare($conn, $sql_chart);
        mysqli_stmt_bind_param($stmt, "is", $store_id, $date_loop);
        mysqli_stmt_execute($stmt);
        $res_chart = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        
        // Konversi nama hari ke Indonesia
        $days_indo = ['Sun'=>'Min', 'Mon'=>'Sen', 'Tue'=>'Sel', 'Wed'=>'Rab', 'Thu'=>'Kam', 'Fri'=>'Jum', 'Sat'=>'Sab'];
        $chart_labels[] = $days_indo[$day_name];
        $chart_data[] = $res_chart['total'] ?? 0;
    }
}
?>
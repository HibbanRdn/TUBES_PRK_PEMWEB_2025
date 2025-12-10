<?php
// FILE: process_reports.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../../config/database.php';

// 1. Cek Login & Role
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'owner') {
    header("Location: ../../auth/login.php");
    exit;
}

$fullname = $_SESSION['fullname'];
$owner_id = $_SESSION['user_id'];

// 2. Ambil Data Toko (Lengkap dengan Nama & Alamat untuk Kop Surat)
$sql_store = "SELECT id, name, address, phone FROM stores WHERE owner_id = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql_store);
mysqli_stmt_bind_param($stmt, "i", $owner_id);
mysqli_stmt_execute($stmt);
$store = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Default values untuk menghindari error undefined variable di FE
$store_id = $store['id'] ?? 0;
$store_name = $store['name'] ?? 'Nama Toko';
$store_address = $store['address'] ?? 'Alamat Toko';
$store_phone = $store['phone'] ?? '-';

// 3. LOGIKA FILTER PERIODE
$period = $_GET['period'] ?? '7';
$end_date = date('Y-m-d');

if ($period == '30') {
    $start_date = date('Y-m-d', strtotime('-30 days'));
    $label_period = "30 Hari Terakhir";
} elseif ($period == 'month') {
    $start_date = date('Y-m-01');
    $end_date   = date('Y-m-t');
    $label_period = "Bulan Ini";
} else {
    $start_date = date('Y-m-d', strtotime('-6 days'));
    $label_period = "7 Hari Terakhir";
}

$formatted_period = date('d M Y', strtotime($start_date)) . ' - ' . date('d M Y', strtotime($end_date));

// Inisialisasi Data Default
$total_revenue = 0;
$total_trx = 0;
$avg_daily = 0;
$trend_labels = [];
$trend_data = [];
$cat_labels = [];
$cat_data = [];
$top_products = null; 

if ($store_id > 0) {
    // A. RINGKASAN TOTAL
    $sql_summary = "SELECT SUM(total_price) as revenue, COUNT(id) as trx_count 
                    FROM transactions 
                    WHERE store_id = ? AND DATE(date) BETWEEN ? AND ?";
    $stmt = mysqli_prepare($conn, $sql_summary);
    mysqli_stmt_bind_param($stmt, "iss", $store_id, $start_date, $end_date);
    mysqli_stmt_execute($stmt);
    $summary = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    
    $total_revenue = $summary['revenue'] ?? 0;
    $total_trx = $summary['trx_count'] ?? 0;
    
    // Hitung durasi dan rata-rata harian
    $date1 = new DateTime($start_date);
    $date2 = new DateTime($end_date);
    $interval = $date1->diff($date2)->days + 1;
    $avg_daily = ($interval > 0) ? $total_revenue / $interval : 0;

    // B. DATA GRAFIK TREN (Looping per hari)
    $current = strtotime($start_date);
    $end = strtotime($end_date);
    while ($current <= $end) {
        $date_loop = date('Y-m-d', $current);
        $trend_labels[] = date('d M', $current);
        
        $sql_day = "SELECT SUM(total_price) as total FROM transactions WHERE store_id = ? AND DATE(date) = ?";
        $stmt_day = mysqli_prepare($conn, $sql_day);
        mysqli_stmt_bind_param($stmt_day, "is", $store_id, $date_loop);
        mysqli_stmt_execute($stmt_day);
        $res_day = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_day));
        $trend_data[] = $res_day['total'] ?? 0;
        
        $current = strtotime('+1 day', $current);
    }

    // C. DATA KATEGORI
    $sql_cat = "SELECT c.name, SUM(td.subtotal) as total
                FROM transaction_details td
                JOIN products p ON td.product_id = p.id
                JOIN categories c ON p.category_id = c.id
                JOIN transactions t ON td.transaction_id = t.id
                WHERE t.store_id = ? AND DATE(t.date) BETWEEN ? AND ?
                GROUP BY c.name";
    $stmt_cat = mysqli_prepare($conn, $sql_cat);
    mysqli_stmt_bind_param($stmt_cat, "iss", $store_id, $start_date, $end_date);
    mysqli_stmt_execute($stmt_cat);
    $res_cat = mysqli_stmt_get_result($stmt_cat);
    
    while($row = mysqli_fetch_assoc($res_cat)) {
        $cat_labels[] = $row['name'];
        $cat_data[] = $row['total'];
    }

    // D. TOP PRODUK
    $sql_top = "SELECT p.name, SUM(td.qty) as sold, SUM(td.subtotal) as revenue
                FROM transaction_details td
                JOIN products p ON td.product_id = p.id
                JOIN transactions t ON td.transaction_id = t.id
                WHERE t.store_id = ? AND DATE(t.date) BETWEEN ? AND ?
                GROUP BY p.name
                ORDER BY sold DESC LIMIT 5";
    $stmt_top = mysqli_prepare($conn, $sql_top);
    mysqli_stmt_bind_param($stmt_top, "iss", $store_id, $start_date, $end_date);
    mysqli_stmt_execute($stmt_top);
    $top_products = mysqli_stmt_get_result($stmt_top);
}
?>
<?php
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

// Inisialisasi Data
$total_revenue = 0;
$total_trx = 0;
$avg_daily = 0;
$trend_labels = [];
$trend_data = [];
$cat_labels = [];
$cat_data = [];
$top_products = [];

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
    
    // Hitung durasi
    $date1 = new DateTime($start_date);
    $date2 = new DateTime($end_date);
    $interval = $date1->diff($date2)->days + 1;
    $avg_daily = ($interval > 0) ? $total_revenue / $interval : 0;

    // B. DATA GRAFIK TREN
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

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - DigiNiaga</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
        }
        
        .pdf-only { display: none; }
        
        .animate-fadeIn { 
            animation: fadeIn 0.6s ease-out; 
        }
        
        .animate-slideUp {
            animation: slideUp 0.5s ease-out;
        }
        
        .animate-scaleIn {
            animation: scaleIn 0.4s ease-out;
        }
        
        @keyframes fadeIn { 
            from { opacity: 0; transform: translateY(20px); } 
            to { opacity: 1; transform: translateY(0); } 
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }
        
        .card-gradient {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
        }
        
        .stat-card {
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            opacity: 0.1;
            transform: translate(40%, -40%);
        }
        
        .stat-card.revenue::before {
            background: linear-gradient(135deg, #10b981, #059669);
        }
        
        .stat-card.transaction::before {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
        }
        
        .stat-card.average::before {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        }
        
        .chart-container {
            position: relative;
            background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
        }
        
        .rank-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .progress-bar {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            transition: width 1s ease-out;
        }
    </style>
</head>
<body class="min-h-screen pb-12">

    <!-- Decorative Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-50 rounded-full filter blur-3xl opacity-30"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-50 rounded-full filter blur-3xl opacity-30"></div>
    </div>

    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-50 shadow-sm" data-html2canvas-ignore="true">
        <div class="flex items-center gap-3 animate-fadeIn">
            <a href="dashboard.php" class="group flex items-center gap-2 text-gray-600 hover:text-blue-600 transition-all duration-300">
                <div class="w-10 h-10 bg-gray-100 group-hover:bg-blue-50 rounded-xl flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </div>
            </a>
            <div class="h-8 w-px bg-gray-200 mx-2"></div>
            <div>
                <h1 class="font-bold text-gray-800 text-lg">Laporan Penjualan</h1>
                <p class="text-xs text-gray-500">Analisis Performa Bisnis</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="hidden sm:block text-right">
                <p class="text-xs text-gray-400">Logged in as</p>
                <p class="text-sm font-bold text-gray-700"><?= htmlspecialchars($fullname) ?></p>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto mt-8 px-6 relative z-10" data-html2canvas-ignore="true">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4 animate-slideUp">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div>
                        <h2 class="text-3xl font-extrabold text-gray-800">Analisis Performa</h2>
                        <p class="text-gray-500 text-sm mt-1">Laporan detail keuangan toko Anda</p>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <form action="" method="GET" class="relative">
                    <select name="period" onchange="this.form.submit()" class="appearance-none bg-white border-2 border-gray-200 text-gray-700 py-3 pl-5 pr-12 rounded-xl shadow-sm hover:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-bold cursor-pointer transition-all">
                        <option value="7" <?= $period == '7' ? 'selected' : '' ?>>7 Hari Terakhir</option>
                        <option value="30" <?= $period == '30' ? 'selected' : '' ?>>30 Hari Terakhir</option>
                        <option value="month" <?= $period == 'month' ? 'selected' : '' ?>>Bulan Ini</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </form>

                <button onclick="exportPDF()" class="group relative bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 flex items-center gap-2">
                    <svg class="w-5 h-5 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export PDF
                </button>
            </div>
        </div>
    </div>

    <div id="report-content" class="max-w-7xl mx-auto px-6 relative z-10">
        
        <!-- PDF Header -->
        <div id="pdf-header" class="pdf-only mb-8 border-b-4 border-gradient-to-r from-blue-600 to-purple-600 pb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-4xl font-extrabold text-gray-900 uppercase tracking-wide mb-2"><?= htmlspecialchars($store_name) ?></h1>
                    <p class="text-sm text-gray-600 flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                        <?= htmlspecialchars($store_address) ?>
                    </p>
                    <p class="text-sm text-gray-600 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path></svg>
                        <?= htmlspecialchars($store_phone) ?>
                    </p>
                </div>
                <div class="text-right bg-gradient-to-br from-blue-50 to-purple-50 p-4 rounded-2xl">
                    <h2 class="text-2xl font-extrabold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">LAPORAN PENJUALAN</h2>
                    <p class="text-sm text-gray-600 mt-2 font-semibold">Periode: <?= $formatted_period ?></p>
                    <p class="text-xs text-gray-400 mt-1">Dicetak: <?= date('d M Y H:i') ?> WIB</p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="stat-card revenue card-gradient p-8 rounded-3xl border border-gray-100 shadow-lg hover-lift animate-scaleIn">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-green-400 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="text-xs font-bold text-green-600 bg-green-50 px-3 py-1 rounded-full">
                            +12.5%
                        </div>
                    </div>
                    <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-2">Total Pendapatan</p>
                    <h3 class="text-3xl font-extrabold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent mb-3">
                        Rp <?= number_format($total_revenue, 0, ',', '.') ?>
                    </h3>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 75%; background: linear-gradient(90deg, #10b981, #059669);"></div>
                    </div>
                </div>
            </div>

            <div class="stat-card transaction card-gradient p-8 rounded-3xl border border-gray-100 shadow-lg hover-lift animate-scaleIn" style="animation-delay: 0.1s;">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <div class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">
                            Active
                        </div>
                    </div>
                    <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-2">Total Transaksi</p>
                    <h3 class="text-3xl font-extrabold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent mb-3">
                        <?= number_format($total_trx) ?>
                    </h3>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 60%; background: linear-gradient(90deg, #3b82f6, #06b6d4);"></div>
                    </div>
                </div>
            </div>

            <div class="stat-card average card-gradient p-8 rounded-3xl border border-gray-100 shadow-lg hover-lift animate-scaleIn" style="animation-delay: 0.2s;">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-400 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <div class="text-xs font-bold text-purple-600 bg-purple-50 px-3 py-1 rounded-full">
                            Daily
                        </div>
                    </div>
                    <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-2">Rata-rata per Hari</p>
                    <h3 class="text-3xl font-extrabold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-3">
                        Rp <?= number_format($avg_daily, 0, ',', '.') ?>
                    </h3>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 85%; background: linear-gradient(90deg, #8b5cf6, #ec4899);"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trend Chart -->
        <div class="card-gradient p-8 rounded-3xl border border-gray-100 shadow-lg mb-8 break-inside-avoid animate-slideUp">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-xl font-extrabold text-gray-800 flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                        </div>
                        Tren Pendapatan
                    </h3>
                    <p class="text-xs text-gray-500 mt-1 ml-13">Grafik perkembangan penjualan</p>
                </div>
                <div class="flex items-center gap-2 text-xs font-semibold text-gray-500">
                    <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                    <span>Revenue</span>
                </div>
            </div>
            <div class="chart-container relative rounded-2xl p-6">
                <canvas id="trendChart" style="height: 300px;"></canvas>
            </div>
        </div>

        <!-- Category & Top Products -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            
            <!-- Category Chart -->
            <div class="card-gradient p-8 rounded-3xl border border-gray-100 shadow-lg break-inside-avoid animate-scaleIn">
                <div class="mb-6">
                    <h3 class="text-xl font-extrabold text-gray-800 flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        </div>
                        Penjualan per Kategori
                    </h3>
                    <p class="text-xs text-gray-500 mt-1 ml-13">Distribusi penjualan berdasarkan kategori</p>
                </div>
                <div class="chart-container relative rounded-2xl p-6 mb-6">
                    <canvas id="categoryChart" style="height: 250px;"></canvas>
                </div>
                <div class="space-y-3">
                    <?php 
                    $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
                    foreach($cat_labels as $index => $cat): 
                        $val = $cat_data[$index];
                        $color = $colors[$index % count($colors)];
                        $percentage = $total_revenue > 0 ? ($val / $total_revenue) * 100 : 0;
                    ?>
                    <div class="bg-white p-4 rounded-xl border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-center mb-2">
                            <div class="flex items-center gap-3">
                                <span class="w-4 h-4 rounded-lg shadow-sm" style="background-color: <?= $color ?>"></span>
                                <span class="text-sm font-bold text-gray-700"><?= htmlspecialchars($cat) ?></span>
                            </div>
                            <span class="text-xs font-bold text-gray-400"><?= number_format($percentage, 1) ?>%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="flex-1 mr-4">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?= $percentage ?>%; background: <?= $color ?>;"></div>
                                </div>
                            </div>
                            <span class="text-sm font-extrabold text-gray-800">Rp <?= number_format($val, 0, ',', '.') ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Top Products -->
            <div class="card-gradient p-8 rounded-3xl border border-gray-100 shadow-lg break-inside-avoid animate-scaleIn" style="animation-delay: 0.1s;">
                <div class="mb-6">
                    <h3 class="text-xl font-extrabold text-gray-800 flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-teal-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                        </div>
                        Top 5 Produk Terlaris
                    </h3>
                    <p class="text-xs text-gray-500 mt-1 ml-13">Produk dengan penjualan tertinggi</p>
                </div>
                <div class="space-y-4">
                    <?php 
                    $rank = 1;
                    $rankColors = [
                        'from-yellow-400 to-orange-500',
                        'from-gray-300 to-gray-400', 
                        'from-orange-300 to-orange-400',
                        'from-blue-400 to-blue-500',
                        'from-purple-400 to-purple-500'
                    ];
                    if ($top_products && mysqli_num_rows($top_products) > 0):
                        while($prod = mysqli_fetch_assoc($top_products)): 
                            $gradientColor = $rankColors[$rank - 1];
                    ?>
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="rank-badge w-12 h-12 bg-gradient-to-br <?= $gradientColor ?> rounded-2xl text-white flex items-center justify-center font-extrabold text-lg shadow-lg">
                                    <?= $rank++ ?>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-gray-800 text-base mb-1"><?= htmlspecialchars($prod['name']) ?></h4>
                                    <div class="flex items-center gap-3">
                                        <p class="text-xs text-gray-500 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path></svg>
                                            <span class="font-bold"><?= $prod['sold'] ?></span> terjual
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="block text-xl font-extrabold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                                    Rp <?= number_format($prod['revenue'], 0, ',', '.') ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; else: ?>
                        <div class="text-center py-12">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            </div>
                            <p class="text-gray-400 text-sm font-medium">Data tidak tersedia</p>
                            <p class="text-gray-300 text-xs mt-1">Belum ada produk terjual dalam periode ini</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- PDF Footer -->
        <div id="pdf-footer" class="pdf-only mt-12 pt-6 border-t-2 border-gray-200">
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 p-6 rounded-2xl">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-bold text-gray-700">
                            Laporan ini dibuat otomatis oleh Sistem DigiNiaga
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            Tanggal: <?= date('d F Y, H:i') ?> WIB • Valid tanpa tanda tangan
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-500 rounded-2xl flex items-center justify-center">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        // Trend Chart
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        let gradientTrend = ctxTrend.createLinearGradient(0, 0, 0, 400);
        gradientTrend.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
        gradientTrend.addColorStop(1, 'rgba(139, 92, 246, 0.05)');

        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: <?= json_encode($trend_labels) ?>,
                datasets: [{
                    label: 'Pendapatan',
                    data: <?= json_encode($trend_data) ?>,
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: gradientTrend,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        borderRadius: 8,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { 
                            borderDash: [5, 5],
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            font: { size: 11, weight: '600' },
                            color: '#6B7280',
                            callback: function(value) {
                                return 'Rp ' + (value/1000) + 'k';
                            }
                        }
                    },
                    x: { 
                        grid: { display: false, drawBorder: false },
                        ticks: {
                            font: { size: 11, weight: '700' },
                            color: '#374151'
                        }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeInOutQuart'
                }
            }
        });

        // Category Chart
        const ctxCat = document.getElementById('categoryChart').getContext('2d');
        new Chart(ctxCat, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($cat_labels) ?>,
                datasets: [{
                    data: <?= json_encode($cat_data) ?>,
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        borderRadius: 8,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function(context) {
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': Rp ' + context.parsed.toLocaleString('id-ID') + ' (' + percentage + '%)';
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1500,
                    easing: 'easeInOutQuart'
                }
            }
        });

        // Export PDF Function
        function exportPDF() {
            const element = document.getElementById('report-content');
            
            // Show PDF-only elements
            const headers = document.querySelectorAll('.pdf-only');
            headers.forEach(el => el.style.display = 'block');

            const opt = {
                margin:       [10, 10, 10, 10],
                filename:     'Laporan_Penjualan_<?= htmlspecialchars($store_name) ?>_<?= date("Ymd") ?>.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true, letterRendering: true },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(element).save().then(() => {
                headers.forEach(el => el.style.display = 'none');
            });
        }

        // Animate progress bars on load
        window.addEventListener('load', function() {
            const progressBars = document.querySelectorAll('.progress-fill');
            progressBars.forEach((bar, index) => {
                setTimeout(() => {
                    bar.style.width = bar.style.width || '0%';
                }, index * 100);
            });
        });
    </script>

</body>
</html>
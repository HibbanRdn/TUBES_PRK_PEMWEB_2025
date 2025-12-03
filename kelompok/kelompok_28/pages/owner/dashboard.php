<?php
session_start();
require_once '../../config/database.php';

// 1. Cek Login & Role Owner
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'owner') {
    header("Location: ../../redirect.php");
    exit;
}

$fullname = $_SESSION['fullname'];
$owner_id = $_SESSION['user_id'];

// 2. Ambil Store ID milik Owner
$sql_store = "SELECT id FROM stores WHERE owner_id = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql_store);
mysqli_stmt_bind_param($stmt, "i", $owner_id);
mysqli_stmt_execute($stmt);
$res_store = mysqli_stmt_get_result($stmt);
$store = mysqli_fetch_assoc($res_store);

// Default values
$store_id = $store['id'] ?? 0;
$has_store = ($store_id > 0); 

// Inisialisasi variabel
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

    // B. Hitung Stok Menipis
    $sql_stock = "SELECT COUNT(id) as low_count FROM products WHERE store_id = ? AND stock < 5";
    $stmt = mysqli_prepare($conn, $sql_stock);
    mysqli_stmt_bind_param($stmt, "i", $store_id);
    mysqli_stmt_execute($stmt);
    $low_stock = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['low_count'];

    // C. Ambil 5 Transaksi Terakhir
    $sql_recent = "SELECT t.invoice_code, t.date, t.total_price, e.fullname as kasir 
                   FROM transactions t
                   JOIN employees e ON t.employee_id = e.id
                   WHERE t.store_id = ? 
                   ORDER BY t.date DESC LIMIT 5";
    $stmt = mysqli_prepare($conn, $sql_recent);
    mysqli_stmt_bind_param($stmt, "i", $store_id);
    mysqli_stmt_execute($stmt);
    $recent_trx = mysqli_stmt_get_result($stmt);

    // D. Data Grafik (7 Hari Terakhir)
    for ($i = 6; $i >= 0; $i--) {
        $date_loop = date('Y-m-d', strtotime("-$i days"));
        $day_name = date('D', strtotime($date_loop));
        
        $sql_chart = "SELECT SUM(total_price) as total FROM transactions WHERE store_id = ? AND DATE(date) = ?";
        $stmt = mysqli_prepare($conn, $sql_chart);
        mysqli_stmt_bind_param($stmt, "is", $store_id, $date_loop);
        mysqli_stmt_execute($stmt);
        $res_chart = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        
        $days_indo = ['Sun'=>'Min', 'Mon'=>'Sen', 'Tue'=>'Sel', 'Wed'=>'Rab', 'Thu'=>'Kam', 'Fri'=>'Jum', 'Sat'=>'Sab'];
        $chart_labels[] = $days_indo[$day_name];
        $chart_data[] = $res_chart['total'] ?? 0;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Owner - DigiNiaga</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .animate-fadeIn { animation: fadeIn 0.5s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-gray-50 min-h-screen pb-10">

    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-20 shadow-sm">
        <div class="flex items-center gap-3">
            <img src="../../assets/images/logo.png" alt="Logo" class="h-8 w-auto">
            <span class="font-bold text-gray-800 text-lg hidden md:block">DigiNiaga POS</span>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-right hidden sm:block">
                <p class="text-sm font-bold text-gray-700"><?= htmlspecialchars($fullname) ?></p>
                <p class="text-xs text-blue-600 font-medium">Owner</p>
            </div>
            <a href="../../auth/logout.php" class="text-red-500 hover:text-red-700 transition p-2 bg-red-50 rounded-lg" title="Keluar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </a>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto mt-8 px-6">
        
        <?php if ($has_store): ?>
            
            <div class="animate-fadeIn">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Dashboard Ringkasan</h1>
                        <p class="text-gray-500 text-sm mt-1">Pantau performa toko Anda hari ini.</p>
                    </div>
                    
                    <div class="flex flex-wrap gap-3">
                        <a href="reports.php" class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            Laporan
                        </a>

                        <a href="users.php" class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Karyawan
                        </a>

                        <a href="edit_store.php" class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Pengaturan
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between h-40 hover:shadow-md transition">
                        <div>
                            <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center text-green-600 mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="text-gray-500 text-sm font-medium">Omzet Hari Ini</p>
                            <h3 class="text-2xl font-bold text-gray-900 mt-1">Rp <?= number_format($omzet_today, 0, ',', '.') ?></h3>
                        </div>
                        <div class="flex items-center text-xs font-semibold text-green-600">
                            <span class="inline-block w-2 h-2 rounded-full bg-green-500 mr-2 animate-pulse"></span>
                            <span>Update Realtime</span>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between h-40 hover:shadow-md transition">
                        <div>
                            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            </div>
                            <p class="text-gray-500 text-sm font-medium">Total Transaksi</p>
                            <h3 class="text-2xl font-bold text-gray-900 mt-1"><?= $trx_count ?></h3>
                        </div>
                        <div class="text-xs font-semibold text-blue-600">
                            Penjualan Hari Ini
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between h-40 hover:shadow-md transition">
                        <div>
                            <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center text-orange-500 mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <p class="text-gray-500 text-sm font-medium">Stok Menipis</p>
                            <h3 class="text-2xl font-bold text-gray-900 mt-1"><?= $low_stock ?></h3>
                        </div>
                        <?php if ($low_stock > 0): ?>
                            <div class="text-xs font-bold text-orange-600 bg-orange-100 px-2 py-1 rounded w-fit">
                                Perlu Restock!
                            </div>
                        <?php else: ?>
                            <div class="text-xs font-semibold text-green-600">
                                Stok Aman
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <h3 class="font-bold text-gray-800 mb-6">Grafik Penjualan Mingguan</h3>
                        <div class="relative h-64 w-full">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>

                    <div class="lg:col-span-1 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-gray-800">Transaksi Terakhir</h3>
                            <a href="#" class="text-xs text-blue-600 hover:underline">Lihat Semua</a>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-gray-400 uppercase font-semibold border-b border-gray-100">
                                    <tr>
                                        <th class="py-2">ID</th>
                                        <th class="py-2">Waktu</th>
                                        <th class="py-2 text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <?php if (!empty($recent_trx) && mysqli_num_rows($recent_trx) > 0): ?>
                                        <?php while($row = mysqli_fetch_assoc($recent_trx)): 
                                            $short_id = substr($row['invoice_code'], -6);
                                            $time = date('H:i', strtotime($row['date']));
                                        ?>
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="py-3 font-medium text-gray-900 text-xs">#<?= $short_id ?></td>
                                            <td class="py-3 text-gray-500 text-xs"><?= $time ?></td>
                                            <td class="py-3 text-right font-bold text-green-600 text-xs">
                                                Rp<?= number_format($row['total_price'], 0, ',', '.') ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="py-8 text-center text-gray-400 text-xs">Belum ada transaksi hari ini.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                const ctx = document.getElementById('salesChart').getContext('2d');
                const labels = <?= json_encode($chart_labels) ?>;
                const dataValues = <?= json_encode($chart_data) ?>;

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Penjualan (Rp)',
                            data: dataValues,
                            backgroundColor: '#3b82f6', 
                            borderRadius: 6,
                            hoverBackgroundColor: '#2563eb'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { borderDash: [4, 4], color: '#f3f4f6' }, ticks: { font: { size: 10 } } },
                            x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                        }
                    }
                });
            </script>

        <?php else: ?>

            <div class="flex flex-col items-center justify-center min-h-[60vh] text-center animate-fadeIn">
                <div class="relative mb-6">
                    <div class="absolute inset-0 bg-blue-100 rounded-full animate-ping opacity-25"></div>
                    <div class="bg-white p-6 rounded-full shadow-xl border border-blue-50 relative z-10">
                        <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
                
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang, <?= htmlspecialchars($fullname) ?>! 👋</h2>
                <p class="text-gray-500 max-w-lg mx-auto mb-8 text-lg">
                    Langkah awal menuju kesuksesan digital Anda dimulai di sini. <br>
                    Anda belum memiliki toko yang terdaftar.
                </p>
                
                <a href="store_setup.php" class="group relative inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white transition-all duration-200 bg-blue-600 rounded-full hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 hover:shadow-lg hover:-translate-y-1">
                    <span class="mr-2">Mulai Usaha Pertama Anda</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </a>
                
                <p class="mt-6 text-xs text-gray-400">
                    Hanya butuh 2 menit untuk setup toko, produk, dan karyawan.
                </p>
            </div>

        <?php endif; ?>

    </div>

</body>
</html>
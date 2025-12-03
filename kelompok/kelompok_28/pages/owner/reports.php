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
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        /* Style khusus saat generate PDF */
        .pdf-only { display: none; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen pb-12">

    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-20 shadow-sm" data-html2canvas-ignore="true">
        <div class="flex items-center gap-3">
            <a href="dashboard.php" class="text-gray-500 hover:text-blue-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <span class="font-bold text-gray-800 text-lg">Laporan Penjualan</span>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-600 hidden sm:block">Halo, <b><?= htmlspecialchars($fullname) ?></b></span>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto mt-8 px-6" data-html2canvas-ignore="true">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Analisis Performa</h1>
                <p class="text-gray-500 text-sm mt-1">Laporan detail keuangan toko Anda.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <form action="" method="GET" class="relative">
                    <select name="period" onchange="this.form.submit()" class="appearance-none bg-white border border-gray-300 text-gray-700 py-2 pl-4 pr-8 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium cursor-pointer">
                        <option value="7" <?= $period == '7' ? 'selected' : '' ?>>7 Hari Terakhir</option>
                        <option value="30" <?= $period == '30' ? 'selected' : '' ?>>30 Hari Terakhir</option>
                        <option value="month" <?= $period == 'month' ? 'selected' : '' ?>>Bulan Ini</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </form>

                <button onclick="exportPDF()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-md transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export PDF
                </button>
            </div>
        </div>
    </div>

    <div id="report-content" class="max-w-6xl mx-auto px-6">
        
        <div id="pdf-header" class="pdf-only mb-8 border-b-2 border-gray-800 pb-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 uppercase tracking-wide"><?= htmlspecialchars($store_name) ?></h1>
                    <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($store_address) ?></p>
                    <p class="text-sm text-gray-600">Telp: <?= htmlspecialchars($store_phone) ?></p>
                </div>
                <div class="text-right">
                    <h2 class="text-xl font-bold text-blue-700">LAPORAN PENJUALAN</h2>
                    <p class="text-sm text-gray-500 mt-1">Periode: <?= $formatted_period ?></p>
                    <p class="text-xs text-gray-400">Dicetak: <?= date('d M Y H:i') ?></p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Total Pendapatan</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">Rp <?= number_format($total_revenue, 0, ',', '.') ?></h3>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Total Transaksi</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1"><?= number_format($total_trx) ?></h3>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Rata-rata per Hari</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">Rp <?= number_format($avg_daily, 0, ',', '.') ?></h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mb-8 break-inside-avoid">
            <h3 class="font-bold text-gray-800 mb-4">Tren Pendapatan</h3>
            <div class="relative h-72 w-full">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
            
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm break-inside-avoid">
                <h3 class="font-bold text-gray-800 mb-4">Penjualan per Kategori</h3>
                <div class="relative h-64 w-full flex justify-center">
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="mt-4 space-y-2">
                    <?php 
                    $colors = ['#2563eb', '#16a34a', '#f59e0b', '#dc2626', '#9333ea'];
                    foreach($cat_labels as $index => $cat): 
                        $val = $cat_data[$index];
                        $color = $colors[$index % count($colors)];
                    ?>
                    <div class="flex justify-between items-center text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full" style="background-color: <?= $color ?>"></span>
                            <span class="text-gray-600"><?= htmlspecialchars($cat) ?></span>
                        </div>
                        <span class="font-bold text-gray-800">Rp <?= number_format($val, 0, ',', '.') ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm break-inside-avoid">
                <h3 class="font-bold text-gray-800 mb-4">Top 5 Produk Terlaris</h3>
                <div class="space-y-3">
                    <?php 
                    $rank = 1;
                    if ($top_products && mysqli_num_rows($top_products) > 0):
                        while($prod = mysqli_fetch_assoc($top_products)): 
                    ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs">
                                <?= $rank++ ?>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm"><?= htmlspecialchars($prod['name']) ?></h4>
                                <p class="text-xs text-gray-500"><?= $prod['sold'] ?> terjual</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="block font-bold text-green-600 text-sm">Rp <?= number_format($prod['revenue'], 0, ',', '.') ?></span>
                        </div>
                    </div>
                    <?php endwhile; else: ?>
                        <div class="text-center py-4 text-gray-400 text-sm">Data tidak tersedia.</div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <div id="pdf-footer" class="pdf-only mt-12 pt-4 border-t border-gray-200 text-center">
            <p class="text-xs text-gray-400">
                Laporan ini dibuat otomatis oleh Sistem DigiNiaga pada <?= date('d F Y') ?>.<br>
                Valid tanpa tanda tangan.
            </p>
        </div>

    </div>

    <script>
        // 1. Inisialisasi Chart
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        let gradient = ctxTrend.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
        gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: <?= json_encode($trend_labels) ?>,
                datasets: [{
                    label: 'Pendapatan',
                    data: <?= json_encode($trend_data) ?>,
                    borderColor: '#2563eb',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    pointRadius: 0, // Hilangkan titik agar rapi di PDF
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [2, 4] } },
                    x: { grid: { display: false } }
                }
            }
        });

        const ctxCat = document.getElementById('categoryChart').getContext('2d');
        new Chart(ctxCat, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($cat_labels) ?>,
                datasets: [{
                    data: <?= json_encode($cat_data) ?>,
                    backgroundColor: ['#2563eb', '#16a34a', '#f59e0b', '#dc2626', '#9333ea'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: { legend: { display: false } }
            }
        });

        // 2. FUNGSI EXPORT PDF PROFESIONAL
        function exportPDF() {
            // Ambil elemen yang akan di-print
            const element = document.getElementById('report-content');
            
            // Tampilkan Header & Footer khusus PDF
            const headers = document.querySelectorAll('.pdf-only');
            headers.forEach(el => el.style.display = 'block');

            // Konfigurasi PDF
            const opt = {
                margin:       [10, 10, 10, 10], // Margin (Atas, Kiri, Bawah, Kanan)
                filename:     'Laporan_Penjualan_<?= date("Ymd") ?>.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true }, // Scale 2 agar teks tajam
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            // Generate
            html2pdf().set(opt).from(element).save().then(() => {
                // Sembunyikan kembali Header & Footer setelah selesai download
                headers.forEach(el => el.style.display = 'none');
            });
        }
    </script>

</body>
</html>
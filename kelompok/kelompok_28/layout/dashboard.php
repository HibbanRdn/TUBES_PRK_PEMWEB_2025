<?php 
require_once '../process/process_owner.php'; 

// Set judul halaman untuk header.php
$page_title = "Dashboard Owner - DigiNiaga";

// 1. Include Header
require_once 'header.php';

// 2. Include Navbar
require_once 'navbar.php';
?>

<div class="max-w-7xl mx-auto mt-8 px-6 relative z-10">
    
    <?php if ($has_store): ?>
        
        <div class="animate-fadeIn">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 gap-4">
                <div class="animate-slideUp">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-blue-500 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-extrabold text-gray-800">Dashboard Ringkasan</h1>
                            <p class="text-gray-500 text-sm mt-1 font-medium">Pantau performa toko Anda secara realtime</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-3">
                    <a href="reports.php" class="bg-white border border-gray-200 text-gray-700 px-5 py-3 rounded-xl text-sm font-bold hover:scale-105 hover:shadow-lg transition-all duration-300 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Laporan
                    </a>
                    <a href="users.php" class="bg-white border border-gray-200 text-gray-700 px-5 py-3 rounded-xl text-sm font-bold hover:scale-105 hover:shadow-lg transition-all duration-300 flex items-center gap-2">
                         <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Karyawan
                    </a>
                    <a href="settings.php" class="bg-white border border-gray-200 text-gray-700 px-5 py-3 rounded-xl text-sm font-bold hover:scale-105 hover:shadow-lg transition-all duration-300 flex items-center gap-2">
                         <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Pengaturan
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="card-gradient p-8 rounded-3xl shadow-2xl hover-lift border border-white/50 animate-scaleIn relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-green-400/20 to-emerald-400/20 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-4">
                            <div class="stat-icon w-16 h-16 bg-gradient-to-br from-green-400 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="flex items-center gap-2 bg-green-50 px-3 py-1 rounded-full">
                                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div><span class="text-xs font-bold text-green-700">Live</span>
                            </div>
                        </div>
                        <p class="text-gray-500 text-sm font-semibold mb-2 uppercase tracking-wide">Omzet Hari Ini</p>
                        <h3 class="text-4xl font-extrabold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent mb-3">Rp <?= number_format($omzet_today, 0, ',', '.') ?></h3>
                    </div>
                </div>

                <div class="card-gradient p-8 rounded-3xl shadow-2xl hover-lift border border-white/50 animate-scaleIn relative overflow-hidden group" style="animation-delay: 0.1s;">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-400/20 to-cyan-400/20 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-4">
                            <div class="stat-icon w-16 h-16 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-400 font-semibold">Target</p>
                                <p class="text-sm font-bold text-blue-600">50 trx</p>
                            </div>
                        </div>
                        <p class="text-gray-500 text-sm font-semibold mb-2 uppercase tracking-wide">Total Transaksi</p>
                        <h3 class="text-4xl font-extrabold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent mb-3"><?= $trx_count ?></h3>
                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-500 to-cyan-500 h-2 rounded-full transition-all duration-1000" style="width: <?= min(($trx_count/50)*100, 100) ?>%"></div>
                        </div>
                    </div>
                </div>

                <div class="card-gradient p-8 rounded-3xl shadow-2xl hover-lift border border-white/50 animate-scaleIn relative overflow-hidden group" style="animation-delay: 0.2s;">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-orange-400/20 to-red-400/20 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-4">
                            <div class="stat-icon w-16 h-16 bg-gradient-to-br from-orange-400 to-red-500 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <?php if ($low_stock > 0): ?>
                                <div class="flex items-center gap-1 bg-red-50 px-3 py-1 rounded-full"><span class="text-xs font-bold text-red-700">Alert!</span></div>
                            <?php endif; ?>
                        </div>
                        <p class="text-gray-500 text-sm font-semibold mb-2 uppercase tracking-wide">Stok Menipis</p>
                        <h3 class="text-4xl font-extrabold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent mb-3"><?= $low_stock ?></h3>
                        <?php if ($low_stock > 0): ?>
                            <div class="flex items-center gap-2 bg-red-100 px-3 py-2 rounded-xl"><span class="text-xs font-bold text-red-700">Segera Restock!</span></div>
                        <?php else: ?>
                            <div class="flex items-center gap-2 bg-green-100 px-3 py-2 rounded-xl"><span class="text-xs font-bold text-green-700">Stok Aman</span></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 card-gradient p-8 rounded-3xl shadow-2xl border border-white/50 animate-slideUp">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-extrabold text-gray-800 flex items-center gap-3">Grafik Penjualan Mingguan</h3>
                    </div>
                    <div class="chart-container relative">
                        <canvas id="salesChart" class="w-full" style="height: 300px;"></canvas>
                    </div>
                </div>

                <div class="lg:col-span-1 card-gradient p-8 rounded-3xl shadow-2xl border border-white/50 animate-slideUp" style="animation-delay: 0.1s;">
                    <h3 class="text-xl font-extrabold text-gray-800 mb-6">Aktivitas Terbaru</h3>
                    <div class="space-y-4 max-h-96 overflow-y-auto custom-scrollbar">
                        <?php if (!empty($recent_trx) && mysqli_num_rows($recent_trx) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($recent_trx)): ?>
                            <div class="bg-white p-4 rounded-2xl border border-gray-100 hover:shadow-lg transition-all duration-300">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="font-bold text-gray-800 text-sm">#<?= substr($row['invoice_code'], -6) ?></p>
                                        <p class="text-xs text-gray-400"><?= date('H:i', strtotime($row['date'])) ?></p>
                                    </div>
                                    <span class="text-sm font-extrabold text-green-600">Rp<?= number_format($row['total_price'], 0, ',', '.') ?></span>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-center text-gray-400 text-sm">Belum ada transaksi</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const ctx = document.getElementById('salesChart').getContext('2d');
            const labels = <?= json_encode($chart_labels) ?>;
            const dataValues = <?= json_encode($chart_data) ?>;
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(99, 102, 241, 0.8)');
            gradient.addColorStop(1, 'rgba(168, 85, 247, 0.8)');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Penjualan (Rp)',
                        data: dataValues,
                        backgroundColor: gradient,
                        borderRadius: 12,
                        hoverBackgroundColor: 'rgba(79, 70, 229, 1)',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [5, 5], drawBorder: false } },
                        x: { grid: { display: false } }
                    }
                }
            });
        </script>

    <?php else: ?>
        <div class="flex flex-col items-center justify-center min-h-[70vh] text-center animate-fadeIn">
            <h2 class="text-5xl font-extrabold text-gray-800 mb-4 animate-slideUp">
                Selamat Datang, <?= htmlspecialchars($fullname) ?>! 👋
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto mb-10 text-xl font-medium animate-slideUp">
                Mari kita ciptakan toko online pertama Anda!
            </p>
            <a href="store_setup.php" class="inline-flex items-center justify-center px-10 py-5 text-lg font-extrabold text-white bg-gradient-to-r from-purple-600 to-blue-600 rounded-2xl hover:scale-105 transition-all">
                Mulai Usaha Pertama Anda
            </a>
        </div>
    <?php endif; ?>

</div>

<?php 
// 3. Include Footer (Tutup Body HTML)
require_once 'footer.php'; 
?>
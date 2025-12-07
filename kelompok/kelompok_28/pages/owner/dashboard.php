<?php 
// FILE: dashboard.php
// Memanggil logika backend sebelum merender HTML
require_once '../../process/process_owner.php'; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Owner - DigiNiaga</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(5px);
        }
        
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
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .hover-lift:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .card-gradient {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
        }
        
        .stat-icon {
            position: relative;
            overflow: hidden;
        }
        
        .stat-icon::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
            animation: iconGlow 3s ease-in-out infinite;
        }
        
        @keyframes iconGlow {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(10px, 10px); }
        }
        
        .chart-container {
            position: relative;
            background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
            border-radius: 1.5rem;
            padding: 1.5rem;
        }

        /* Modal Animation Styles */
        .modal-enter {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease-out;
        }
        .modal-enter-active {
            opacity: 1;
            pointer-events: auto;
        }
        .modal-content-enter {
            transform: scale(0.95) translateY(10px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .modal-content-active {
            transform: scale(1) translateY(0);
            opacity: 1;
        }
        
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: linear-gradient(135deg, #5568d3, #65408b); }
    </style>
</head>
<body class="min-h-screen pb-16">

    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-50 rounded-full filter blur-3xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-50 rounded-full filter blur-3xl opacity-20"></div>
    </div>

    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-50 shadow-sm">
        <div class="flex items-center gap-3 animate-fadeIn">
            <div class="relative">
                <div class="h-10 w-10 bg-gradient-to-br from-blue-100 to-purple-100 rounded-lg flex items-center justify-center border border-blue-50 shadow-sm relative z-10">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path> </svg>
                </div>
                <div class="absolute inset-0 bg-blue-400 blur-md opacity-20 rounded-lg"></div>
            </div>

            <div class="hidden md:block">
                <span class="block font-bold text-gray-800 text-lg leading-tight tracking-tight">
                    <?= htmlspecialchars($store_name) ?>
                </span>
                
                <span class="block text-xs text-gray-500 font-medium mt-0.5 max-w-[250px] truncate" title="<?= htmlspecialchars($store_address) ?>">
                    <?= htmlspecialchars($store_address) ?>
                </span>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-right hidden sm:block">
                <p class="text-sm font-bold text-gray-700"><?= htmlspecialchars($fullname) ?></p>
                <p class="text-xs font-semibold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">Owner</p>
            </div>
            
            <button onclick="confirmLogout()" class="text-red-500 hover:text-white hover:bg-red-500 transition-all duration-300 p-3 bg-red-50 rounded-xl hover-lift group relative" title="Keluar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </button>
        </div>
    </nav>

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
                                <div class="stat-icon w-16 h-16 bg-gradient-to-br from-green-400 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:rotate-12 transition-transform duration-300">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div class="flex items-center gap-2 bg-green-50 px-3 py-1 rounded-full">
                                    <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                                    <span class="text-xs font-bold text-green-700">Live</span>
                                </div>
                            </div>
                            <p class="text-gray-500 text-sm font-semibold mb-2 uppercase tracking-wide">Omzet Hari Ini</p>
                            <h3 class="text-4xl font-extrabold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent mb-3">
                                Rp <?= number_format($omzet_today, 0, ',', '.') ?>
                            </h3>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path></svg>
                                <span class="text-xs font-bold text-green-600">Update Realtime</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-gradient p-8 rounded-3xl shadow-2xl hover-lift border border-white/50 animate-scaleIn relative overflow-hidden group" style="animation-delay: 0.1s;">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-400/20 to-cyan-400/20 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-4">
                                <div class="stat-icon w-16 h-16 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:rotate-12 transition-transform duration-300">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-400 font-semibold">Target</p>
                                    <p class="text-sm font-bold text-blue-600">50 trx</p>
                                </div>
                            </div>
                            <p class="text-gray-500 text-sm font-semibold mb-2 uppercase tracking-wide">Total Transaksi</p>
                            <h3 class="text-4xl font-extrabold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent mb-3">
                                <?= $trx_count ?>
                            </h3>
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-500 to-cyan-500 h-2 rounded-full transition-all duration-1000" style="width: <?= min(($trx_count/50)*100, 100) ?>%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card-gradient p-8 rounded-3xl shadow-2xl hover-lift border border-white/50 animate-scaleIn relative overflow-hidden group" style="animation-delay: 0.2s;">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-orange-400/20 to-red-400/20 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-4">
                                <div class="stat-icon w-16 h-16 bg-gradient-to-br from-orange-400 to-red-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:rotate-12 transition-transform duration-300">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <?php if ($low_stock > 0): ?>
                                    <div class="flex items-center gap-1 bg-red-50 px-3 py-1 rounded-full">
                                        <svg class="w-3 h-3 text-red-500 animate-bounce" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        <span class="text-xs font-bold text-red-700">Alert!</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <p class="text-gray-500 text-sm font-semibold mb-2 uppercase tracking-wide">Stok Menipis</p>
                            <h3 class="text-4xl font-extrabold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent mb-3">
                                <?= $low_stock ?>
                            </h3>
                            <?php if ($low_stock > 0): ?>
                                <div class="flex items-center gap-2 bg-red-100 px-3 py-2 rounded-xl">
                                    <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                    <span class="text-xs font-bold text-red-700">Segera Restock!</span>
                                </div>
                            <?php else: ?>
                                <div class="flex items-center gap-2 bg-green-100 px-3 py-2 rounded-xl">
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    <span class="text-xs font-bold text-green-700">Stok Aman</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 card-gradient p-8 rounded-3xl shadow-2xl border border-white/50 animate-slideUp">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-xl font-extrabold text-gray-800 flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-blue-500 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    Grafik Penjualan Mingguan
                                </h3>
                                <p class="text-xs text-gray-500 mt-1 ml-13">Analisis tren 7 hari terakhir</p>
                            </div>
                            <select class="bg-white border border-gray-200 rounded-xl px-4 py-2 text-sm font-semibold text-gray-700 hover:border-purple-300 transition-colors focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option>7 Hari</option>
                                <option>30 Hari</option>
                                <option>90 Hari</option>
                            </select>
                        </div>
                        <div class="chart-container relative">
                            <canvas id="salesChart" class="w-full" style="height: 300px;"></canvas>
                        </div>
                    </div>

                    <div class="lg:col-span-1 card-gradient p-8 rounded-3xl shadow-2xl border border-white/50 animate-slideUp" style="animation-delay: 0.1s;">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-xl font-extrabold text-gray-800 flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-teal-500 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    Aktivitas Terbaru
                                </h3>
                                <p class="text-xs text-gray-500 mt-1 ml-13">5 transaksi terakhir</p>
                            </div>
                        </div>
                        
                        <div class="space-y-4 max-h-96 overflow-y-auto custom-scrollbar">
                            <?php if (!empty($recent_trx) && mysqli_num_rows($recent_trx) > 0): ?>
                                <?php $delay = 0; ?>
                                <?php while($row = mysqli_fetch_assoc($recent_trx)): 
                                    $short_id = substr($row['invoice_code'], -6);
                                    $time = date('H:i', strtotime($row['date']));
                                ?>
                                <div class="bg-white p-4 rounded-2xl border border-gray-100 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 animate-fadeIn" style="animation-delay: <?= $delay * 0.1 ?>s;">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800 text-sm">#<?= $short_id ?></p>
                                                <p class="text-xs text-gray-400 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                                    <?= $time ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                                        <span class="text-xs text-gray-500">Total</span>
                                        <span class="text-sm font-extrabold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                                            Rp<?= number_format($row['total_price'], 0, ',', '.') ?>
                                        </span>
                                    </div>
                                </div>
                                <?php $delay++; ?>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="text-center py-12">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    </div>
                                    <p class="text-gray-400 text-sm font-medium">Belum ada transaksi</p>
                                    <p class="text-gray-300 text-xs mt-1">Data akan muncul setelah transaksi pertama</p>
                                </div>
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
                            borderSkipped: false,
                            hoverBackgroundColor: 'rgba(79, 70, 229, 1)',
                            barThickness: 40
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
                                    font: { size: 12, weight: '700' },
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
            </script>

        <?php else: ?>

            <div class="flex flex-col items-center justify-center min-h-[70vh] text-center animate-fadeIn">
                <div class="relative mb-8 floating">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-400 to-blue-400 rounded-full animate-ping opacity-20"></div>
                    <div class="bg-white p-10 rounded-full shadow-2xl border-4 border-purple-100 relative z-10">
                        <svg class="w-24 h-24 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
                
                <h2 class="text-5xl font-extrabold text-gray-800 mb-4 animate-slideUp">
                    Selamat Datang, <?= htmlspecialchars($fullname) ?>! 
                    <span class="inline-block animate-bounce ml-2">👋</span>
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto mb-10 text-xl font-medium animate-slideUp leading-relaxed" style="animation-delay: 0.1s;">
                    Langkah awal menuju kesuksesan digital Anda dimulai di sini. <br>
                    <span class="text-lg text-gray-500">Mari kita ciptakan toko online pertama Anda!</span>
                </p>
                
                <div class="bg-white p-8 rounded-3xl shadow-xl mb-8 border border-gray-100 animate-scaleIn max-w-lg" style="animation-delay: 0.2s;">
                    <div class="grid grid-cols-3 gap-6 text-center">
                        <div>
                            <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <p class="text-gray-800 font-bold text-sm">Cepat</p>
                            <p class="text-gray-500 text-xs">2 menit</p>
                        </div>
                        <div>
                            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-teal-500 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <p class="text-gray-800 font-bold text-sm">Aman</p>
                            <p class="text-gray-500 text-xs">Terenkripsi</p>
                        </div>
                        <div>
                            <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-red-500 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path></svg>
                            </div>
                            <p class="text-gray-800 font-bold text-sm">Mudah</p>
                            <p class="text-gray-500 text-xs">User friendly</p>
                        </div>
                    </div>
                </div>
                
                <a href="store_setup.php" class="group relative inline-flex items-center justify-center px-10 py-5 text-lg font-extrabold text-white transition-all duration-300 bg-gradient-to-r from-purple-600 to-blue-600 rounded-2xl hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-4 focus:ring-purple-300 hover:shadow-2xl hover:scale-105 animate-scaleIn" style="animation-delay: 0.3s;">
                    <span class="mr-3">Mulai Usaha Pertama Anda</span>
                    <svg class="w-6 h-6 group-hover:translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    <div class="absolute inset-0 bg-white/20 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </a>
                
                <div class="mt-8 flex items-center gap-3 text-gray-500 text-sm animate-fadeIn" style="animation-delay: 0.4s;">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                    <p class="font-medium">
                        Proses setup hanya 2 menit • Sudah dipercaya 1000+ UMKM
                    </p>
                </div>
            </div>

        <?php endif; ?>

    </div>

    <div id="logoutModal" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="logoutBackdrop"></div>
        
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="relative w-full max-w-md p-0 bg-white rounded-3xl shadow-2xl transform scale-95 opacity-0 transition-all duration-300 overflow-hidden" id="logoutPanel">
                
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-red-500 to-orange-500"></div>
                <div class="absolute -right-10 -top-10 w-32 h-32 bg-red-50 rounded-full blur-2xl opacity-50"></div>
                
                <div class="p-8 text-center relative z-10">
                    <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm border border-red-100">
                        <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </div>

                    <h3 class="text-2xl font-bold text-gray-900 mb-2 font-display">Konfirmasi Keluar</h3>
                    <p class="text-gray-500 text-sm mb-8 leading-relaxed">
                        Anda yakin ingin mengakhiri sesi ini? <br>
                        Anda harus login kembali untuk mengakses dashboard.
                    </p>
                    
                    <div class="flex gap-4">
                        <button onclick="closeLogoutModal()" class="w-full py-3.5 px-6 rounded-xl border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-200">
                            Batal
                        </button>
                        <a href="../../auth/logout.php" class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-red-600 to-orange-600 text-white font-bold hover:shadow-lg hover:scale-[1.02] transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 shadow-md flex items-center justify-center gap-2">
                            <span>Ya, Keluar</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('logoutModal');
        const backdrop = document.getElementById('logoutBackdrop');
        const panel = document.getElementById('logoutPanel');

        function confirmLogout() {
            modal.classList.remove('hidden');
            // Small delay to allow display:block to apply before changing opacity
            setTimeout(() => {
                modal.classList.add('modal-enter-active');
                backdrop.classList.remove('opacity-0');
                panel.classList.remove('scale-95', 'opacity-0');
                panel.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeLogoutModal() {
            backdrop.classList.add('opacity-0');
            panel.classList.remove('scale-100', 'opacity-100');
            panel.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.remove('modal-enter-active');
                modal.classList.add('hidden');
            }, 300); // Match transition duration
        }

        // Close on backdrop click
        modal.addEventListener('click', function(e) {
            if (e.target === backdrop || e.target.closest('#logoutPanel') === null && e.target !== panel) {
                // closeLogoutModal(); // Optional: Uncomment if you want backdrop click to close
            }
        });
        
        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeLogoutModal();
            }
        });
    </script>

</body>
</html>
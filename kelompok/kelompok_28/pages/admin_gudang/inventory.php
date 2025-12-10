<?php
session_start();
require_once '../../config/database.php';

// 1. LOGIKA CEK LOGIN & SESSION
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin_gudang') {
    header("Location: ../../auth/login.php"); 
    exit;
} else {
    $fullname = $_SESSION['fullname'];
    $user_id = $_SESSION['user_id'];
    
    $store_id = 0;
    $store_name = "DigiNiaga Store";
    $store_address = "Alamat tidak tersedia";

    // Ambil store_id dari karyawan, LALU ambil detail tokonya
    $sql_emp = "SELECT e.store_id, s.name as store_name, s.address as store_address 
                FROM employees e
                JOIN stores s ON e.store_id = s.id
                WHERE e.id = '$user_id'";
    
    $res_emp = mysqli_query($conn, $sql_emp);
    
    if($res_emp && mysqli_num_rows($res_emp) > 0){
        $data = mysqli_fetch_assoc($res_emp);
        $store_id = $data['store_id'];
        $store_name = $data['store_name'];       
        $store_address = $data['store_address']; 
        $_SESSION['store_id'] = $store_id;
    }
}

// 2. LOGIKA STOCK
$stats_query = "SELECT 
                    COUNT(*) as total_items,
                    SUM(stock * price) as total_asset,
                    SUM(CASE WHEN stock < 5 THEN 1 ELSE 0 END) as low_stock_count
                FROM products 
                WHERE store_id = '$store_id' AND is_active = 1";
$stats_res = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_res);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gudang - <?= htmlspecialchars($store_name) ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
        }
        
        .glass-effect { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(5px); }
        .animate-fadeIn { animation: fadeIn 0.6s ease-out; }
        .animate-slideUp { animation: slideUp 0.5s ease-out; }
        .animate-scaleIn { animation: scaleIn 0.4s ease-out; }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes scaleIn { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
        
        .hover-lift { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .hover-lift:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15); }
        
        .card-gradient { background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); }
        
        .stat-icon { position: relative; overflow: hidden; }
        .stat-icon::before {
            content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
            animation: iconGlow 3s ease-in-out infinite;
        }
        @keyframes iconGlow { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(10px, 10px); } }

        .modal-enter { opacity: 0; pointer-events: none; transition: opacity 0.3s ease-out; }
        .modal-enter-active { opacity: 1; pointer-events: auto; }
        
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 10px; }
    </style>
</head>
<body class="min-h-screen pb-16">

    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-50 rounded-full filter blur-3xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-indigo-50 rounded-full filter blur-3xl opacity-20"></div>
    </div>

    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-50 shadow-sm">
        <div class="flex items-center gap-3 animate-fadeIn">
            <div class="relative">
                <div class="h-10 w-10 bg-gradient-to-br from-indigo-500 to-blue-500 rounded-lg flex items-center justify-center border border-indigo-50 shadow-sm relative z-10">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <div class="absolute inset-0 bg-indigo-400 blur-md opacity-20 rounded-lg"></div>
            </div>
            
            <div>
                <span class="block font-bold text-gray-800 text-lg leading-tight tracking-tight">
                    Panel Gudang <?= htmlspecialchars($store_name) ?>
                </span>
                <span class="block text-xs text-gray-500 font-medium mt-0.5 max-w-[250px] truncate" title="<?= htmlspecialchars($store_address) ?>">
                    <?= htmlspecialchars($store_address) ?>
                </span>
            </div>
            </div>
        <div class="flex items-center gap-4 animate-fadeIn">
            <div class="text-right hidden sm:block">
                <p class="text-sm font-bold text-gray-700"><?= htmlspecialchars($fullname) ?></p>
                <p class="text-xs font-semibold bg-gradient-to-r from-indigo-600 to-blue-600 bg-clip-text text-transparent">Admin Gudang</p>
            </div>
            
            <button onclick="confirmLogout()" class="text-red-500 hover:text-white hover:bg-red-500 transition-all duration-300 p-3 bg-red-50 rounded-xl hover-lift group relative" title="Keluar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </button>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto mt-8 px-6 relative z-10">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="card-gradient p-8 rounded-3xl shadow-2xl hover-lift border border-white/50 animate-scaleIn relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-indigo-400/20 to-blue-400/20 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="stat-icon w-16 h-16 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:rotate-12 transition-transform duration-300 mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    </div>
                    <p class="text-gray-500 text-sm font-semibold mb-2 uppercase tracking-wide">Total Produk</p>
                    <h3 class="text-4xl font-extrabold bg-gradient-to-r from-indigo-600 to-blue-600 bg-clip-text text-transparent mb-3">
                        <?= $stats['total_items'] ?? 0 ?> <span class="text-lg text-gray-400">Unit</span>
                    </h3>
                </div>
            </div>

            <div class="card-gradient p-8 rounded-3xl shadow-2xl hover-lift border border-white/50 animate-scaleIn relative overflow-hidden group" style="animation-delay: 0.1s;">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-green-400/20 to-emerald-400/20 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="stat-icon w-16 h-16 bg-gradient-to-br from-green-400 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:rotate-12 transition-transform duration-300 mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-gray-500 text-sm font-semibold mb-2 uppercase tracking-wide">Estimasi Aset</p>
                    <h3 class="text-4xl font-extrabold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent mb-3">
                        Rp <?= number_format($stats['total_asset'] ?? 0, 0, ',', '.') ?>
                    </h3>
                </div>
            </div>

            <div class="card-gradient p-8 rounded-3xl shadow-2xl hover-lift border border-white/50 animate-scaleIn relative overflow-hidden group" style="animation-delay: 0.2s;">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-orange-400/20 to-red-400/20 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-4">
                        <div class="stat-icon w-16 h-16 <?= ($stats['low_stock_count'] > 0) ? 'bg-gradient-to-br from-orange-400 to-red-500' : 'bg-gray-200' ?> rounded-2xl flex items-center justify-center shadow-lg group-hover:rotate-12 transition-transform duration-300">
                            <svg class="w-8 h-8 <?= ($stats['low_stock_count'] > 0) ? 'text-white' : 'text-gray-500' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <?php if ($stats['low_stock_count'] > 0): ?>
                            <div class="flex items-center gap-1 bg-red-50 px-3 py-1 rounded-full animate-pulse">
                                <span class="text-xs font-bold text-red-700">Perhatian!</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <p class="text-gray-500 text-sm font-semibold mb-2 uppercase tracking-wide">Stok Menipis</p>
                    <h3 class="text-4xl font-extrabold <?= ($stats['low_stock_count'] > 0) ? 'bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent' : 'text-gray-400' ?> mb-3">
                        <?= $stats['low_stock_count'] ?? 0 ?> <span class="text-lg font-normal text-gray-400">Item</span>
                    </h3>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-1 animate-slideUp">
                <div class="card-gradient p-8 rounded-3xl shadow-2xl border border-white/50 sticky top-28">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-extrabold text-gray-800 flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center shadow-md">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </div>
                            Input Barang
                        </h3>
                    </div>

                    <form action="../../process/barang_handler.php?act=add" method="POST" class="space-y-5">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide ml-1">Nama Produk</label>
                            <input type="text" name="name" class="w-full px-5 py-3 bg-white border border-gray-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all shadow-sm" placeholder="Contoh: Kopi Robusta" required>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide ml-1">Kategori</label>
                            <div class="flex gap-2">
                                <div class="relative w-full">
                                    <select name="category_id" class="w-full px-5 py-3 bg-white border border-gray-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all appearance-none shadow-sm" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <?php
                                        // Simpan query di variabel agar bisa dipakai ulang di Modal Edit
                                        $cat_query = mysqli_query($conn, "SELECT * FROM categories WHERE store_id = '$store_id'");
                                        while($cat = mysqli_fetch_assoc($cat_query)) {
                                            echo "<option value='".$cat['id']."'>".$cat['name']."</option>";
                                        }
                                        ?>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                                <button type="button" onclick="openModalCategory()" class="bg-indigo-50 text-indigo-600 border border-indigo-100 w-12 rounded-xl flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all shadow-sm hover:shadow-md" title="Tambah Kategori">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide ml-1">Harga</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-3.5 text-gray-400 text-xs font-bold">Rp</span>
                                    <input type="number" name="price" class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm" placeholder="0" required>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide ml-1">Stok</label>
                                <input type="number" name="stock" class="w-full px-5 py-3 bg-white border border-gray-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm" placeholder="0" required>
                            </div>
                        </div>

                        <button type="submit" class="w-full mt-4 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-indigo-200 hover:shadow-indigo-300 transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Simpan Data
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2 animate-slideUp" style="animation-delay: 0.1s;">
                <div class="card-gradient p-8 rounded-3xl shadow-2xl border border-white/50 flex flex-col h-full">
                    
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-xl font-extrabold text-gray-800 flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center shadow-md">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                            </div>
                            Daftar Stok
                        </h3>
                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" id="searchInput" onkeyup="searchTable()" class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm" placeholder="Cari nama barang...">
                        </div>
                    </div>

                    <div class="overflow-x-auto custom-scrollbar flex-grow rounded-2xl border border-gray-100">
                        <table class="w-full" id="inventoryTable">
                            <thead class="bg-gray-50/50 sticky top-0">
                                <tr>
                                    <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Produk</th>
                                    <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Harga</th>
                                    <th class="text-center py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Stok</th>
                                    <th class="text-right py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <?php
                                $query = "SELECT p.*, c.name as category_name 
                                          FROM products p 
                                          LEFT JOIN categories c ON p.category_id = c.id 
                                          WHERE p.store_id = '$store_id' AND p.is_active = 1 
                                          ORDER BY p.id DESC";
                                $result = mysqli_query($conn, $query);

                                if(mysqli_num_rows($result) > 0){
                                    while($row = mysqli_fetch_assoc($result)) {
                                        $isLow = $row['stock'] < 5;
                                ?>
                                <tr class="hover:bg-indigo-50/30 transition-colors group">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-indigo-600 font-bold text-lg shadow-inner">
                                                <?= strtoupper(substr($row['name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-800 text-sm productName"><?= htmlspecialchars($row['name']) ?></div>
                                                <div class="text-[10px] text-gray-400 font-mono tracking-wider">ID: <?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-3 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                            <?= htmlspecialchars($row['category_name'] ?? 'General') ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 font-bold text-gray-700 text-sm">
                                        Rp <?= number_format($row['price'], 0, ',', '.') ?>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-sm font-bold <?= $isLow ? 'text-red-600' : 'text-gray-800' ?>"><?= $row['stock'] ?></span>
                                            <?php if($isLow): ?>
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-600 animate-pulse mt-1">Stok Tipis</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button onclick="openEditModal(this)" 
                                                data-id="<?= $row['id'] ?>"
                                                data-name="<?= htmlspecialchars($row['name']) ?>"
                                                data-cat="<?= $row['category_id'] ?>"
                                                data-price="<?= $row['price'] ?>"
                                                data-stock="<?= $row['stock'] ?>"
                                                class="w-9 h-9 rounded-xl flex items-center justify-center text-gray-400 hover:text-blue-500 hover:bg-blue-50 hover:shadow-md transition-all duration-300" title="Edit Barang">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>

                                            <button onclick="confirmDelete(<?= $row['id'] ?>)" 
                                            class="w-9 h-9 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 hover:shadow-md transition-all duration-300" title="Hapus Barang">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="py-12 text-center text-gray-400">
                                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        </div>
                                        <p class="font-medium">Belum ada barang di gudang</p>
                                    </td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="editBackdrop"></div>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl transform scale-95 opacity-0 transition-all duration-300 overflow-hidden" id="editPanel">
                <div class="p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                        Edit Barang
                    </h3>

                    <form action="../../process/barang_handler.php?act=update" method="POST" class="space-y-4">
                        <input type="hidden" name="id" id="edit_id"> <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Nama Produk</label>
                            <input type="text" name="name" id="edit_name" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Kategori</label>
                            <div class="relative">
                                <select name="category_id" id="edit_category_id" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none" required>
                                    <?php
                                        // Reset pointer data kategori karena sudah dipakai di form Input
                                        mysqli_data_seek($cat_query, 0); 
                                        while($cat = mysqli_fetch_assoc($cat_query)) {
                                            echo "<option value='".$cat['id']."'>".$cat['name']."</option>";
                                        }
                                    ?>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Harga (Rp)</label>
                                <input type="number" name="price" id="edit_price" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Stok</label>
                                <input type="number" name="stock" id="edit_stock" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                        </div>

                        <div class="flex gap-3 mt-6">
                            <button type="button" onclick="closeEditModal()" class="flex-1 py-3 rounded-xl border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors">Batal</button>
                            <button type="submit" class="flex-1 py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors shadow-lg shadow-blue-200">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="categoryModal" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="catBackdrop"></div>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl transform scale-95 opacity-0 transition-all duration-300 overflow-hidden" id="catPanel">
                <div class="p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        </div>
                        Tambah Kategori
                    </h3>
                    <form action="../../process/barang_handler.php?act=add_category" method="POST">
                        <div class="mb-6">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Nama Kategori</label>
                            <input type="text" name="category_name" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Misal: Elektronik, Makanan" required>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" onclick="closeModalCategory()" class="flex-1 py-3 rounded-xl border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors">Batal</button>
                            <button type="submit" class="flex-1 py-3 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-200">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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

                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Konfirmasi Keluar</h3>
                    <p class="text-gray-500 text-sm mb-8 leading-relaxed">
                        Anda yakin ingin mengakhiri sesi ini? <br>
                        Anda harus login kembali untuk mengakses panel gudang.
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
        // 1. Search Logic
        function searchTable() {
            let input = document.getElementById("searchInput");
            let filter = input.value.toUpperCase();
            let table = document.getElementById("inventoryTable");
            let tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                let td = tr[i].getElementsByClassName("productName")[0];
                if (td) {
                    let txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }       
            }
        }

        // 2. LOGOUT MODAL LOGIC
        const logoutModal = document.getElementById('logoutModal');
        const logoutBackdrop = document.getElementById('logoutBackdrop');
        const logoutPanel = document.getElementById('logoutPanel');

        function confirmLogout() {
            logoutModal.classList.remove('hidden');
            setTimeout(() => {
                logoutModal.classList.add('modal-enter-active');
                logoutBackdrop.classList.remove('opacity-0');
                logoutPanel.classList.remove('scale-95', 'opacity-0');
                logoutPanel.classList.remove('scale-100', 'opacity-100');
            }, 10);
        }

        function closeLogoutModal() {
            logoutBackdrop.classList.add('opacity-0');
            logoutPanel.classList.remove('scale-100', 'opacity-100');
            logoutPanel.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                logoutModal.classList.remove('modal-enter-active');
                logoutModal.classList.add('hidden');
            }, 300);
        }

        // 3. CATEGORY MODAL LOGIC
        const catModal = document.getElementById('categoryModal');
        const catBackdrop = document.getElementById('catBackdrop');
        const catPanel = document.getElementById('catPanel');

        function openModalCategory() {
            catModal.classList.remove('hidden');
            setTimeout(() => {
                catBackdrop.classList.remove('opacity-0');
                catPanel.classList.remove('scale-95', 'opacity-0');
                catPanel.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeModalCategory() {
            catBackdrop.classList.add('opacity-0');
            catPanel.classList.remove('scale-100', 'opacity-100');
            catPanel.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                catModal.classList.add('hidden');
            }, 300);
        }

        // 4. Notification Logic (Delete)
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus barang ini?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl',
                    cancelButton: 'rounded-xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `../../process/barang_handler.php?act=delete&id=${id}`;
                }
            })
        }

        // 5. EDIT MODAL LOGIC (BARU)
        const editModal = document.getElementById('editModal');
        const editBackdrop = document.getElementById('editBackdrop');
        const editPanel = document.getElementById('editPanel');

        function openEditModal(button) {
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const cat = button.getAttribute('data-cat');
            const price = button.getAttribute('data-price');
            const stock = button.getAttribute('data-stock');

            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_category_id').value = cat;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_stock').value = stock;

            editModal.classList.remove('hidden');
            setTimeout(() => {
                editBackdrop.classList.remove('opacity-0');
                editPanel.classList.remove('scale-95', 'opacity-0');
                editPanel.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeEditModal() {
            editBackdrop.classList.add('opacity-0');
            editPanel.classList.remove('scale-100', 'opacity-100');
            editPanel.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                editModal.classList.add('hidden');
            }, 300);
        }

        // 6. GLOBAL NOTIFICATION HANDLER (UPDATED)
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.has('status')) {
            const status = urlParams.get('status');
            const msg = urlParams.get('msg');

            if (status === 'success') {
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Data barang berhasil ditambahkan.', customClass: { popup: 'rounded-3xl' } });
            } else if (status === 'updated') {
                Swal.fire({ icon: 'success', title: 'Diperbarui!', text: 'Data barang berhasil diubah.', customClass: { popup: 'rounded-3xl' } });
            } else if (status === 'success_cat') {
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Kategori baru berhasil ditambahkan.', customClass: { popup: 'rounded-3xl' } });
            } else if (status === 'error') {
                // Tampilkan pesan error spesifik dari backend
                Swal.fire({ icon: 'error', title: 'Gagal!', text: msg || 'Terjadi kesalahan.', customClass: { popup: 'rounded-3xl' } });
            }

            // Bersihkan URL
            window.history.replaceState(null, null, window.location.pathname);
        }
        
        if (urlParams.has('msg') && urlParams.get('msg') === 'deleted') {
            Swal.fire({ icon: 'success', title: 'Terhapus!', text: 'Data barang telah dihapus.', customClass: { popup: 'rounded-3xl' } });
            window.history.replaceState(null, null, window.location.pathname);
        }
    </script>

</body>
</html>
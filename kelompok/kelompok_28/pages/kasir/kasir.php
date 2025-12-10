<?php 
// FILE: pages/kasir/dashboard.php

// Cek Session & Load Backend
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../../process/process_cashier.php'; 

$trx_data = isset($_SESSION['success_trx']) ? $_SESSION['success_trx'] : null;
if ($trx_data) { unset($_SESSION['success_trx']); }

$products = $products ?? []; 
$page_title = "Kasir - " . ($_SESSION['fullname'] ?? 'Staff');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #f8fafc; 
            overflow-x: hidden; 
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.5);
        }
        
        .glass-panel {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-left: 1px solid rgba(255, 255, 255, 0.6);
        }
        
        .card-gradient {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
        }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes scaleIn { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.7; }
            100% { transform: scale(0.95); opacity: 1; }
        }

        .animate-fadeIn { animation: fadeIn 0.4s ease-out forwards; }
        .animate-slideUp { animation: slideUp 0.5s ease-out forwards; }
        .animate-scaleIn { animation: scaleIn 0.3s ease-out forwards; }
        .animate-blob { animation: blob 7s infinite; }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }

        .hover-lift { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1); }
        .active-press:active { transform: scale(0.98); }
        
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        
        .cart-item {
            transition: all 0.3s ease;
        }
        
        .cart-item.removing {
            opacity: 0;
            transform: translateX(100px);
        }
        
        .btn-loading {
            position: relative;
            pointer-events: none;
        }
        
        .btn-loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 0.6s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .toast {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 9999;
            min-width: 300px;
            transform: translateX(-400px);
            transition: transform 0.3s ease;
        }
        
        .toast.show {
            transform: translateX(0);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col h-screen overflow-hidden text-gray-800">

    <!-- Background Blobs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 left-0 w-96 h-96 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-32 left-20 w-96 h-96 bg-pink-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
    </div>

    <!-- Navigation -->
    <nav class="glass-effect px-6 py-3 flex justify-between items-center shadow-sm z-50 shrink-0 h-[74px] relative">
        <div class="flex items-center gap-4">
            <div class="relative group">
                <div class="absolute -inset-0.5 bg-gradient-to-r from-purple-600 to-blue-600 rounded-xl blur opacity-30 group-hover:opacity-60 transition duration-200"></div>
                <div class="relative w-11 h-11 bg-white rounded-xl flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
            <div>
                <h1 class="font-bold text-gray-800 text-xl leading-tight tracking-tight">Kasir</h1>
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </span>
                    <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">Online</p>
                </div>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="hidden md:block text-right mr-2">
                <p class="text-sm font-bold text-gray-800"><?= htmlspecialchars($_SESSION['fullname'] ?? 'Kasir') ?></p>
                <p class="text-xs text-gray-500 font-medium" id="currentDateTime"><?= date('l, d M Y') ?></p>
            </div>
            
            <a href="dashboard_kasir.php" class="p-3 rounded-xl bg-white/50 border border-gray-200 text-gray-600 hover:bg-white hover:text-indigo-600 hover:shadow-lg transition-all duration-300 group" title="Dashboard">
                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            </a>
            
            <a href="../../auth/logout.php" class="p-3 rounded-xl bg-red-50 border border-red-100 text-red-500 hover:bg-red-500 hover:text-white hover:shadow-lg transition-all duration-300 group" title="Logout">
                <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </a>
        </div>
    </nav>

    <div class="flex-1 flex overflow-hidden relative z-10">
        
        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0 p-4 lg:p-6 lg:pr-4">
            
            <!-- Search Bar -->
            <div class="glass-effect p-2 rounded-2xl shadow-lg shadow-gray-200/50 mb-6 shrink-0 animate-slideUp">
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="searchInput" autocomplete="off" autofocus placeholder="Cari nama barang, kategori, atau kode (Tekan F2)" 
                           class="block w-full pl-12 pr-12 py-3.5 bg-transparent border border-transparent rounded-xl focus:bg-white/50 focus:border-indigo-200 focus:ring-4 focus:ring-indigo-500/10 transition-all font-medium text-gray-700 placeholder-gray-400 outline-none">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-2 pointer-events-none">
                        <span id="searchResultCount" class="hidden text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-lg"></span>
                        <kbd class="hidden sm:inline-block px-2.5 py-1 bg-gray-100 border border-gray-200 rounded-lg text-xs font-bold text-gray-500 shadow-sm">F2</kbd>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="flex-1 overflow-y-auto custom-scrollbar pr-2 pb-20">
                <?php if (empty($products)): ?>
                    <div class="h-full flex flex-col items-center justify-center text-center animate-fadeIn">
                        <div class="w-32 h-32 bg-gray-100 rounded-full flex items-center justify-center mb-6 shadow-inner">
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-700">Produk Kosong</h3>
                        <p class="text-sm text-gray-500 mt-2 max-w-xs">Data produk belum tersedia. Silakan tambahkan produk melalui dashboard owner.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5" id="productGrid">
                        <?php foreach ($products as $index => $p): 
                            $is_habis = $p['stock'] <= 0;
                            $is_low_stock = $p['stock'] > 0 && $p['stock'] <= 5;
                            $search_key = strtolower($p['name'] . ' ' . ($p['category_name'] ?? '') . ' ' . ($p['code'] ?? ''));
                            $delay = min($index * 50, 1000); 
                        ?>
                            <div class="product-item-wrapper group relative animate-fadeIn" 
                                 style="animation-delay: <?= $delay ?>ms"
                                 data-search="<?= htmlspecialchars($search_key) ?>"
                                 data-id="<?= $p['id'] ?>">
                                
                                <button type="button" onclick='addToCart(<?= $p['id'] ?>, <?= json_encode($p['name'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= $p['price'] ?>, <?= $p['stock'] ?>)'
                                        <?= $is_habis ? 'disabled' : '' ?>
                                        class="w-full text-left card-gradient p-5 rounded-[1.5rem] border border-white/60 shadow-sm hover-lift active-press flex flex-col h-full relative overflow-hidden ring-1 ring-gray-50 <?= $is_habis ? 'opacity-60 cursor-not-allowed grayscale' : '' ?>">
                                    
                                    <div class="absolute -top-10 -right-10 w-24 h-24 bg-gradient-to-br from-indigo-50 to-blue-50 rounded-full opacity-60 group-hover:scale-150 transition-transform duration-500"></div>

                                    <div class="relative z-10 flex justify-between items-start mb-3 w-full">
                                        <span class="text-[10px] font-extrabold tracking-wider uppercase text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100">
                                            <?= htmlspecialchars($p['category_name'] ?? 'ITEM') ?>
                                        </span>
                                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-lg shadow-sm <?= $is_habis ? 'bg-red-500 text-white' : ($is_low_stock ? 'bg-orange-500 text-white' : 'bg-white text-gray-500 border border-gray-100') ?>">
                                            <?= $is_habis ? 'HABIS' : 'Stok: ' . $p['stock'] ?>
                                        </span>
                                    </div>

                                    <h3 class="relative z-10 font-bold text-gray-800 text-sm leading-snug mb-4 line-clamp-2 min-h-[40px] group-hover:text-indigo-600 transition-colors duration-300">
                                        <?= htmlspecialchars($p['name']) ?>
                                    </h3>

                                    <div class="relative z-10 mt-auto flex items-center justify-between border-t border-dashed border-gray-100 pt-3 w-full">
                                        <span class="font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-gray-900 to-gray-600 text-base">
                                            <?= number_format($p['price'], 0, ',', '.') ?>
                                        </span>
                                        <div class="w-9 h-9 rounded-full bg-white text-indigo-600 flex items-center justify-center shadow-sm border border-gray-100 group-hover:bg-gradient-to-r group-hover:from-indigo-600 group-hover:to-blue-600 group-hover:text-white group-hover:border-transparent transition-all duration-300 transform group-hover:rotate-90">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        </div>
                                    </div>
                                    
                                    <?php if($is_habis): ?>
                                        <div class="absolute inset-0 bg-white/40 backdrop-blur-[2px] flex items-center justify-center z-20">
                                            <span class="transform -rotate-12 border-2 border-red-500 text-red-500 font-black px-3 py-1 rounded-lg text-xs uppercase bg-white shadow-lg">Sold Out</span>
                                        </div>
                                    <?php endif; ?>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div id="noSearchResults" class="hidden flex-col items-center justify-center py-20 animate-fadeIn">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <p class="text-gray-500 font-semibold text-lg">Produk tidak ditemukan</p>
                        <p class="text-gray-400 text-sm mb-4">Coba kata kunci lain atau reset pencarian</p>
                        <button onclick="resetSearch()" class="px-6 py-2 bg-white border border-indigo-200 text-indigo-600 rounded-xl font-bold hover:bg-indigo-50 transition-colors shadow-sm">
                            Reset Pencarian
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </main>

        <!-- Cart Sidebar -->
        <aside class="w-full lg:w-[420px] z-40 flex flex-col glass-panel h-full absolute lg:relative right-0 transition-transform duration-300 transform translate-x-full lg:translate-x-0 shadow-2xl" id="cartPanel">
            
            <button type="button" onclick="toggleCart()" class="lg:hidden absolute -left-14 top-6 bg-gradient-to-r from-purple-600 to-blue-600 text-white p-3 rounded-l-2xl shadow-lg hover:pr-4 transition-all group">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span id="mobileCartBadge" class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center hidden group-hover:scale-110 transition-transform">0</span>
            </button>

            <div class="px-6 py-5 border-b border-gray-200/50 bg-white/50 backdrop-blur-md flex justify-between items-center shrink-0">
                <h2 class="font-extrabold text-xl text-gray-800 flex items-center gap-2">
                    <span class="text-2xl">🛒</span> Keranjang
                    <span id="cartCountBadge" class="bg-gradient-to-r from-purple-500 to-blue-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full hidden animate-scaleIn shadow-md">0</span>
                </h2>
                <button type="button" onclick="clearCart()" class="text-xs font-bold text-red-500 hover:text-white hover:bg-red-500 px-3 py-1.5 rounded-lg transition-all duration-300 border border-transparent hover:border-red-500 bg-red-50">
                    Reset
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-5 space-y-3 custom-scrollbar bg-gray-50/50" id="cartItemsContainer">
                <div id="emptyCartState" class="h-full flex flex-col items-center justify-center text-center opacity-50 animate-fadeIn">
                    <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-white rounded-full flex items-center justify-center mb-4 shadow-sm">
                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                    <p class="font-bold text-gray-600 text-lg">Keranjang Kosong</p>
                    <p class="text-sm text-gray-400 mt-1 max-w-[200px]">Pilih produk di sebelah kiri untuk memulai transaksi.</p>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-xl border-t border-gray-200 p-6 z-20 shadow-[0_-10px_40px_rgba(0,0,0,0.05)]">
                
                <div class="flex justify-between items-end mb-5">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Tagihan</span>
                    <span class="text-3xl font-black bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent" id="cartTotalDisplay">Rp 0</span>
                </div>

                <form action="../../process/process_cashier.php" method="POST" onsubmit="return handleSubmit(event)" id="transactionForm">
                    
                    <div class="grid grid-cols-4 gap-2 mb-4">
                        <button type="button" onclick="setPayment('exact')" class="text-[10px] font-bold py-2.5 bg-gray-100 hover:bg-blue-50 hover:text-blue-600 rounded-xl border border-transparent hover:border-blue-200 transition-all">Uang Pas</button>
                        <button type="button" onclick="addMoney(10000)" class="text-[10px] font-bold py-2.5 bg-gray-100 hover:bg-emerald-50 hover:text-emerald-600 rounded-xl border border-transparent hover:border-emerald-200 transition-all">+10k</button>
                        <button type="button" onclick="addMoney(50000)" class="text-[10px] font-bold py-2.5 bg-gray-100 hover:bg-emerald-50 hover:text-emerald-600 rounded-xl border border-transparent hover:border-emerald-200 transition-all">+50k</button>
                        <button type="button" onclick="addMoney(100000)" class="text-[10px] font-bold py-2.5 bg-gray-100 hover:bg-emerald-50 hover:text-emerald-600 rounded-xl border border-transparent hover:border-emerald-200 transition-all">+100k</button>
                    </div>

                    <div class="relative mb-4 group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-gray-400 font-bold group-focus-within:text-indigo-600 transition-colors">Rp</span>
                        </div>
                        <input type="text" id="payAmountDisplay" onkeyup="handlePayInput(this)" onpaste="handlePaste(event)" placeholder="0" 
                               class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-xl font-bold text-gray-800 text-xl text-right focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all shadow-inner">
                    </div>

                    <div class="flex justify-between items-center px-4 py-3 bg-gray-50 rounded-xl border border-gray-100 border-dashed mb-5">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Kembalian</span>
                        <span class="font-extrabold text-lg text-gray-400 transition-colors duration-300" id="changeAmountDisplay">Rp 0</span>
                    </div>

                    <input type="hidden" name="process_transaction" value="1">
                    <input type="hidden" name="cart_data" id="cartDataInput">
                    <input type="hidden" name="total_amount" id="totalAmountInput">
                    <input type="hidden" name="pay_amount" id="payAmountInput">
                    
                    <button type="submit" id="btnProcess" disabled class="group relative w-full py-4 rounded-xl font-bold text-white shadow-none flex items-center justify-center gap-3 transition-all duration-300 transform active:scale-[0.98] bg-gray-200 cursor-not-allowed overflow-hidden">
                        <span class="relative z-10 flex items-center gap-2" id="btnProcessText">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            PROSES PEMBAYARAN
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </button>
                </form>
            </div>
        </aside>

        <div onclick="toggleCart()" id="cartOverlay" class="lg:hidden fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-30 hidden transition-opacity duration-300"></div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-900/60 backdrop-blur-md transition-opacity opacity-0">
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-sm mx-4 overflow-hidden transform scale-90 transition-all duration-300 relative" id="modalContent">
            <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-b from-green-50 to-white z-0"></div>

            <div class="relative z-10 p-8 text-center">
                <div class="w-24 h-24 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-green-200/50 animate-scaleIn">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h2 class="text-3xl font-black text-gray-800 mb-2 tracking-tight">Sukses!</h2>
                <p class="text-gray-500 text-sm font-medium">Transaksi berhasil disimpan.</p>
            </div>
            
            <div class="px-8 pb-8 relative z-10">
                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5 mb-6 relative overflow-hidden">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-green-400 to-emerald-500"></div>
                    <div class="flex justify-between mb-3 text-sm text-gray-500 border-b border-gray-200 pb-3">
                        <span>Total Belanja</span>
                        <span class="font-bold text-gray-800" id="modalTotal">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Kembalian</span>
                        <span class="font-extrabold text-xl text-green-600" id="modalChange">Rp 0</span>
                    </div>
                </div>
                <button onclick="closeSuccessModal()" class="w-full py-4 bg-gradient-to-r from-gray-800 to-gray-900 text-white font-bold rounded-xl hover:shadow-lg hover:scale-[1.02] transition-all duration-200">
                    Tutup & Transaksi Baru
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toastNotification" class="toast">
        <div class="bg-white rounded-xl shadow-2xl p-4 flex items-center gap-3 border-l-4" id="toastContent">
            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" id="toastIcon">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div class="flex-1">
                <p class="font-bold text-gray-800 text-sm" id="toastTitle">Notification</p>
                <p class="text-xs text-gray-500" id="toastMessage">Message here</p>
            </div>
        </div>
    </div>

<script>
    let cart = [];
    let currentTotal = 0;
    let searchDebounce = null;

    const formatRupiah = (num) => 'Rp ' + parseInt(num).toLocaleString('id-ID');
    const parseRupiah = (str) => parseInt(str.replace(/[^0-9]/g, '')) || 0;

    function showToast(title, message, type = 'success') {
        const toast = document.getElementById('toastNotification');
        const toastContent = document.getElementById('toastContent');
        const toastIcon = document.getElementById('toastIcon');
        const toastTitle = document.getElementById('toastTitle');
        const toastMessage = document.getElementById('toastMessage');

        // Set content
        toastTitle.innerText = title;
        toastMessage.innerText = message;

        // Set style based on type
        if (type === 'success') {
            toastContent.classList.remove('border-red-500', 'border-orange-500');
            toastContent.classList.add('border-green-500');
            toastIcon.classList.remove('bg-red-100', 'bg-orange-100');
            toastIcon.classList.add('bg-green-100');
            toastIcon.innerHTML = '<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        } else if (type === 'error') {
            toastContent.classList.remove('border-green-500', 'border-orange-500');
            toastContent.classList.add('border-red-500');
            toastIcon.classList.remove('bg-green-100', 'bg-orange-100');
            toastIcon.classList.add('bg-red-100');
            toastIcon.innerHTML = '<svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
        } else if (type === 'warning') {
            toastContent.classList.remove('border-green-500', 'border-red-500');
            toastContent.classList.add('border-orange-500');
            toastIcon.classList.remove('bg-green-100', 'bg-red-100');
            toastIcon.classList.add('bg-orange-100');
            toastIcon.innerHTML = '<svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
        }

        // Show toast
        toast.classList.add('show');

        // Auto hide after 3 seconds
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    const searchInput = document.getElementById('searchInput');
    const productGrid = document.getElementById('productGrid');
    const noResults = document.getElementById('noSearchResults');
    const searchResultCount = document.getElementById('searchResultCount');
    
    function triggerSearch() {
        const term = searchInput.value.toLowerCase().trim();
        const items = document.querySelectorAll('.product-item-wrapper');
        let visibleCount = 0;

        items.forEach(item => {
            const keys = item.getAttribute('data-search');
            if (keys.includes(term)) {
                item.style.display = 'block';
                item.style.animation = 'none';
                item.offsetHeight; /* trigger reflow */
                item.style.animation = 'fadeIn 0.4s ease-out forwards';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Update result count
        if (term && visibleCount > 0) {
            searchResultCount.innerText = `${visibleCount} hasil`;
            searchResultCount.classList.remove('hidden');
        } else {
            searchResultCount.classList.add('hidden');
        }

        // Show/hide elements
        if (visibleCount > 0) {
            productGrid.classList.remove('hidden');
            if(noResults) noResults.classList.add('hidden');
        } else if (term) {
            productGrid.classList.add('hidden');
            if(noResults) {
                noResults.classList.remove('hidden');
                noResults.style.display = 'flex';
            }
        }
    }
    
    // Debounced search for better performance
    if(searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(searchDebounce);
            searchDebounce = setTimeout(triggerSearch, 150);
        });
    }

    function resetSearch() {
        searchInput.value = '';
        searchResultCount.classList.add('hidden');
        triggerSearch();
        searchInput.focus();
    }

    function addToCart(id, name, price, stock) {
        // Type safety
        id = parseInt(id); 
        stock = parseInt(stock);
        price = parseFloat(price);

        // Check existing item
        const existing = cart.find(i => i.id === id);
        
        if (existing) {
            if (existing.qty >= stock) {
                showToast('Stok Habis', `Maksimal ${stock} item untuk ${name}`, 'warning');
                return;
            }
            existing.qty++;
            showToast('Item Ditambah', `${name} (${existing.qty}x)`, 'success');
        } else {
            if(stock <= 0) {
                showToast('Stok Habis', `${name} tidak tersedia`, 'error');
                return;
            }
            cart.push({ id, name, price, qty: 1, stock });
            showToast('Ditambahkan', `${name} masuk keranjang`, 'success');
        }
        
        renderCart();
        
        const container = document.getElementById('cartItemsContainer');
        setTimeout(() => container.scrollTop = container.scrollHeight, 50);
        
        if(window.innerWidth < 1024) {
            openCart();
        }
    }

    function updateQty(id, delta) {
        id = parseInt(id);
        const item = cart.find(i => i.id === id);
        
        if (!item) return;

        const newQty = item.qty + delta;
        
        if (newQty > item.stock) {
            showToast('Stok Terbatas', `Maksimal ${item.stock} item`, 'warning');
            return;
        }
        
        if (newQty <= 0) {
            removeFromCart(id, item.name);
        } else {
            item.qty = newQty;
            renderCart();
        }
    }

    function removeFromCart(id, name) {
        if (confirm(`Hapus ${name} dari keranjang?`)) {
            const itemElement = document.querySelector(`[data-cart-id="${id}"]`);
            if (itemElement) {
                itemElement.classList.add('removing');
                setTimeout(() => {
                    cart = cart.filter(i => i.id !== id);
                    renderCart();
                    showToast('Item Dihapus', `${name} telah dihapus`, 'success');
                }, 300);
            }
        }
    }

    function renderCart() {
        const container = document.getElementById('cartItemsContainer');
        const emptyState = document.getElementById('emptyCartState');
        const badge = document.getElementById('cartCountBadge');
        const mobileBadge = document.getElementById('mobileCartBadge');
        const totalDisplay = document.getElementById('cartTotalDisplay');
        
        // Clear container
        container.innerHTML = '';
        currentTotal = 0;

        if (cart.length === 0) {
            if(emptyState) {
                container.appendChild(emptyState);
                emptyState.classList.remove('hidden');
            }
            badge.classList.add('hidden');
            mobileBadge.classList.add('hidden');
        } else {
            if(emptyState) emptyState.classList.add('hidden');
            badge.innerText = cart.length;
            badge.classList.remove('hidden');
            mobileBadge.innerText = cart.length;
            mobileBadge.classList.remove('hidden');

            cart.forEach(item => {
                const subtotal = item.price * item.qty;
                currentTotal += subtotal;
                
                const html = `
                    <div class="cart-item bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm flex justify-between items-center group hover:shadow-md transition-all duration-200" data-cart-id="${item.id}">
                        <div class="flex-1 min-w-0 pr-3">
                            <h4 class="font-bold text-gray-800 text-sm truncate leading-tight" title="${item.name}">${item.name}</h4>
                            <p class="text-[11px] text-gray-400 mt-1 font-medium">@ Rp ${parseInt(item.price).toLocaleString('id-ID')}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex items-center bg-gray-100 rounded-lg p-1">
                                <button type="button" onclick="updateQty(${item.id}, -1)" class="w-6 h-6 flex items-center justify-center bg-white rounded-md shadow-sm text-gray-500 hover:text-red-500 font-bold transition-all hover:scale-110 active:scale-90">-</button>
                                <span class="text-xs font-bold w-8 text-center text-gray-700 select-none">${item.qty}</span>
                                <button type="button" onclick="updateQty(${item.id}, 1)" class="w-6 h-6 flex items-center justify-center bg-white rounded-md shadow-sm text-indigo-600 hover:bg-indigo-50 font-bold transition-all hover:scale-110 active:scale-90">+</button>
                            </div>
                            <div class="text-right w-[90px]">
                                <span class="block font-bold text-indigo-600 text-sm">Rp ${parseInt(subtotal).toLocaleString('id-ID')}</span>
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', html);
            });
        }

        // Update total
        totalDisplay.innerText = formatRupiah(currentTotal);
        calculateChange();
    }

    function clearCart() {
        if (cart.length === 0) { 
            showToast('Info', 'Keranjang sudah kosong', 'warning');
            return; 
        }
        if (confirm('Yakin ingin mengosongkan keranjang?')) {
            cart = [];
            renderCart();
            document.getElementById('payAmountDisplay').value = '';
            calculateChange();
            showToast('Keranjang Dikosongkan', 'Semua item telah dihapus', 'success');
        }
    }

    const payInputDisplay = document.getElementById('payAmountDisplay');
    const payInputHidden = document.getElementById('payAmountInput');

    function handlePayInput(el) {
        let val = el.value.replace(/[^0-9]/g, '');
        if (val === '') { 
            calculateChange(); 
            return; 
        } 
        el.value = parseInt(val).toLocaleString('id-ID');
        calculateChange();
    }

    function handlePaste(event) {
        event.preventDefault();
        const pastedText = (event.clipboardData || window.clipboardData).getData('text');
        const numbers = pastedText.replace(/[^0-9]/g, '');
        if (numbers) {
            document.getElementById('payAmountDisplay').value = parseInt(numbers).toLocaleString('id-ID');
            calculateChange();
        }
    }

    function addMoney(amount) {
        let currentRaw = parseRupiah(payInputDisplay.value);
        let newVal = currentRaw + amount;
        payInputDisplay.value = newVal.toLocaleString('id-ID');
        calculateChange();
        showToast('Uang Ditambah', `+Rp ${amount.toLocaleString('id-ID')}`, 'success');
    }

    function setPayment(type) {
        if (type === 'exact') {
            if (currentTotal === 0) {
                showToast('Keranjang Kosong', 'Tambahkan produk terlebih dahulu', 'warning');
                return;
            }
            payInputDisplay.value = currentTotal.toLocaleString('id-ID');
            calculateChange();
            showToast('Uang Pas', 'Pembayaran sesuai total', 'success');
        }
    }

    function calculateChange() {
        const payVal = parseRupiah(payInputDisplay.value);
        const change = payVal - currentTotal;
        const changeDisplay = document.getElementById('changeAmountDisplay');
        const btn = document.getElementById('btnProcess');

        // Update hidden inputs
        document.getElementById('cartDataInput').value = JSON.stringify(cart);
        document.getElementById('totalAmountInput').value = currentTotal;
        payInputHidden.value = payVal;

        if (cart.length > 0 && payVal >= currentTotal) {
            changeDisplay.innerText = formatRupiah(change);
            changeDisplay.classList.remove('text-red-500', 'text-gray-400');
            changeDisplay.classList.add('text-green-600');
            
            btn.disabled = false;
            btn.classList.remove('bg-gray-200', 'cursor-not-allowed');
            btn.classList.add('bg-gradient-to-r', 'from-purple-600', 'to-blue-600', 'hover:shadow-lg');
        } else {
            let diff = Math.abs(change);
            changeDisplay.innerText = (currentTotal > 0) ? `Kurang Rp ${diff.toLocaleString('id-ID')}` : 'Rp 0';
            changeDisplay.classList.add(currentTotal > 0 ? 'text-red-500' : 'text-gray-400');
            changeDisplay.classList.remove('text-green-600');
            
            btn.disabled = true;
            btn.classList.add('bg-gray-200', 'cursor-not-allowed');
            btn.classList.remove('bg-gradient-to-r', 'from-purple-600', 'to-blue-600', 'hover:shadow-lg');
        }
    }

    function handleSubmit(event) {
        if (cart.length === 0) { 
            event.preventDefault();
            showToast('Keranjang Kosong', 'Tambahkan produk terlebih dahulu', 'error');
            return false; 
        }
        if (parseRupiah(payInputDisplay.value) < currentTotal) {
            event.preventDefault();
            showToast('Uang Kurang', 'Pembayaran tidak mencukupi', 'error');
            return false;
        }

        const btn = document.getElementById('btnProcess');
        const btnText = document.getElementById('btnProcessText');
        btn.classList.add('btn-loading');
        btn.disabled = true;
        btnText.innerHTML = 'Memproses...';

        return true;
    }

    function toggleCart() {
        const panel = document.getElementById('cartPanel');
        const overlay = document.getElementById('cartOverlay');
        panel.classList.toggle('translate-x-full');
        overlay.classList.toggle('hidden');
    }

    function openCart() {
        const panel = document.getElementById('cartPanel');
        const overlay = document.getElementById('cartOverlay');
        panel.classList.remove('translate-x-full');
        overlay.classList.remove('hidden');
    }

    function closeCart() {
        const panel = document.getElementById('cartPanel');
        const overlay = document.getElementById('cartOverlay');
        panel.classList.add('translate-x-full');
        overlay.classList.add('hidden');
    }

    const successData = <?= json_encode($trx_data) ?>;
    if (successData) {
        const modal = document.getElementById('successModal');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            document.getElementById('modalContent').classList.remove('scale-90');
            document.getElementById('modalContent').classList.add('scale-100');
        }, 10);
        
        document.getElementById('modalTotal').innerText = formatRupiah(successData.total);
        document.getElementById('modalChange').innerText = formatRupiah(successData.change);

    }

    function closeSuccessModal() {
        const modal = document.getElementById('successModal');
        const modalContent = document.getElementById('modalContent');
        
        modalContent.classList.add('scale-90');
        modalContent.classList.remove('scale-100');
        modal.classList.add('opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            // Reset form
            cart = [];
            renderCart();
            document.getElementById('payAmountDisplay').value = '';
            calculateChange();
            closeCart();
        }, 300);
    }

    document.addEventListener('keydown', (e) => {
        // F2: Focus search
        if (e.key === 'F2') { 
            e.preventDefault(); 
            searchInput.focus(); 
        }
        
        if (e.key === 'Escape') {
            if (!document.getElementById('successModal').classList.contains('hidden')) {
                closeSuccessModal();
            } else if (window.innerWidth < 1024) {
                closeCart();
            }
        }

        if (e.key === 'Enter' && e.target.id === 'payAmountDisplay') {
            const btn = document.getElementById('btnProcess');
            if (!btn.disabled) {
                document.getElementById('transactionForm').requestSubmit();
            }
        }
    });

    function updateDateTime() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
        const dateStr = now.toLocaleDateString('id-ID', options);
        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        
        const dateTimeElement = document.getElementById('currentDateTime');
        if (dateTimeElement) {
            dateTimeElement.innerText = `${dateStr} • ${timeStr}`;
        }
    }

    // Update every second
    setInterval(updateDateTime, 1000);
    updateDateTime();


    document.addEventListener('DOMContentLoaded', function() {
        // Focus search on load
        if (searchInput && window.innerWidth >= 1024) {
            setTimeout(() => searchInput.focus(), 100);
        }

        // Log system info
        console.log('%c🚀 DigiNiaga POS System', 'color: #4f46e5; font-size: 20px; font-weight: bold;');
        console.log('%cVersion: 2.0.0 | Optimized Build', 'color: #6366f1; font-size: 12px;');
        console.log('%c⚡ Performance Mode: Active', 'color: #10b981; font-size: 12px; font-weight: bold;');
    });

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            // navigator.serviceWorker.register('/sw.js').catch(() => {});
        });
    }
</script>

</body>
</html>
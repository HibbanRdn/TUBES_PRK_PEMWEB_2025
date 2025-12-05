<?php
session_start();
// Cek jika user sudah login, langsung lempar ke dashboard
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: pages/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - DigiNiaga POS</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Menggunakan Style yang sama persis dengan Login Page */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-fadeInUp { animation: fadeInUp 0.8s ease-out forwards; }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transition: all 0.3s ease;
        }
        .btn-gradient:hover {
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.4);
            transform: translateY(-2px);
        }

        .btn-glass {
            background: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(37, 99, 235, 0.2);
            color: #1d4ed8;
            transition: all 0.3s ease;
        }
        .btn-glass:hover {
            background: rgba(255, 255, 255, 0.9);
            border-color: #2563eb;
            transform: translateY(-2px);
        }
        
        .pattern-dots {
            background-image: radial-gradient(circle, #e0e7ff 1px, transparent 1px);
            background-size: 30px 30px;
        }

        /* Abstract Shapes untuk Background */
        .blob {
            position: absolute;
            filter: blur(50px);
            z-index: -1;
            opacity: 0.6;
            animation: float 10s infinite ease-in-out;
        }
        .blob-1 { top: -10%; left: -10%; width: 500px; height: 500px; background: #dbeafe; animation-delay: 0s; }
        .blob-2 { bottom: -10%; right: -10%; width: 500px; height: 500px; background: #bfdbfe; animation-delay: 2s; }
        .blob-3 { top: 40%; left: 40%; width: 300px; height: 300px; background: #e0e7ff; animation-delay: 4s; }
    </style>
</head>
<body class="bg-gray-50 h-screen w-full flex flex-col overflow-hidden relative font-sans">

    <div class="absolute inset-0 pattern-dots pointer-events-none"></div>
    <div class="blob blob-1 rounded-full"></div>
    <div class="blob blob-2 rounded-full"></div>
    <div class="blob blob-3 rounded-full"></div>

    <nav class="w-full p-6 flex justify-between items-center relative z-10 animate-fadeInUp">
        <div class="flex items-center gap-3">
            <img src="./assets/images/logo.png" alt="Logo DigiNiaga" class="h-12 w-auto object-contain">
        </div>
        <div>
            <a href="./auth/login.php" class="text-sm font-semibold text-gray-600 hover:text-brand-600 transition-colors">Bantuan</a>
        </div>
    </nav>

    <main class="flex-1 flex flex-col justify-center items-center px-4 sm:px-6 relative z-10">
        
        <div class="max-w-4xl w-full text-center space-y-8">
            
            <div class="space-y-4 animate-fadeInUp" style="animation-delay: 0.1s;">
                <div class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-brand-600 text-xs font-semibold mb-4">
                    <span class="flex h-2 w-2 relative mr-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-500"></span>
                    </span>
                    Solusi Point of Sale Modern #1
                </div>
                
                <h1 class="text-5xl md:text-7xl font-extrabold text-gray-900 tracking-tight leading-tight">
                    Kelola Bisnis <br>
                    <span class="gradient-text">Tanpa Batas.</span>
                </h1>
                
                <p class="text-lg md:text-xl text-gray-500 max-w-2xl mx-auto leading-relaxed">
                    Satu platform terintegrasi untuk Owner, Kasir, dan Gudang. Pantau stok real-time dan laporan penjualan di mana saja.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fadeInUp" style="animation-delay: 0.3s;">
                
                <a href="./auth/login.php" class="group relative w-full sm:w-auto">
                    <div class="absolute -inset-0.5 bg-gradient-to-r from-brand-600 to-brand-400 rounded-xl blur opacity-30 group-hover:opacity-75 transition duration-200"></div>
                    <button class="relative w-full sm:w-auto btn-gradient text-white font-bold rounded-xl px-8 py-4 flex items-center justify-center gap-3">
                        <span>Masuk ke Sistem</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </a>

                <a href="./auth/register.php" class="w-full sm:w-auto">
                    <button class="w-full sm:w-auto btn-glass font-bold rounded-xl px-8 py-4 flex items-center justify-center gap-3">
                        <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span>Daftar Toko Baru</span>
                    </button>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12 pt-12 border-t border-gray-200 animate-fadeInUp" style="animation-delay: 0.5s;">
                <div class="p-4 rounded-2xl glass-effect text-left">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-brand-600 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <h3 class="font-bold text-gray-800">Manajemen Stok</h3>
                    <p class="text-sm text-gray-500 mt-1">Kontrol inventaris real-time untuk gudang.</p>
                </div>
                <div class="p-4 rounded-2xl glass-effect text-left">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="font-bold text-gray-800">Transaksi Cepat</h3>
                    <p class="text-sm text-gray-500 mt-1">Point of Sales kasir yang responsif & mudah.</p>
                </div>
                <div class="p-4 rounded-2xl glass-effect text-left">
                    <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center text-teal-600 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h3 class="font-bold text-gray-800">Laporan Owner</h3>
                    <p class="text-sm text-gray-500 mt-1">Analisa performa toko secara mendalam.</p>
                </div>
            </div>

        </div>
    </main>

    <footer class="w-full p-6 text-center text-xs text-gray-400 animate-fadeInUp relative z-10">
        &copy; 2025 DigiNiaga System. All rights reserved.
    </footer>

</body>
</html>
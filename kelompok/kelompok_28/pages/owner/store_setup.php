<?php 
// FILE: store_setup.php
// Memanggil Backend Logic
require_once '../../process/process_store_setup.php'; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Usaha - DigiNiaga</title>
    
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
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-fadeInUp { animation: fadeInUp 0.8s ease-out forwards; }
        .animate-slideInLeft { animation: slideInLeft 0.8s ease-out forwards; }
        
        .gradient-text {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .input-glow:focus { box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
        
        .btn-gradient {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            position: relative;
            overflow: hidden;
        }
        
        .btn-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }
        
        .btn-gradient:hover::before { left: 100%; }
        
        .pattern-dots {
            background-image: radial-gradient(circle, rgba(255, 255, 255, 0.15) 1px, transparent 1px);
            background-size: 25px 25px;
        }
        
        .floating-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(40px);
            opacity: 0.3;
        }
        
        .shape-1 {
            width: 300px; height: 300px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            top: -100px; right: -100px;
            animation: float 8s ease-in-out infinite;
        }
        
        .shape-2 {
            width: 200px; height: 200px;
            background: linear-gradient(135deg, #1d4ed8, #1e3a8a);
            bottom: -50px; left: -50px;
            animation: float 10s ease-in-out infinite reverse;
        }

        /* Custom Scroll for Form Area */
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>

<body class="bg-gradient-to-br from-gray-50 to-blue-50 h-screen w-full flex overflow-hidden">

    <div class="hidden md:flex w-2/5 bg-gradient-to-br from-brand-900 via-brand-800 to-brand-900 flex-col justify-between p-12 relative overflow-hidden">
        <div class="absolute inset-0 pattern-dots"></div>
        <div class="absolute inset-0">
            <div class="floating-shape shape-1"></div>
            <div class="floating-shape shape-2"></div>
        </div>
        
        <div class="relative z-10 animate-slideInLeft">
            <div class="mb-8">
                <img src="../../assets/images/logo.png" alt="Logo DigiNiaga" class="h-10 w-auto mb-8 brightness-0 invert opacity-90">
                <div class="w-16 h-1 bg-blue-400 mb-6 rounded-full"></div>
                <h2 class="text-4xl font-bold text-white mb-4 leading-tight">Mulai Perjalanan Bisnis Digital.</h2>
                <p class="text-blue-200 text-base leading-relaxed">Satu langkah lagi untuk mengelola stok, kasir, dan laporan keuangan toko Anda dengan sistem DigiNiaga.</p>
            </div>
            
            <div class="space-y-6 mt-12">
                <div class="flex items-center space-x-4 animate-fadeInUp" style="animation-delay: 0.2s;">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center flex-shrink-0 border border-blue-400/20 backdrop-blur-sm">
                        <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold text-lg">Setup Toko</h3>
                        <p class="text-blue-300 text-sm">Konfigurasi profil usaha Anda dengan mudah</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4 animate-fadeInUp" style="animation-delay: 0.4s;">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center flex-shrink-0 border border-blue-400/20 backdrop-blur-sm">
                        <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold text-lg">Siap Digunakan</h3>
                        <p class="text-blue-300 text-sm">Dashboard langsung aktif setelah setup selesai</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative z-10 flex items-center text-blue-300 text-xs">
            <span>&copy; 2025 DigiNiaga System.</span>
        </div>
    </div>

    <div class="w-full md:w-3/5 flex flex-col items-center p-8 relative overflow-y-auto custom-scroll">
        <div class="w-full max-w-lg my-auto animate-fadeInUp">
            
            <div class="md:hidden flex justify-center mb-8">
                <img src="../../assets/images/logo.png" alt="Logo DigiNiaga" class="h-16 w-auto object-contain">
            </div>

            <div class="mb-10">
                <span class="inline-block py-1 px-3 rounded-full bg-blue-50 text-brand-600 text-xs font-bold tracking-wide uppercase mb-3">Langkah Terakhir</span>
                <h1 class="text-3xl font-bold gradient-text mb-2">Informasi Usaha</h1>
                <p class="text-gray-500 text-base">Halo <span class="font-bold text-gray-800"><?= htmlspecialchars($fullname) ?></span>, mari lengkapi profil toko Anda untuk memulai.</p>
            </div>

            <?= $message ?>

            <form action="" method="POST" class="space-y-6" id="setupForm">
                
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Nama Usaha / Toko</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <input type="text" name="name" required class="input-glow bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500 block w-full pl-12 pr-4 py-3.5 transition-all" placeholder="Contoh: Kopi Senja, Berkah Mart">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">No. Telepon Toko</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none z-10">
                                <span class="text-gray-500 font-bold text-sm">+62</span>
                            </div>
                            <input type="text" name="phone" required class="input-glow bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500 block w-full pl-12 pr-4 py-3.5 transition-all" placeholder="812-3456-7890">
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Kategori Bisnis</label>
                        <div class="relative">
                            <select name="category" required class="input-glow bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500 block w-full px-4 py-3.5 transition-all appearance-none cursor-pointer">
                                <option value="" disabled selected>Pilih Kategori...</option>
                                <option value="Retail">Retail / Minimarket</option>
                                <option value="F&B">F&B / Cafe / Resto</option>
                                <option value="Fashion">Fashion</option>
                                <option value="Elektronik">Elektronik</option>
                                <option value="Jasa">Jasa / Laundry</option>
                                <option value="Kesehatan">Kesehatan</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Alamat Lengkap</label>
                    <div class="relative">
                        <div class="absolute top-3.5 left-0 flex items-start pl-4 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <textarea name="address" required rows="3" class="input-glow bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500 block w-full pl-12 pr-4 py-3.5 transition-all resize-none" placeholder="Jalan, Nomor Ruko, Kota..."></textarea>
                    </div>
                </div>

                <button type="submit" class="btn-gradient w-full text-white font-bold rounded-xl text-sm px-5 py-4 text-center transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 active:translate-y-0 flex justify-center items-center gap-2 group">
                    <span class="relative z-10">Simpan & Masuk Dashboard</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </button>
                
                <p class="text-center text-xs text-gray-400 mt-4">
                    Dengan melanjutkan, Anda menyetujui <a href="#" class="text-brand-600 hover:underline">Ketentuan Layanan</a> DigiNiaga.
                </p>
            </form>
        </div>
    </div>

    <script>
        // Animasi input focus
        const inputs = document.querySelectorAll('input[type="text"], select, textarea');
        inputs.forEach(input => {
            input.addEventListener('focus', function() { this.parentElement.classList.add('scale-[1.01]'); });
            input.addEventListener('blur', function() { this.parentElement.classList.remove('scale-[1.01]'); });
        });

        // Loading state pada tombol submit
        const setupForm = document.getElementById('setupForm');
        setupForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
        });
    </script>
</body>
</html>
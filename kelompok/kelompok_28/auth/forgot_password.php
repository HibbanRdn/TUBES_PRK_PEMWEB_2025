<?php
// auth/forgot_password.php
session_start();
// Redirect jika sudah login
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: ../pages/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - DigiNiaga</title>
    
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
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes pulse-ring {
            0% {
                transform: scale(0.95);
                opacity: 1;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.7;
            }
            100% {
                transform: scale(0.95);
                opacity: 1;
            }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        .animate-fadeInUp {
            animation: fadeInUp 0.8s ease-out forwards;
        }
        
        .animate-slideInLeft {
            animation: slideInLeft 0.8s ease-out forwards;
        }
        
        .animate-pulse-ring {
            animation: pulse-ring 3s ease-in-out infinite;
        }
        
        .animate-shake {
            animation: shake 0.5s ease-in-out;
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .input-glow:focus {
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
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
        
        .btn-gradient:hover::before {
            left: 100%;
        }
        
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
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            top: -100px;
            right: -100px;
            animation: float 8s ease-in-out infinite;
        }
        
        .shape-2 {
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, #1d4ed8, #1e3a8a);
            bottom: -50px;
            left: -50px;
            animation: float 10s ease-in-out infinite reverse;
        }
        
        .shield-icon {
            filter: drop-shadow(0 10px 30px rgba(37, 99, 235, 0.3));
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-blue-50 h-screen w-full flex overflow-hidden">

    <!-- Left Panel - Security Info -->
    <div class="hidden md:flex w-2/5 bg-gradient-to-br from-brand-900 via-brand-800 to-brand-900 flex-col justify-between p-12 relative overflow-hidden">
        <!-- Animated Background Pattern -->
        <div class="absolute inset-0 pattern-dots"></div>
        
        <!-- Floating Shapes -->
        <div class="absolute inset-0">
            <div class="floating-shape shape-1"></div>
            <div class="floating-shape shape-2"></div>
        </div>
        
        <!-- Content -->
        <div class="relative z-10 animate-slideInLeft">
            <div class="mb-8">
                <div class="w-16 h-1 bg-blue-400 mb-6 rounded-full"></div>
                <h2 class="text-4xl font-bold text-white mb-4 leading-tight">
                    Reset Akses
                </h2>
                <p class="text-blue-200 text-base leading-relaxed mb-6">
                    Owner dapat mereset password melalui Email terdaftar.
                </p>
                <p class="text-blue-300 text-sm leading-relaxed">
                    Untuk Staff (Kasir/Gudang), silakan hubungi Owner Toko untuk reset manual demi menjaga keamanan sistem.
                </p>
            </div>
            
            <!-- Security Features -->
            <div class="space-y-4 mt-12">
                <div class="flex items-start space-x-3 animate-fadeInUp" style="animation-delay: 0.2s;">
                    <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold mb-1">Enkripsi Aman</h3>
                        <p class="text-blue-300 text-sm">Data dilindungi dengan enkripsi tingkat enterprise</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3 animate-fadeInUp" style="animation-delay: 0.4s;">
                    <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold mb-1">Verifikasi Email</h3>
                        <p class="text-blue-300 text-sm">Link reset dikirim ke email terdaftar Anda</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3 animate-fadeInUp" style="animation-delay: 0.6s;">
                    <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold mb-1">Waktu Terbatas</h3>
                        <p class="text-blue-300 text-sm">Link reset berlaku selama 15 menit</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="relative z-10 flex items-center justify-between text-blue-300 text-xs">
            <span>&copy; 2025 Kelompok 28. All rights reserved.</span>
            <div class="flex space-x-4">
                <a href="#" class="hover:text-white transition-colors">Privacy</a>
                <a href="#" class="hover:text-white transition-colors">Terms</a>
            </div>
        </div>
    </div>

    <!-- Right Panel - Reset Form -->
    <div class="w-full md:w-3/5 flex flex-col justify-center items-center p-8 relative overflow-y-auto">
        
        <div class="w-full max-w-md animate-fadeInUp">
            
            <!-- Security Shield Icon -->
            <div class="flex justify-center mb-8">
                <div class="relative">
                    <!-- Outer Ring with Pulse -->
                    <div class="absolute inset-0 bg-brand-100 rounded-full animate-pulse-ring"></div>
                    
                    <!-- Shield Icon -->
                    <div class="relative bg-gradient-to-br from-brand-500 to-brand-700 rounded-full p-6 shield-icon animate-float">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold gradient-text mb-3">Lupa Password?</h1>
                <p class="text-gray-600 text-base">
                    Masukkan <span class="font-semibold text-brand-600">Email (Owner)</span> atau <span class="font-semibold text-brand-600">Username (Staff)</span>
                </p>
            </div>

            <!-- Alert Messages -->
            <?php if (isset($_GET['status'])): ?>
                
                <?php if ($_GET['status'] == 'email_sent'): ?>
                    <div class="flex p-4 mb-6 text-sm text-green-800 border border-green-300 rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 animate-fadeInUp shadow-sm" role="alert">
                        <svg class="flex-shrink-0 inline w-6 h-6 me-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                        </svg>
                        <div>
                            <span class="font-bold text-base block mb-1">Email Berhasil Terkirim!</span>
                            <span class="text-sm">Silakan cek inbox email <strong class="font-semibold"><?= isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '' ?></strong></span><br>
                            <span class="text-xs text-green-700 mt-2 block italic">(Jangan lupa cek folder Spam jika tidak ada di Inbox)</span>
                        </div>
                    </div>
                
                <?php elseif ($_GET['status'] == 'staff_notice'): ?>
                    <div class="flex p-4 mb-6 text-sm text-blue-800 border border-blue-300 rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 animate-fadeInUp shadow-sm" role="alert">
                        <svg class="flex-shrink-0 inline w-6 h-6 me-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <div>
                            <span class="font-bold text-base block mb-1">Akun Staff Ditemukan</span>
                            <span class="text-sm">Demi keamanan, reset password untuk akun Staff hanya bisa dilakukan oleh <strong class="font-semibold">Owner Toko</strong> melalui Dashboard Owner.</span>
                        </div>
                    </div>

                <?php elseif ($_GET['status'] == 'not_found'): ?>
                    <div class="flex items-start p-4 mb-6 text-sm text-red-800 border border-red-300 rounded-xl bg-gradient-to-r from-red-50 to-pink-50 animate-fadeInUp animate-shake shadow-sm" role="alert">
                        <svg class="flex-shrink-0 inline w-6 h-6 me-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                             <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <div>
                            <span class="font-bold text-base block mb-1">Data Tidak Ditemukan</span>
                            <span class="text-sm">Email atau Username tidak terdaftar dalam sistem.</span>
                        </div>
                    </div>

                <?php elseif ($_GET['status'] == 'error_sending'): ?>
                    <div class="flex items-start p-4 mb-6 text-sm text-red-800 border border-red-300 rounded-xl bg-gradient-to-r from-red-50 to-pink-50 animate-fadeInUp shadow-sm" role="alert">
                        <svg class="flex-shrink-0 inline w-6 h-6 me-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z"/>
                        </svg>
                        <div>
                            <span class="font-bold text-base block mb-1">Gagal Mengirim Email</span>
                            <span class="text-sm">Terjadi kesalahan pada server. Silakan coba beberapa saat lagi.</span>
                        </div>
                    </div>

                <?php elseif ($_GET['status'] == 'empty'): ?>
                    <div class="flex items-start p-4 mb-6 text-sm text-yellow-800 border border-yellow-300 rounded-xl bg-gradient-to-r from-yellow-50 to-amber-50 animate-fadeInUp shadow-sm" role="alert">
                        <svg class="flex-shrink-0 inline w-6 h-6 me-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z"/>
                        </svg>
                        <div>
                            <span class="font-bold text-base">Kolom Kosong!</span>
                            <span class="text-sm block mt-1">Harap isi Email atau Username terlebih dahulu.</span>
                        </div>
                    </div>
                <?php endif; ?>

            <?php endif; ?>

            <!-- Reset Form -->
            <form action="process_forgot.php" method="POST" class="space-y-6" id="resetForm">
                <div class="space-y-2">
                    <label for="identifier" class="block text-sm font-semibold text-gray-700">Email atau Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                        <input type="text" name="identifier" id="identifier" 
                               class="input-glow bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500 block w-full pl-12 pr-4 py-3.5 transition-all" 
                               placeholder="owner@toko.com atau kasir_budi" required>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 ml-1">
                        <svg class="w-3.5 h-3.5 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        Masukkan email untuk Owner atau username untuk Staff
                    </p>
                </div>

                <button type="submit" 
                        class="btn-gradient w-full text-white font-bold rounded-xl text-sm px-5 py-4 text-center transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 active:translate-y-0">
                    <span class="relative z-10 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Kirim Permintaan Reset
                    </span>
                </button>
            </form>

            <!-- Back to Login -->
            <div class="mt-8 text-center">
                <a href="login.php" class="inline-flex items-center text-sm font-semibold text-gray-600 hover:text-brand-600 transition-all group">
                    <svg class="h-4 w-4 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke halaman Login
                </a>
            </div>

            <!-- Help Section -->
            <div class="mt-8 p-5 bg-gradient-to-br from-gray-50 to-blue-50 rounded-xl border border-gray-200">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-1">Butuh Bantuan?</h3>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Jika Anda mengalami kesulitan dalam mereset password, silakan hubungi 
                            <a href="mailto:hibbanrdn@gmail.com" class="text-brand-600 font-semibold hover:underline">Administrator</a> 
                            untuk bantuan lebih lanjut.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Form Submit Animation
        const resetForm = document.getElementById('resetForm');
        resetForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            submitBtn.disabled = true;
        });
        
        // Input Focus Animation
        const identifierInput = document.getElementById('identifier');
        identifierInput.addEventListener('focus', function() {
            this.parentElement.classList.add('scale-[1.02]');
        });
        
        identifierInput.addEventListener('blur', function() {
            this.parentElement.classList.remove('scale-[1.02]');
        });
        
        // Auto dismiss success message after 8 seconds
        const successAlert = document.querySelector('[role="alert"]');
        if (successAlert && successAlert.classList.contains('text-green-800')) {
            setTimeout(() => {
                successAlert.style.transition = 'opacity 0.5s ease-out';
                successAlert.style.opacity = '0';
                setTimeout(() => successAlert.remove(), 500);
            }, 8000);
        }
    </script>

</body>
</html>
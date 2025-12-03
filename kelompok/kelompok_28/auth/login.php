<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: ../redirect.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Masuk - DigiNiaga</title>
    
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
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
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
                <div class="w-16 h-1 bg-blue-400 mb-6 rounded-full"></div>
                <h2 class="text-4xl font-bold text-white mb-4 leading-tight">Kelola bisnis lebih efisien.</h2>
                <p class="text-blue-200 text-base leading-relaxed">Solusi transformasi digital untuk UMKM dengan DigiNiaga. Pantau stok, penjualan, dan laporan dalam satu platform terintegrasi.</p>
            </div>
            
            <div class="space-y-4 mt-12">
                <div class="flex items-start space-x-3 animate-fadeInUp" style="animation-delay: 0.2s;">
                    <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold mb-1">Dashboard Real-time</h3>
                        <p class="text-blue-300 text-sm">Monitor performa bisnis secara langsung</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3 animate-fadeInUp" style="animation-delay: 0.4s;">
                    <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold mb-1">Otomasi Cerdas</h3>
                        <p class="text-blue-300 text-sm">Hemat waktu dengan sistem otomatis</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3 animate-fadeInUp" style="animation-delay: 0.6s;">
                    <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold mb-1">Aman & Terpercaya</h3>
                        <p class="text-blue-300 text-sm">Data terenkripsi dengan standar tinggi</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative z-10 flex items-center justify-between text-blue-300 text-xs">
            <span>&copy; 2025 Kelompok 28. All rights reserved.</span>
            <div class="flex space-x-4">
                <a href="#" class="hover:text-white transition-colors">Privacy</a>
                <a href="#" class="hover:text-white transition-colors">Terms</a>
            </div>
        </div>
    </div>

    <div class="w-full md:w-3/5 flex flex-col justify-center items-center p-8 relative overflow-y-auto">
        <div class="w-full max-w-md animate-fadeInUp">
            
            <div class="flex justify-center mb-8">
                <div class="relative animate-float">
                    <img src="../assets/images/logo.png" alt="Logo DigiNiaga" class="h-24 w-auto object-contain drop-shadow-lg">
                </div>
            </div>

            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold gradient-text mb-3">Selamat Datang</h1>
                <p class="text-gray-500 text-base">Silakan masukkan detail akun Anda untuk memulai.</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="flex items-center p-4 mb-6 text-sm text-red-800 border border-red-300 rounded-xl bg-red-50 animate-fadeInUp shadow-sm" role="alert">
                    <svg class="flex-shrink-0 inline w-5 h-5 me-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <div>
                        <span class="font-semibold">Gagal Masuk!</span>
                        <?php 
                            if($_GET['error'] == 'invalid') echo " Username atau Password tidak valid.";
                            elseif($_GET['error'] == 'empty') echo " Harap isi semua kolom.";
                            elseif($_GET['error'] == 'suspended') echo " Akun Anda telah dinonaktifkan. Hubungi Owner.";
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['reset']) && $_GET['reset'] == 'success'): ?>
                <div id="toast-success" class="fixed top-5 right-5 z-50 flex items-center w-full max-w-sm p-4 mb-4 text-gray-500 bg-white rounded-xl shadow-2xl border-l-4 border-green-500 animate-slideInLeft" role="alert">
                    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                        </svg>
                        <span class="sr-only">Check icon</span>
                    </div>
                    
                    <div class="ml-3 text-sm font-normal">
                        <span class="mb-1 text-sm font-semibold text-gray-900 block">Berhasil!</span>
                        <div class="text-sm font-normal">Password berhasil direset. Silakan login.</div>
                    </div>
                    
                    <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8" aria-label="Close" onclick="dismissToast()">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>

                <script>
                    function dismissToast() {
                        const toast = document.getElementById('toast-success');
                        if (toast) {
                            toast.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
                            toast.style.opacity = '0';
                            toast.style.transform = 'translateX(100%)'; 
                            setTimeout(() => toast.remove(), 500);
                        }
                    }
                    // Auto-close toast setelah 5 detik
                    setTimeout(dismissToast, 5000);
                </script>
            <?php endif; ?>

            <?php if (isset($_GET['registered']) && $_GET['registered'] == 'success'): ?>
                <div class="flex items-center p-4 mb-6 text-sm text-green-800 border border-green-300 rounded-xl bg-green-50 animate-fadeInUp shadow-sm" role="alert">
                    <svg class="flex-shrink-0 inline w-5 h-5 me-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                    </svg>
                    <div>
                        <span class="font-bold">Registrasi Berhasil!</span> Silakan login dengan akun baru Anda.
                    </div>
                </div>
            <?php endif; ?>

            <form action="process_login.php" method="POST" class="space-y-6" id="loginForm">
                <div class="space-y-2">
                    <label for="username" class="block text-sm font-semibold text-gray-700">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <input type="text" name="username" id="username" class="input-glow bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500 block w-full pl-12 pr-4 py-3.5 transition-all" placeholder="Masukkan username Anda" required>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input type="password" name="password" id="password" class="input-glow bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500 block w-full pl-12 pr-12 py-3.5 transition-all" placeholder="Masukkan password Anda" required>
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex items-center pr-4">
                            <svg id="eyeIcon" class="w-5 h-5 text-gray-400 hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" type="checkbox" class="w-4 h-4 text-brand-600 bg-gray-100 border-gray-300 rounded focus:ring-brand-500 focus:ring-2 cursor-pointer">
                        <label for="remember" class="ml-2 text-sm font-medium text-gray-600 cursor-pointer select-none">Ingat saya</label>
                    </div>
                    <a href="forgot_password.php" class="text-sm font-semibold text-brand-600 hover:text-brand-700 transition-colors">Lupa password?</a>
                </div>

                <button type="submit" class="btn-gradient w-full text-white font-bold rounded-xl text-sm px-5 py-4 text-center transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 active:translate-y-0">
                    <span class="relative z-10">Masuk ke Dashboard</span>
                </button>
            </form>

            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-gradient-to-br from-gray-50 to-blue-50 text-gray-500">atau</span>
                </div>
            </div>

            <div class="text-center p-6 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border border-blue-100">
                <p class="text-sm text-gray-600 mb-3">Belum punya akun staf?</p>
                <a href="mailto:hibbanrdn@gmail.com?subject=Permintaan%20Akun%20Staff%20Baru%20-%20DigiNiaga" class="inline-flex items-center space-x-2 font-semibold text-brand-600 hover:text-brand-700 transition-colors group">
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span>Hubungi Owner</span>
                </a>
            </div>

            <p class="mt-8 text-center text-xs text-gray-400">
                Dengan masuk, Anda menyetujui <a href="#" class="text-brand-600 hover:underline">Syarat & Ketentuan</a> dan <a href="#" class="text-brand-600 hover:underline">Kebijakan Privasi</a> kami.
            </p>
        </div>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            if (type === 'text') {
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
            } else {
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        });
        
        const loginForm = document.getElementById('loginForm');
        loginForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            submitBtn.disabled = true;
        });
        
        const inputs = document.querySelectorAll('input[type="text"], input[type="password"]');
        inputs.forEach(input => {
            input.addEventListener('focus', function() { this.parentElement.classList.add('scale-[1.02]'); });
            input.addEventListener('blur', function() { this.parentElement.classList.remove('scale-[1.02]'); });
        });
    </script>
</body>


</html>
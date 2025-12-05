<?php
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
    <title>Daftar Owner Baru - DigiNiaga</title>
    
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
        /* Menggunakan style animasi yang sama dengan login.php */
        @keyframes float { 0%, 100% { transform: translateY(0px) rotate(0deg); } 50% { transform: translateY(-20px) rotate(5deg); } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideInRight { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }
        
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-fadeInUp { animation: fadeInUp 0.8s ease-out forwards; }
        .animate-slideInRight { animation: slideInRight 0.8s ease-out forwards; }
        
        .gradient-text {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .input-glow:focus { box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
        
        .btn-gradient {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transition: all 0.3s ease;
        }
        .btn-gradient:hover { box-shadow: 0 10px 20px -10px rgba(37, 99, 235, 0.5); transform: translateY(-2px); }
        
        .pattern-dots {
            background-image: radial-gradient(circle, rgba(255, 255, 255, 0.15) 1px, transparent 1px);
            background-size: 25px 25px;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-blue-50 h-screen w-full flex overflow-hidden">

    <div class="hidden md:flex w-2/5 bg-gradient-to-br from-brand-900 via-brand-800 to-brand-900 flex-col justify-between p-12 relative overflow-hidden">
        <div class="absolute inset-0 pattern-dots"></div>
        
        <div class="relative z-10 animate-slideInRight">
            <div class="mb-8">
                <div class="w-16 h-1 bg-blue-400 mb-6 rounded-full"></div>
                <h2 class="text-4xl font-bold text-white mb-4 leading-tight">
                    Mulai perjalanan bisnis digital Anda.
                </h2>
                <p class="text-blue-200 text-base leading-relaxed">
                    Bergabunglah dengan ribuan pemilik bisnis lainnya. Kelola stok, pantau kasir, dan analisa keuntungan dalam satu dashboard.
                </p>
            </div>
            
            <div class="mt-12 space-y-6">
                <div class="flex items-center space-x-4 text-blue-100">
                    <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-300 font-bold">1</div>
                    <p>Daftar Akun Owner</p>
                </div>
                <div class="w-0.5 h-6 bg-blue-800 ml-4"></div>
                <div class="flex items-center space-x-4 text-blue-100">
                    <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-300 font-bold">2</div>
                    <p>Buat Toko Pertama</p>
                </div>
                <div class="w-0.5 h-6 bg-blue-800 ml-4"></div>
                <div class="flex items-center space-x-4 text-blue-100">
                    <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-300 font-bold">3</div>
                    <p>Rekrut Kasir & Gudang</p>
                </div>
            </div>
        </div>

        <div class="relative z-10 text-blue-300 text-xs">
            &copy; 2025 DigiNiaga System.  All rights reserved.
        </div>
    </div>

    <div class="w-full md:w-3/5 flex flex-col items-center justify-center p-8 relative overflow-y-auto">
        <div class="w-full max-w-lg animate-fadeInUp my-auto">
            
            <div class="text-center mb-8">
                <img src="../assets/images/logo.png" alt="Logo" class="h-16 w-auto mx-auto mb-4 object-contain">
                <h1 class="text-3xl font-bold gradient-text">Registrasi Owner</h1>
                <p class="text-gray-500 mt-2 text-sm">Lengkapi data diri untuk membuat akun pemilik bisnis.</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="p-4 mb-6 text-sm text-red-800 border border-red-300 rounded-xl bg-red-50" role="alert">
                    <span class="font-bold">Gagal!</span>
                    <?php 
                        if($_GET['error'] == 'empty') echo "Harap isi semua kolom wajib.";
                        elseif($_GET['error'] == 'password_mismatch') echo "Konfirmasi password tidak sesuai.";
                        elseif($_GET['error'] == 'username_taken') echo "Username sudah digunakan.";
                        elseif($_GET['error'] == 'email_taken') echo "Email sudah terdaftar.";
                        elseif($_GET['error'] == 'db_error') echo "Terjadi kesalahan sistem. Coba lagi.";
                    ?>
                </div>
            <?php endif; ?>

            <form action="process_register.php" method="POST" class="space-y-5" id="registerForm">
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="fullname" class="input-glow w-full p-3 border border-gray-300 rounded-xl focus:border-brand-500 focus:ring-brand-500 transition-colors" placeholder="Contoh: Budi Santoso" required>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Email (Wajib)</label>
                        <input type="email" name="email" class="input-glow w-full p-3 border border-gray-300 rounded-xl focus:border-brand-500 focus:ring-brand-500 transition-colors" placeholder="email@bisnis.com" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">No. HP / WhatsApp</label>
                        <input type="text" name="phone" class="input-glow w-full p-3 border border-gray-300 rounded-xl focus:border-brand-500 focus:ring-brand-500 transition-colors" placeholder="0812...">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" class="input-glow w-full p-3 border border-gray-300 rounded-xl focus:border-brand-500 focus:ring-brand-500 transition-colors" placeholder="Username untuk login" required>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" class="input-glow w-full p-3 border border-gray-300 rounded-xl focus:border-brand-500 focus:ring-brand-500" placeholder="Minimal 6 karakter" required minlength="6">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Ulangi Password</label>
                        <input type="password" name="confirm_password" class="input-glow w-full p-3 border border-gray-300 rounded-xl focus:border-brand-500 focus:ring-brand-500" placeholder="Ketik ulang password" required>
                    </div>
                </div>

                <div class="flex items-start mt-2">
                    <div class="flex items-center h-5">
                        <input id="terms" type="checkbox" required class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-brand-300">
                    </div>
                    <label for="terms" class="ml-2 text-sm font-medium text-gray-500">Saya menyetujui <a href="#" class="text-brand-600 hover:underline">Syarat & Ketentuan</a> DigiNiaga.</label>
                </div>

                <button type="submit" class="btn-gradient w-full text-white font-bold rounded-xl text-sm px-5 py-4 text-center shadow-lg transform hover:-translate-y-0.5 transition-all">
                    Daftar Sekarang
                </button>
            </form>

            <div class="mt-8 text-center text-sm">
                <span class="text-gray-500">Sudah punya akun?</span>
                <a href="login.php" class="font-bold text-brand-600 hover:text-brand-700 ml-1">Masuk disini</a>
            </div>
        </div>
    </div>

</body>
</html>
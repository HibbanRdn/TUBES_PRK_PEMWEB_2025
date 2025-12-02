<?php
session_start();
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
    <title>Login Masuk - DigiNiaga</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
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
                            600: '#2563eb', // Warna Utama (Royal Blue)
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-white h-screen w-full flex overflow-hidden">

    <div class="hidden md:flex w-1/4 bg-brand-900 flex-col justify-center p-8 relative overflow-hidden">
        <div class="absolute inset-0 opacity-20" 
             style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px;">
        </div>
        
        <div class="relative z-10">
            <h2 class="text-3xl font-bold text-white mb-6 leading-tight">
                Kelola bisnis lebih efisien.
            </h2>
            <p class="text-blue-200 text-sm leading-relaxed">
                Solusi transformasi digital untuk UMKM dengan DigiNiaga. Pantau stok, penjualan, dan laporan dalam satu platform.
            </p>
        </div>

        <div class="absolute bottom-8 left-8 text-blue-300 text-xs">
            &copy; 2025 Kelompok 28.
        </div>
    </div>

    <div class="w-full md:w-3/4 flex flex-col justify-center items-center p-8 bg-white overflow-y-auto">
        
        <div class="w-full max-w-md">
            
            <div class="flex justify-center mb-6">
                <img src="../assets/images/logo.png" alt="Logo DigiNiaga" class="h-20 w-auto object-contain">
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-2 text-center">Selamat Datang</h1>
            <p class="text-gray-500 mb-8 text-center">Silakan masukkan detail akun Anda untuk memulai.</p>

            <?php if (isset($_GET['error'])): ?>
                <div class="flex items-center p-4 mb-6 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50" role="alert">
                    <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span class="sr-only">Info</span>
                    <div>
                        <span class="font-medium">Gagal Masuk!</span>
                        <?php 
                            if($_GET['error'] == 'invalid') echo "Username atau Password tidak valid.";
                            elseif($_GET['error'] == 'empty') echo "Harap isi semua kolom.";
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <form action="process_login.php" method="POST" class="space-y-5">
                <div>
                    <label for="username" class="block mb-2 text-sm font-semibold text-gray-700">Username</label>
                    <input type="text" name="username" id="username" 
                           class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block w-full p-3 transition-colors" 
                           placeholder="Contoh: owner" required>
                </div>

                <div>
                    <label for="password" class="block mb-2 text-sm font-semibold text-gray-700">Password</label>
                    <input type="password" name="password" id="password" 
                           class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block w-full p-3 transition-colors" 
                           placeholder="••••••••" required>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="remember" type="checkbox" value="" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-brand-300">
                        </div>
                        <label for="remember" class="ms-2 text-sm font-medium text-gray-500">Ingat saya</label>
                    </div>
                    <a href="forgot_password.php" class="text-sm font-medium text-brand-600 hover:underline">Lupa password?</a>
                </div>

                <button type="submit" 
                        class="w-full text-white bg-brand-600 hover:bg-brand-700 focus:ring-4 focus:outline-none focus:ring-brand-300 font-bold rounded-lg text-sm px-5 py-3 text-center transition-all shadow-md hover:shadow-lg">
                    Masuk ke Dashboard
                </button>
            </form>

            <p class="mt-8 text-center text-sm text-gray-500">
                Belum punya akun staf? 
                <a href="mailto:hibbanrdn@gmail.com?subject=Permintaan%20Akun%20Staff%20Baru%20-%20DigiNiaga&body=Halo%20Owner%2C%0D%0A%0D%0ASaya%20ingin%20meminta%20pembuatan%20akun%20staff%20baru%20untuk%20sistem%20DigiNiaga.%0D%0A%0D%0ANama%20Staff%3A%20%5BIsi%20Nama%5D%0D%0APosisi%3A%20%5BIsi%20Posisi%5D%0D%0A%0D%0ATerima%20kasih." 
                   class="font-semibold text-brand-600 hover:underline">
                   Hubungi Owner
                </a>
            </p>
        </div>
    </div>

</body>
</html>
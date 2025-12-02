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
                            600: '#2563eb', 
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
                Keamanan Akun
            </h2>
            <p class="text-blue-200 text-sm leading-relaxed">
                Jangan khawatir. Kami membantu memastikan akses Anda ke DigiNiaga tetap aman dan terkendali.
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

            <h1 class="text-3xl font-bold text-gray-900 mb-2 text-center">Lupa Password?</h1>
            <p class="text-gray-500 mb-8 text-center text-sm">
                Masukkan username Anda untuk mengajukan permohonan reset password.
            </p>

            <?php if (isset($_GET['status'])): ?>
                
                <?php if ($_GET['status'] == 'success'): ?>
                    <div class="flex p-4 mb-6 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50" role="alert">
                        <svg class="flex-shrink-0 inline w-4 h-4 me-3 mt-[2px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                        </svg>
                        <div>
                            <span class="font-bold">Permintaan Diterima!</span><br>
                            Halo <strong><?= isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'User' ?></strong>, username Anda valid. Silakan hubungi Owner/Admin Gudang untuk meminta password sementara.
                        </div>
                    </div>
                
                <?php elseif ($_GET['status'] == 'not_found'): ?>
                    <div class="flex items-center p-4 mb-6 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50" role="alert">
                        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <div>
                            <span class="font-medium">Gagal!</span> Username tersebut tidak terdaftar dalam sistem.
                        </div>
                    </div>

                <?php elseif ($_GET['status'] == 'empty'): ?>
                    <div class="flex items-center p-4 mb-6 text-sm text-yellow-800 border border-yellow-300 rounded-lg bg-yellow-50" role="alert">
                         <span class="font-medium">Perhatian!</span> Harap isi kolom username.
                    </div>
                <?php endif; ?>

            <?php endif; ?>

            <form action="process_forgot.php" method="POST" class="space-y-5">
                <div>
                    <label for="username" class="block mb-2 text-sm font-semibold text-gray-700">Username</label>
                    <input type="text" name="username" id="username" 
                           class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block w-full p-3 transition-colors" 
                           placeholder="Masukkan username Anda" required>
                </div>

                <button type="submit" 
                        class="w-full text-white bg-brand-600 hover:bg-brand-700 focus:ring-4 focus:outline-none focus:ring-brand-300 font-bold rounded-lg text-sm px-5 py-3 text-center transition-all shadow-md hover:shadow-lg">
                    Cek Ketersediaan Akun
                </button>
            </form>

            <div class="mt-8 text-center">
                <a href="login.php" class="inline-flex items-center text-sm font-semibold text-gray-600 hover:text-brand-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke halaman Login
                </a>
            </div>

        </div>
    </div>

</body>
</html>
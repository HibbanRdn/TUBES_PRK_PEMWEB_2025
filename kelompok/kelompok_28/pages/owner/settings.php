<?php 
// FILE: settings.php
// Memanggil logika backend untuk memproses data dan mengambil variabel ($owner, $store, dll)
require_once '../../process/process_settings.php'; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - DigiNiaga</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
        }
        
        .card-gradient { background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); }
        .input-glow:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1), 0 4px 12px rgba(99, 102, 241, 0.15);
            border-color: #6366f1;
        }
        .btn-dark {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            transition: all 0.3s ease;
        }
        .btn-dark:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(0, 0, 0, 0.3); }
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }
        .btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(102, 126, 234, 0.4); }
        
        /* Animasi */
        .animate-scaleIn { animation: scaleIn 0.4s ease-out; }
        @keyframes scaleIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    </style>
</head>
<body class="min-h-screen pb-20">

    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-50">
        <div class="flex items-center gap-5">
            <a href="dashboard.php" class="w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition-colors text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>

            <div class="h-8 w-px bg-gray-200"></div>

            <div>
                <h1 class="text-xl font-bold text-gray-900 leading-none">Pengaturan</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola profil & data toko</p>
            </div>
        </div>

        <div class="text-right hidden sm:block">
            <p class="text-xs text-gray-400 font-medium mb-0.5">Logged in as</p>
            <p class="text-sm font-bold text-gray-900"><?= htmlspecialchars($fullname) ?></p>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto mt-8 px-4 sm:px-6 relative z-10">
        
        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] == 'success_profile'): ?>
                <div class="mb-8 card-gradient p-5 rounded-2xl border-2 border-green-200 shadow-lg animate-scaleIn">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600">✓</div>
                        <p class="font-bold text-green-800">Profil berhasil diperbarui!</p>
                    </div>
                </div>
            <?php elseif ($_GET['msg'] == 'success_store'): ?>
                <div class="mb-8 card-gradient p-5 rounded-2xl border-2 border-green-200 shadow-lg animate-scaleIn">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600">✓</div>
                        <p class="font-bold text-green-800">Data toko berhasil disimpan!</p>
                    </div>
                </div>
            <?php elseif (strpos($_GET['msg'], 'error') !== false): ?>
                <div class="mb-8 card-gradient p-5 rounded-2xl border-2 border-red-200 shadow-lg animate-scaleIn">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center text-red-600">!</div>
                        <p class="font-bold text-red-800">Terjadi kesalahan. Pastikan data lengkap.</p>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <div class="card-gradient rounded-3xl p-8 shadow-2xl border border-white/50 hover:shadow-xl transition-shadow animate-scaleIn">
                <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-200">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-extrabold text-gray-900">Profil Owner</h2>
                        <p class="text-sm text-gray-500 font-medium">Informasi pribadi Anda</p>
                    </div>
                </div>

                <form action="" method="POST" class="space-y-6">
                    <input type="hidden" name="action" value="update_profile">
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">Username</label>
                        <input type="text" value="<?= htmlspecialchars($owner['username']) ?>" disabled class="w-full p-4 bg-gray-100 border-2 border-gray-200 rounded-xl text-gray-500 cursor-not-allowed font-semibold">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">Email</label>
                        <input type="email" value="<?= htmlspecialchars($owner['email']) ?>" disabled class="w-full p-4 bg-gray-100 border-2 border-gray-200 rounded-xl text-gray-500 cursor-not-allowed font-semibold">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">Nama Lengkap</label>
                        <input type="text" name="fullname" value="<?= htmlspecialchars($owner['fullname']) ?>" required class="w-full p-4 border-2 border-gray-200 rounded-xl focus:outline-none input-glow font-semibold text-gray-800">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">Nomor HP</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($owner['phone']) ?>" class="w-full p-4 border-2 border-gray-200 rounded-xl focus:outline-none input-glow font-semibold text-gray-800">
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-4 px-6 btn-gradient text-white font-extrabold rounded-xl shadow-xl flex items-center justify-center gap-2 text-lg">
                            Simpan Profil
                        </button>
                    </div>
                </form>
            </div>

            <div class="card-gradient rounded-3xl p-8 shadow-2xl border border-white/50 hover:shadow-xl transition-shadow animate-scaleIn" style="animation-delay: 0.1s;">
                <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-200">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-500 rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-extrabold text-gray-900">Informasi Toko</h2>
                        <p class="text-sm text-gray-500 font-medium">Data toko untuk struk belanja</p>
                    </div>
                </div>

                <form action="" method="POST" class="space-y-6">
                    <input type="hidden" name="action" value="update_store">
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">Nama Toko</label>
                        <input type="text" name="store_name" value="<?= htmlspecialchars($store['name']) ?>" required class="w-full p-4 border-2 border-gray-200 rounded-xl focus:outline-none input-glow font-semibold text-gray-800">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">Nomor Telepon Toko</label>
                        <input type="text" name="store_phone" value="<?= htmlspecialchars($store['phone']) ?>" class="w-full p-4 border-2 border-gray-200 rounded-xl focus:outline-none input-glow font-semibold text-gray-800">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">Alamat Toko</label>
                        <textarea name="store_address" rows="4" class="w-full p-4 border-2 border-gray-200 rounded-xl focus:outline-none input-glow font-semibold text-gray-800 resize-none"><?= htmlspecialchars($store['address']) ?></textarea>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 flex gap-3">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-xs text-blue-700 leading-relaxed">
                            Data ini akan dicetak pada kop struk belanja. Pastikan alamat dan nomor telepon valid.
                        </p>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-4 px-6 btn-dark text-white font-extrabold rounded-xl shadow-xl flex items-center justify-center gap-2 text-lg">
                            Simpan Data Toko
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

</body>
</html>
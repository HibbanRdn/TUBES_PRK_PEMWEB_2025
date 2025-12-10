<?php 
// FILE: users.php
require_once '../../process/process_users.php'; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Karyawan - <?= htmlspecialchars($store['name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
        }
        
        .animate-fadeIn { 
            animation: fadeIn 0.6s ease-out; 
        }
        
        .animate-slideDown {
            animation: slideDown 0.5s ease-out;
        }
        
        .animate-scaleIn {
            animation: scaleIn 0.4s ease-out;
        }
        
        @keyframes fadeIn { 
            from { opacity: 0; transform: translateY(20px); } 
            to { opacity: 1; transform: translateY(0); } 
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }
        
        .card-gradient {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
        }
        
        .form-input:focus {
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="min-h-screen">

    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-50 rounded-full filter blur-3xl opacity-30"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-50 rounded-full filter blur-3xl opacity-30"></div>
    </div>

    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-50 shadow-sm">
        <div class="flex items-center gap-3 animate-fadeIn">
            <a href="dashboard.php" class="group flex items-center gap-2 text-gray-600 hover:text-blue-600 transition-all duration-300">
                <div class="w-10 h-10 bg-gray-100 group-hover:bg-blue-50 rounded-xl flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </div>
            </a>
            <div class="h-8 w-px bg-gray-200 mx-2"></div>
            <div>
                <h1 class="font-bold text-gray-800 text-lg">Manajemen Karyawan</h1>
                <p class="text-xs text-gray-500">Kelola tim Anda</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="hidden sm:block text-right">
                <p class="text-xs text-gray-400">Toko</p>
                <p class="text-sm font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent"><?= htmlspecialchars($store['name']) ?></p>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto p-6 relative z-10">
        
        <?= $message ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 animate-fadeIn">
            <div class="card-gradient p-6 rounded-2xl border border-gray-100 shadow-lg hover-lift">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Total Karyawan</p>
                        <h3 class="text-3xl font-extrabold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                            <?= mysqli_num_rows($employees) ?>
                        </h3>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-purple-500 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                </div>
            </div>

            <div class="card-gradient p-6 rounded-2xl border border-gray-100 shadow-lg hover-lift">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Kasir</p>
                        <h3 class="text-3xl font-extrabold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                            <?= $total_kasir ?>
                        </h3>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                </div>
            </div>

            <div class="card-gradient p-6 rounded-2xl border border-gray-100 shadow-lg hover-lift">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Admin Gudang</p>
                        <h3 class="text-3xl font-extrabold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent">
                            <?= $total_gudang ?>
                        </h3>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1">
                <div class="card-gradient rounded-3xl shadow-xl border border-gray-100 p-8 sticky top-24 animate-scaleIn">
                    
                    <div class="flex items-center gap-3 mb-6">
                        <?php if($edit_mode): ?>
                            <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-extrabold text-gray-800">Edit Data Karyawan</h2>
                                <p class="text-xs text-gray-500">Perbarui informasi staff</p>
                            </div>
                        <?php else: ?>
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-extrabold text-gray-800">Tambah Staff Baru</h2>
                                <p class="text-xs text-gray-500">Daftarkan karyawan baru</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <form action="" method="POST" class="space-y-5">
                        <input type="hidden" name="action" value="<?= $edit_mode ? 'update' : 'add' ?>">
                        
                        <?php if($edit_mode): ?>
                            <input type="hidden" name="emp_id" value="<?= $edit_data['id'] ?>">
                        <?php endif; ?>
                        
                        <div>
                            <label class="block text-xs font-extrabold text-gray-600 uppercase tracking-wider mb-2">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                    Nama Lengkap
                                </span>
                            </label>
                            <input type="text" name="fullname" required 
                                   value="<?= $edit_mode ? htmlspecialchars($edit_data['fullname']) : '' ?>"
                                   class="form-input w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-white font-medium" 
                                   placeholder="Misal: Siti Aminah">
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-gray-600 uppercase tracking-wider mb-2">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.952 22.952 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path><path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"></path></svg>
                                    Role / Jabatan
                                </span>
                            </label>
                            <select name="role" class="form-input w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none bg-white font-medium transition-all cursor-pointer">
                                <option value="kasir" <?= ($edit_mode && $edit_data['role'] == 'kasir') ? 'selected' : '' ?>>🧾 Kasir (Frontliner)</option>
                                <option value="admin_gudang" <?= ($edit_mode && $edit_data['role'] == 'admin_gudang') ? 'selected' : '' ?>>📦 Admin Gudang (Stok)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-gray-600 uppercase tracking-wider mb-2">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path></svg>
                                    Username
                                </span>
                            </label>
                            <input type="text" name="username" required 
                                   value="<?= $edit_mode ? htmlspecialchars($edit_data['username']) : '' ?>"
                                   class="form-input w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none bg-white font-medium transition-all" 
                                   placeholder="Username login">
                        </div>

                        <div class="<?= $edit_mode ? 'bg-gradient-to-r from-yellow-50 to-orange-50 p-4 rounded-2xl border-2 border-yellow-200' : 'bg-blue-50 p-4 rounded-2xl border-2 border-blue-200' ?>">
                            <label class="block text-xs font-extrabold text-gray-600 uppercase tracking-wider mb-2">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                                    <?= $edit_mode ? 'Reset Password (Opsional)' : 'Password Awal' ?>
                                </span>
                            </label>
                            <input type="text" name="password" <?= $edit_mode ? '' : 'required' ?>
                                   class="form-input w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none bg-white font-medium transition-all" 
                                   placeholder="*******">
                            <p class="text-xs text-gray-500 mt-2 flex items-start gap-2">
                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                <span><?= $edit_mode ? 'Kosongkan jika tidak ingin mengubah password.' : 'Berikan password ini ke staff Anda.' ?></span>
                            </p>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <?php if($edit_mode): ?>
                                <a href="users.php" class="w-1/3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-extrabold py-4 rounded-xl transition-all hover:shadow-lg text-center text-sm flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Batal
                                </a>
                            <?php endif; ?>
                            
                            <button type="submit" class="w-full <?= $edit_mode ? 'bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600' : 'bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700' ?> text-white font-extrabold py-4 rounded-xl transition-all shadow-lg hover:shadow-xl hover:scale-105 flex items-center justify-center gap-2">
                                <?php if($edit_mode): ?>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    Update Data
                                <?php else: ?>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Simpan Karyawan
                                <?php endif; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="card-gradient rounded-3xl shadow-xl border border-gray-100 overflow-hidden animate-scaleIn" style="animation-delay: 0.1s;">
                    <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2 class="text-xl font-extrabold text-gray-800 flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                    Daftar Karyawan Aktif
                                </h2>
                                <p class="text-xs text-gray-500 mt-1 ml-13">Manajemen tim toko Anda</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="bg-gradient-to-r from-blue-600 to-purple-600 text-white text-xs font-extrabold px-4 py-2 rounded-full shadow-lg">
                                    <?= mysqli_num_rows($employees) ?> Staff
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="text-xs text-gray-600 uppercase bg-gray-50 border-b-2 border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 text-left font-extrabold">Nama Staff</th>
                                    <th class="px-6 py-4 text-left font-extrabold">Username</th>
                                    <th class="px-6 py-4 text-left font-extrabold">Role</th>
                                    <th class="px-6 py-4 text-center font-extrabold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($employees) > 0): ?>
                                    <?php 
                                    $no = 0;
                                    while($row = mysqli_fetch_assoc($employees)): 
                                        $no++;
                                        $isEditing = ($edit_mode && $edit_data['id'] == $row['id']);
                                    ?>
                                        <tr class="border-b border-gray-100 hover:bg-blue-50 transition-all duration-300 <?= $isEditing ? 'bg-blue-50 ring-2 ring-blue-500' : 'bg-white' ?> animate-fadeIn" style="animation-delay: <?= $no * 0.05 ?>s;">
                                            <td class="px-6 py-5">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-purple-500 rounded-xl flex items-center justify-center text-white font-extrabold shadow-lg">
                                                        <?= strtoupper(substr($row['fullname'], 0, 2)) ?>
                                                    </div>
                                                    <div>
                                                        <p class="font-extrabold text-gray-900 text-base"><?= htmlspecialchars($row['fullname']) ?></p>
                                                        <p class="text-xs text-gray-400 flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                                            ID: #<?= $row['id'] ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-5">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path></svg>
                                                    <span class="font-bold text-gray-700">@<?= htmlspecialchars($row['username']) ?></span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-5">
                                                <?php if($row['role'] == 'kasir'): ?>
                                                    <span class="inline-flex items-center gap-2 bg-green-100 text-green-800 text-xs font-extrabold px-3 py-1.5 rounded-xl border-2 border-green-200 shadow-sm">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"></path></svg>
                                                        Kasir
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-2 bg-orange-100 text-orange-800 text-xs font-extrabold px-3 py-1.5 rounded-xl border-2 border-orange-200 shadow-sm">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z"></path></svg>
                                                        Gudang
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-5">
                                                <div class="flex justify-center items-center gap-2">
                                                    <a href="?edit=<?= $row['id'] ?>" class="group relative bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 text-white px-4 py-2 rounded-xl text-xs font-extrabold transition-all shadow-md hover:shadow-lg hover:scale-105 flex items-center gap-2">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                        Edit
                                                    </a>
                                                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('⚠️ Yakin ingin menghapus karyawan ini?\n\nData yang dihapus tidak dapat dikembalikan!')" class="group relative bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white px-4 py-2 rounded-xl text-xs font-extrabold transition-all shadow-md hover:shadow-lg hover:scale-105 flex items-center gap-2">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        Hapus
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="px-6 py-16">
                                            <div class="text-center">
                                                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                                </div>
                                                <p class="text-gray-400 text-base font-bold mb-1">Belum ada karyawan</p>
                                                <p class="text-gray-300 text-sm">Tambahkan staff pertama Anda menggunakan form di sebelah kiri</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (mysqli_num_rows($employees) > 0): ?>
                    <div class="p-6 bg-gradient-to-r from-blue-50 to-purple-50 border-t border-gray-200">
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                <span class="font-semibold">Total <span class="font-extrabold text-blue-600"><?= mysqli_num_rows($employees) ?></span> karyawan terdaftar</span>
                            </div>
                            <div class="flex items-center gap-4 text-xs font-bold">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-gradient-to-r from-green-500 to-emerald-500"></div>
                                    <span class="text-gray-600"><?= $total_kasir ?> Kasir</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-gradient-to-r from-orange-500 to-red-500"></div>
                                    <span class="text-gray-600"><?= $total_gudang ?> Gudang</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil data dari session
$fullname = $_SESSION['fullname'] ?? 'User';
$username = $_SESSION['username'] ?? '-';
$role     = $_SESSION['role'] ?? 'Unknown';
$user_id  = $_SESSION['user_id'] ?? 0;

// Logika tampilan Badge Role
$role_badge_color = 'bg-gray-100 text-gray-800';
$role_label = $role;

if ($role === 'owner') {
    $role_badge_color = 'bg-purple-100 text-purple-800 border-purple-200';
    $role_label = '👑 Owner Bisnis';
} elseif ($role === 'admin_gudang') {
    $role_badge_color = 'bg-orange-100 text-orange-800 border-orange-200';
    $role_label = '📦 Admin Gudang';
} elseif ($role === 'kasir') {
    $role_badge_color = 'bg-green-100 text-green-800 border-green-200';
    $role_label = '🛒 Kasir';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Debug - DigiNiaga</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: { brand: { 500: '#3b82f6', 600: '#2563eb', 900: '#1e3a8a' } }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen">

    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <img src="../assets/images/logo.png" alt="Logo" class="h-8 w-auto">
            <span class="font-bold text-gray-800 text-lg">DigiNiaga Dashboard</span>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-500">Halo, <b><?= htmlspecialchars($fullname) ?></b></span>
            <a href="../auth/logout.php" class="bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg text-sm font-semibold transition-colors border border-red-100">
                Logout
            </a>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto mt-10 p-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Selamat Datang! 👋</h1>
            <p class="text-gray-500 mb-8">Ini adalah halaman sementara untuk mengecek status login (Authentication Debug).</p>

            <div class="bg-brand-50 rounded-xl p-6 border border-blue-100 mb-8">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold text-blue-500 uppercase tracking-wider mb-1">Status Akun</p>
                        <h2 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($fullname) ?></h2>
                        <p class="text-gray-600 text-sm mt-1">@<?= htmlspecialchars($username) ?></p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold border <?= $role_badge_color ?>">
                        <?= $role_label ?>
                    </span>
                </div>
            </div>

            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Data Session (Debug Info)</h3>
            <div class="overflow-hidden rounded-xl border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Variable</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Value</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-sm">
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-900">User ID</td>
                            <td class="px-6 py-4 text-gray-500"><?= $user_id ?></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-900">Username</td>
                            <td class="px-6 py-4 text-gray-500"><?= htmlspecialchars($username) ?></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-900">Full Name</td>
                            <td class="px-6 py-4 text-gray-500"><?= htmlspecialchars($fullname) ?></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-900">Role</td>
                            <td class="px-6 py-4 font-mono text-blue-600"><?= htmlspecialchars($role) ?></td>
                        </tr>
                        <?php if (isset($_SESSION['store_id'])): ?>
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-900">Store ID (Toko)</td>
                            <td class="px-6 py-4 text-gray-500"><?= $_SESSION['store_id'] ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</body>
</html>
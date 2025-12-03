<?php
session_start();
require_once '../../config/database.php';

// 1. Cek Login & Role Owner
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'owner') {
    header("Location: ../../auth/login.php");
    exit;
}

$owner_id = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'];

// 2. Cek Apakah Sudah Punya Toko
$sql_check = "SELECT id FROM stores WHERE owner_id = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql_check);
mysqli_stmt_bind_param($stmt, "i", $owner_id);
mysqli_stmt_execute($stmt);
if (mysqli_num_rows(mysqli_stmt_get_result($stmt)) > 0) {
    header("Location: dashboard.php");
    exit;
}

$message = "";

// 3. Proses Form Submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $store_name = trim($_POST['name']);
    $phone      = trim($_POST['phone']);
    $address    = trim($_POST['address']);
    $category   = trim($_POST['category']); // Ambil data kategori

    // Validasi
    if (empty($store_name) || empty($phone) || empty($address) || empty($category)) {
        $message = "<div class='bg-red-50 text-red-600 p-4 rounded-xl mb-6 border-l-4 border-red-500 flex items-center gap-3 animate-pulse'>
                        <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'></path></svg>
                        <span>Mohon lengkapi semua data usaha Anda.</span>
                    </div>";
    } else {
        // Insert ke Database (Menambahkan kolom category)
        $sql_insert = "INSERT INTO stores (owner_id, name, phone, address, category) VALUES (?, ?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($conn, $sql_insert)) {
            mysqli_stmt_bind_param($stmt, "issss", $owner_id, $store_name, $phone, $address, $category);
            
            if (mysqli_stmt_execute($stmt)) {
                header("Location: dashboard.php?setup=success");
                exit;
            } else {
                $message = "<div class='bg-red-50 text-red-600 p-4 rounded-xl mb-6'>Terjadi kesalahan sistem: " . mysqli_error($conn) . "</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Usaha Baru - DigiNiaga</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .input-focus:focus { 
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); 
            border-color: #2563eb; 
        }
        /* Custom Scrollbar untuk bagian form jika konten panjang */
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
    </style>
</head>
<body class="bg-white min-h-screen flex flex-col md:flex-row overflow-hidden">

    <div class="w-full md:w-5/12 lg:w-4/12 bg-blue-700 p-10 flex flex-col justify-between text-white relative h-48 md:h-screen shrink-0">
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <svg class="w-full h-full" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 100 C 20 0 50 0 100 100 L 100 0 L 0 0 Z"></path>
            </svg>
        </div>

        <div class="relative z-10 flex items-center gap-2">
            <img src="../../assets/images/logo.png" alt="Logo" class="h-8 brightness-0 invert"> 
        </div>

        <div class="relative z-10 hidden md:block">
            <h2 class="text-4xl font-bold mb-6 leading-tight">Bangun Ekosistem Digital Bisnis Anda.</h2>
            <p class="text-blue-100 text-lg leading-relaxed mb-8">
                Kelola stok, pantau transaksi kasir, dan analisa keuntungan real-time dalam satu aplikasi pintar.
            </p>
            
            <div class="space-y-4">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <span class="font-medium">Setup Instan</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <span class="font-medium">Manajemen Karyawan</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                    </div>
                    <span class="font-medium">Laporan Akurat</span>
                </div>
            </div>
        </div>

        <div class="relative z-10 text-sm text-blue-200 mt-auto hidden md:block">
            &copy; 2025 DigiNiaga POS System.
        </div>
    </div>

    <div class="w-full md:w-7/12 lg:w-8/12 bg-white h-full md:h-screen overflow-y-auto custom-scroll flex flex-col items-center justify-center p-6 md:p-12">
        
        <div class="w-full max-w-2xl">
            <div class="mb-10">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Informasi Usaha</h1>
                <p class="text-gray-500">Halo <span class="font-bold text-blue-700"><?= htmlspecialchars($fullname) ?></span>, mari lengkapi profil toko Anda untuk memulai.</p>
            </div>

            <?= $message ?>

            <form action="" method="POST" class="space-y-6">
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Usaha / Toko</label>
                    <input type="text" name="name" required class="input-focus w-full px-5 py-4 rounded-xl border border-gray-200 outline-none transition text-gray-800 placeholder-gray-400 bg-gray-50 focus:bg-white text-lg font-medium" placeholder="Contoh: Kopi Senja, Berkah Mart, dll.">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nomor Telepon Toko</label>
                        <div class="relative">
                            <span class="absolute left-5 top-4 text-gray-400 font-bold">+62</span>
                            <input type="text" name="phone" required class="input-focus w-full pl-14 pr-5 py-4 rounded-xl border border-gray-200 outline-none transition text-gray-800 bg-gray-50 focus:bg-white" placeholder="812-3456-7890">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Kategori Bisnis</label>
                        <div class="relative">
                            <select name="category" required class="input-focus w-full px-5 py-4 rounded-xl border border-gray-200 outline-none transition text-gray-800 bg-gray-50 focus:bg-white appearance-none cursor-pointer">
                                <option value="" disabled selected class="text-gray-400">Pilih Kategori...</option>
                                <option value="Retail">Retail / Kelontong / Minimarket</option>
                                <option value="F&B">F&B / Restoran / Cafe</option>
                                <option value="Fashion">Fashion & Aksesoris</option>
                                <option value="Elektronik">Elektronik & Gadget</option>
                                <option value="Jasa">Jasa / Service / Laundry</option>
                                <option value="Kesehatan">Kesehatan / Apotek</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-5 text-gray-500">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Lengkap</label>
                    <textarea name="address" required rows="3" class="input-focus w-full px-5 py-4 rounded-xl border border-gray-200 outline-none transition text-gray-800 placeholder-gray-400 bg-gray-50 focus:bg-white resize-none" placeholder="Nama Jalan, Nomor Ruko, RT/RW, Kota..."></textarea>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-blue-200 hover:shadow-xl hover:-translate-y-1 flex justify-center items-center gap-3 text-lg">
                        <span>Buat Toko & Masuk Dashboard</span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </button>
                    <p class="text-center text-sm text-gray-400 mt-6">
                        Dengan menekan tombol di atas, Anda menyetujui <a href="#" class="text-blue-600 hover:underline font-medium">Syarat & Ketentuan</a> DigiNiaga.
                    </p>
                </div>

            </form>
        </div>
    </div>

</body>
</html>
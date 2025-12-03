<?php
session_start();
require_once '../../config/database.php';

// 1. Cek Apakah User adalah Owner
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'owner') {
    header("Location: ../../auth/login.php");
    exit;
}

$owner_id = $_SESSION['user_id'];
$message = "";

// 2. Ambil Data Toko Milik Owner
$sql_store = "SELECT id, name FROM stores WHERE owner_id = ? LIMIT 1";
$stmt_store = mysqli_prepare($conn, $sql_store);
mysqli_stmt_bind_param($stmt_store, "i", $owner_id);
mysqli_stmt_execute($stmt_store);
$res_store = mysqli_stmt_get_result($stmt_store);
$store = mysqli_fetch_assoc($res_store);

if (!$store) {
    die("Error: Anda belum memiliki toko.");
}
$store_id = $store['id'];

// --- LOGIKA EDIT: Cek apakah sedang mode edit ---
$edit_mode = false;
$edit_data = null;

if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    // Ambil data karyawan spesifik (Pastikan milik toko owner ini agar aman)
    $sql_edit = "SELECT * FROM employees WHERE id = ? AND store_id = ?";
    $stmt_edit = mysqli_prepare($conn, $sql_edit);
    mysqli_stmt_bind_param($stmt_edit, "ii", $edit_id, $store_id);
    mysqli_stmt_execute($stmt_edit);
    $result_edit = mysqli_stmt_get_result($stmt_edit);
    
    if ($result_edit && mysqli_num_rows($result_edit) > 0) {
        $edit_mode = true;
        $edit_data = mysqli_fetch_assoc($result_edit);
    }
}

// 3. Handle Form Submission (Tambah ATAU Update)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action   = $_POST['action'];
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $role     = $_POST['role'];
    $password = $_POST['password']; // Bisa kosong jika mode edit

    // --- A. ADD USER ---
    if ($action == 'add') {
        if (!empty($fullname) && !empty($username) && !empty($password)) {
            // Cek username kembar di toko ini
            $check = mysqli_query($conn, "SELECT id FROM employees WHERE username = '$username' AND store_id = '$store_id'");
            if (mysqli_num_rows($check) > 0) {
                $message = "<div class='bg-red-100 text-red-700 p-3 rounded mb-4'>Username sudah digunakan!</div>";
            } else {
                $hash_pass = password_hash($password, PASSWORD_DEFAULT);
                $sql_insert = "INSERT INTO employees (store_id, fullname, username, password, role, is_active) VALUES (?, ?, ?, ?, ?, 1)";
                if ($stmt = mysqli_prepare($conn, $sql_insert)) {
                    mysqli_stmt_bind_param($stmt, "issss", $store_id, $fullname, $username, $hash_pass, $role);
                    if (mysqli_stmt_execute($stmt)) {
                        $message = "<div class='bg-green-100 text-green-700 p-3 rounded mb-4'>Karyawan berhasil ditambahkan!</div>";
                    }
                }
            }
        }
    }
    // --- B. UPDATE USER ---
    elseif ($action == 'update') {
        $emp_id = $_POST['emp_id'];
        
        // Cek apakah password diisi (Reset Password) atau kosong (Tetap password lama)
        if (!empty($password)) {
            // Update dengan Password Baru
            $hash_pass = password_hash($password, PASSWORD_DEFAULT);
            $sql_update = "UPDATE employees SET fullname=?, username=?, role=?, password=? WHERE id=? AND store_id=?";
            $stmt = mysqli_prepare($conn, $sql_update);
            mysqli_stmt_bind_param($stmt, "ssssii", $fullname, $username, $role, $hash_pass, $emp_id, $store_id);
        } else {
            // Update Tanpa Ganti Password
            $sql_update = "UPDATE employees SET fullname=?, username=?, role=? WHERE id=? AND store_id=?";
            $stmt = mysqli_prepare($conn, $sql_update);
            mysqli_stmt_bind_param($stmt, "sssii", $fullname, $username, $role, $emp_id, $store_id);
        }

        if (mysqli_stmt_execute($stmt)) {
            // Redirect agar form kembali bersih / mode edit hilang
            header("Location: users.php?msg=updated");
            exit;
        } else {
            $message = "<div class='bg-red-100 text-red-700 p-3 rounded mb-4'>Gagal mengupdate data.</div>";
        }
    }
}

// Menangkap pesan sukses dari redirect update
if (isset($_GET['msg']) && $_GET['msg'] == 'updated') {
    $message = "<div class='bg-blue-100 text-blue-700 p-3 rounded mb-4'>Data karyawan berhasil diperbarui!</div>";
}

// 4. Handle Delete
if (isset($_GET['delete'])) {
    $emp_id = $_GET['delete'];
    $sql_del = "DELETE FROM employees WHERE id = ? AND store_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql_del)) {
        mysqli_stmt_bind_param($stmt, "ii", $emp_id, $store_id);
        mysqli_stmt_execute($stmt);
        header("Location: users.php");
        exit;
    }
}

// 5. Ambil Daftar Karyawan
$sql_employees = "SELECT * FROM employees WHERE store_id = ? ORDER BY created_at DESC";
$stmt_emp = mysqli_prepare($conn, $sql_employees);
mysqli_stmt_bind_param($stmt_emp, "i", $store_id);
mysqli_stmt_execute($stmt_emp);
$employees = mysqli_stmt_get_result($stmt_emp);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - <?= htmlspecialchars($store['name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen">

    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-10">
        <div class="flex items-center gap-3">
            <a href="dashboard.php" class="text-gray-500 hover:text-blue-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="font-bold text-gray-800 text-lg">Manajemen Karyawan</h1>
        </div>
        <div class="text-sm text-gray-500">
            Toko: <span class="font-semibold text-blue-600"><?= htmlspecialchars($store['name']) ?></span>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto p-6">
        
        <?= $message ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                    
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <?php if($edit_mode): ?>
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit Data Karyawan
                        <?php else: ?>
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                            Tambah Staff Baru
                        <?php endif; ?>
                    </h2>
                    
                    <form action="" method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="<?= $edit_mode ? 'update' : 'add' ?>">
                        
                        <?php if($edit_mode): ?>
                            <input type="hidden" name="emp_id" value="<?= $edit_data['id'] ?>">
                        <?php endif; ?>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Nama Lengkap</label>
                            <input type="text" name="fullname" required 
                                   value="<?= $edit_mode ? htmlspecialchars($edit_data['fullname']) : '' ?>"
                                   class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" 
                                   placeholder="Misal: Siti Aminah">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Role / Jabatan</label>
                            <select name="role" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                                <option value="kasir" <?= ($edit_mode && $edit_data['role'] == 'kasir') ? 'selected' : '' ?>>Kasir (Frontliner)</option>
                                <option value="admin_gudang" <?= ($edit_mode && $edit_data['role'] == 'admin_gudang') ? 'selected' : '' ?>>Admin Gudang (Stok)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Username</label>
                            <input type="text" name="username" required 
                                   value="<?= $edit_mode ? htmlspecialchars($edit_data['username']) : '' ?>"
                                   class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none" 
                                   placeholder="Username login">
                        </div>

                        <div class="<?= $edit_mode ? 'bg-yellow-50 p-3 rounded-lg border border-yellow-100' : '' ?>">
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">
                                <?= $edit_mode ? 'Reset Password (Opsional)' : 'Password Awal' ?>
                            </label>
                            <input type="text" name="password" <?= $edit_mode ? '' : 'required' ?>
                                   class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none bg-white" 
                                   placeholder="*******">
                            <p class="text-xs text-gray-400 mt-1">
                                <?= $edit_mode ? '*Kosongkan jika tidak ingin mengubah password.' : '*Berikan password ini ke staff Anda.' ?>
                            </p>
                        </div>

                        <div class="flex gap-2 pt-2">
                            <?php if($edit_mode): ?>
                                <a href="users.php" class="w-1/3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 rounded-xl transition text-center text-sm flex items-center justify-center">
                                    Batal
                                </a>
                            <?php endif; ?>
                            
                            <button type="submit" class="w-full <?= $edit_mode ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-blue-600 hover:bg-blue-700' ?> text-white font-bold py-3 rounded-xl transition shadow-lg">
                                <?= $edit_mode ? 'Update Data' : 'Simpan Karyawan' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <h2 class="font-bold text-gray-800">Daftar Karyawan Aktif</h2>
                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full"><?= mysqli_num_rows($employees) ?> Staff</span>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">Nama Staff</th>
                                    <th class="px-6 py-3">Username</th>
                                    <th class="px-6 py-3">Role</th>
                                    <th class="px-6 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($employees) > 0): ?>
                                    <?php while($row = mysqli_fetch_assoc($employees)): ?>
                                        <tr class="bg-white border-b hover:bg-gray-50 transition <?= ($edit_mode && $edit_data['id'] == $row['id']) ? 'bg-blue-50' : '' ?>">
                                            <td class="px-6 py-4 font-medium text-gray-900">
                                                <?= htmlspecialchars($row['fullname']) ?>
                                                <div class="text-xs text-gray-400">ID: #<?= $row['id'] ?></div>
                                            </td>
                                            <td class="px-6 py-4">@<?= htmlspecialchars($row['username']) ?></td>
                                            <td class="px-6 py-4">
                                                <?php if($row['role'] == 'kasir'): ?>
                                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded border border-green-200">Kasir</span>
                                                <?php else: ?>
                                                    <span class="bg-orange-100 text-orange-800 text-xs font-medium px-2.5 py-0.5 rounded border border-orange-200">Gudang</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <div class="flex justify-center items-center gap-2">
                                                    <a href="?edit=<?= $row['id'] ?>" class="bg-yellow-100 text-yellow-600 hover:bg-yellow-200 px-3 py-1 rounded text-xs font-semibold transition">
                                                        Edit
                                                    </a>
                                                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus karyawan ini?')" class="bg-red-100 text-red-600 hover:bg-red-200 px-3 py-1 rounded text-xs font-semibold transition">
                                                        Hapus
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                            Belum ada data karyawan. Silakan tambah staff baru.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
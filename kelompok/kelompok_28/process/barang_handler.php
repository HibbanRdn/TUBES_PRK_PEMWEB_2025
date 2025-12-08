<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php'; 

// 1. CEK LOGIN & ROLE
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['store_id'])) {
    // Redirect jika belum login (path disesuaikan ke folder auth)
    header("Location: ../auth/login.php");
    exit;
}

$store_id = $_SESSION['store_id'];
$action = $_GET['act'] ?? '';

// --- FUNGSI VALIDASI ---
function validateInput($name, $price, $stock) {
    if (empty($name)) {
        return "Nama barang tidak boleh kosong.";
    }
    if ($price <= 0) {
        return "Harga harus lebih besar dari 0.";
    }
    if ($stock < 0) {
        return "Stok tidak boleh negatif (kurang dari 0).";
    }
    return true; 
}

// --- LOGIKA TAMBAH BARANG (CREATE) ---
if ($action == 'add' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $name        = mysqli_real_escape_string($conn, $_POST['name']);
    $category_id = (int) $_POST['category_id'];
    $price       = (float) $_POST['price'];
    $stock       = (int) $_POST['stock'];
    $description = "-"; 

    // Validasi Server Side
    $validation = validateInput($name, $price, $stock);
    if ($validation !== true) {
        header("Location: ../pages/admin_gudang/inventory.php?status=error&msg=" . urlencode($validation));
        exit;
    }

    $query = "INSERT INTO products (store_id, category_id, name, description, price, stock, is_active) 
              VALUES ('$store_id', '$category_id', '$name', '$description', '$price', '$stock', 1)";

    if (mysqli_query($conn, $query)) {
        header("Location: ../pages/admin_gudang/inventory.php?status=success");
    } else {
        header("Location: ../pages/admin_gudang/inventory.php?status=error&msg=Gagal input data");
    }
}

// --- LOGIKA UPDATE BARANG (EDIT) ---
elseif ($action == 'update' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $id          = (int) $_POST['id'];
    $name        = mysqli_real_escape_string($conn, $_POST['name']);
    $category_id = (int) $_POST['category_id'];
    $price       = (float) $_POST['price'];
    $stock       = (int) $_POST['stock'];

    // Validasi Server Side
    $validation = validateInput($name, $price, $stock);
    if ($validation !== true) {
        header("Location: ../pages/admin_gudang/inventory.php?status=error&msg=" . urlencode($validation));
        exit;
    }

    // Query Update dengan keamanan store_id
    $query = "UPDATE products 
              SET name = '$name', 
                  category_id = '$category_id', 
                  price = '$price', 
                  stock = '$stock' 
              WHERE id = '$id' AND store_id = '$store_id'";

    if (mysqli_query($conn, $query)) {
        header("Location: ../pages/admin_gudang/inventory.php?status=updated");
    } else {
        header("Location: ../pages/admin_gudang/inventory.php?status=error&msg=Gagal update data");
    }
}

// --- LOGIKA HAPUS BARANG ---
elseif ($action == 'delete') {
    $id = (int) $_GET['id'];
    $query = "UPDATE products SET is_active = 0 WHERE id = '$id' AND store_id = '$store_id'";

    if (mysqli_query($conn, $query)) {
        header("Location: ../pages/admin_gudang/inventory.php?msg=deleted");
    } else {
        header("Location: ../pages/admin_gudang/inventory.php?status=error&msg=Gagal menghapus");
    }
}

// --- LOGIKA TAMBAH KATEGORI ---
elseif ($action == 'add_category') {
    $name = mysqli_real_escape_string($conn, $_POST['category_name']);
    
    if (empty($name)) {
        header("Location: ../pages/admin_gudang/inventory.php?status=error&msg=Nama kategori kosong");
        exit;
    }

    $check = mysqli_query($conn, "SELECT id FROM categories WHERE store_id='$store_id' AND name='$name'");
    if(mysqli_num_rows($check) > 0) {
        header("Location: ../pages/admin_gudang/inventory.php?status=error&msg=Kategori sudah ada");
        exit;
    }

    $query = "INSERT INTO categories (store_id, name) VALUES ('$store_id', '$name')";
    if (mysqli_query($conn, $query)) {
        header("Location: ../pages/admin_gudang/inventory.php?status=success_cat");
    } else {
        header("Location: ../pages/admin_gudang/inventory.php?status=error");
    }
}
else {
    header("Location: ../pages/admin_gudang/inventory.php");
}
?>
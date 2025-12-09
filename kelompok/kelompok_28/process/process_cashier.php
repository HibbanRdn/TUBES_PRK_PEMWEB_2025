<?php
// FILE: process/process_cashier.php

// --- GANTI BARIS session_start(); BIASA DENGAN INI ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// -----------------------------------------------------

require_once __DIR__ . '/../config/database.php'; 

// ... sisa kode ke bawah biarkan sama ...
// Variabel Default (Jika tidak ada di database)
$store_name = 'DigiNiaga POS';
$store_address = '-';
$products = [];

// Cek Login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../auth/login.php");
    exit;
}

$store_id = $_SESSION['store_id'] ?? 0;
$employee_id = $_SESSION['user_id'] ?? 0;

// === BAGIAN 1: GET DATA (Untuk dipanggil require di dashboard) ===
if ($store_id > 0) {
    // 1. KEMBALIKAN LOGIKA AMBIL NAMA TOKO
    $sql_store = "SELECT name, address FROM stores WHERE id = ? LIMIT 1";
    if ($stmt = mysqli_prepare($conn, $sql_store)) {
        mysqli_stmt_bind_param($stmt, "i", $store_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if($row = mysqli_fetch_assoc($res)) {
            $store_name = $row['name'];
            $store_address = $row['address'];
        }
    }

    // 2. AMBIL PRODUK (Hanya jika bukan POST request/submit form)
    // Supaya saat proses bayar tidak perlu load produk lagi (lebih ringan)
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        $query_product = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.store_id = ? AND p.is_active = 1 AND p.stock > 0 ORDER BY p.name ASC";
        if ($stmt_prod = mysqli_prepare($conn, $query_product)) {
            mysqli_stmt_bind_param($stmt_prod, "i", $store_id);
            mysqli_stmt_execute($stmt_prod);
            $result_prod = mysqli_stmt_get_result($stmt_prod);
            while($row = mysqli_fetch_assoc($result_prod)) {
                $products[] = $row;
            }
        }
    }
}

// === BAGIAN 2: PROSES TRANSAKSI (Logic Pembayaran) ===
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['process_transaction'])) {
    
    $cart_data = json_decode($_POST['cart_data'], true);
    
    // Filter angka
    $total_price = (float) filter_var($_POST['total_amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $cash_amount = (float) filter_var($_POST['pay_amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    
    // Validasi JSON Error
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($cart_data)) {
         echo "<script>alert('Data keranjang corrupt/error!'); window.history.back();</script>"; exit;
    }

    // Hitung Ulang di Server (Keamanan)
    $server_total = 0;
    foreach($cart_data as $item) {
        $server_total += ($item['price'] * $item['qty']);
    }

    // Validasi Sederhana
    if (empty($cart_data)) {
        echo "<script>alert('Keranjang Kosong!'); window.history.back();</script>"; exit;
    }
    
    // Toleransi float precision (opsional, tapi disarankan)
    if ($cash_amount < ($server_total - 1)) { 
        echo "<script>alert('Uang Kurang!'); window.history.back();</script>"; exit;
    }

    $change_amount = $cash_amount - $server_total;
    $invoice_code = "INV/" . date('Ymd') . "/" . $store_id . "/" . rand(100, 999);
    $date_now = date('Y-m-d H:i:s');

    mysqli_begin_transaction($conn);

    try {
        // 1. Insert Header Transaksi
        $sql = "INSERT INTO transactions (store_id, employee_id, invoice_code, total_price, cash_amount, change_amount, date) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisddds", $store_id, $employee_id, $invoice_code, $server_total, $cash_amount, $change_amount, $date_now);
        
        if (!$stmt->execute()) throw new Exception("Gagal Invoice: " . $stmt->error);
        $trx_id = $conn->insert_id;
        $stmt->close();

        // 2. Insert Detail & Potong Stok
        $sql_det = "INSERT INTO transaction_details (transaction_id, product_id, qty, price_at_transaction, subtotal) VALUES (?, ?, ?, ?, ?)";
        $sql_stk = "UPDATE products SET stock = stock - ? WHERE id = ? AND store_id = ?";
        
        $stmt_det = $conn->prepare($sql_det);
        $stmt_stk = $conn->prepare($sql_stk);

        foreach ($cart_data as $item) {
            $sub = $item['price'] * $item['qty'];
            $p_id = $item['id'];
            $qty = $item['qty'];
            $price = $item['price'];
            
            // Simpan Detail
            $stmt_det->bind_param("iiidd", $trx_id, $p_id, $qty, $price, $sub);
            if (!$stmt_det->execute()) throw new Exception("Gagal Detail Item ID: " . $p_id);

            // Kurangi Stok
            $stmt_stk->bind_param("iii", $qty, $p_id, $store_id);
            if (!$stmt_stk->execute()) throw new Exception("Gagal Potong Stok Item ID: " . $p_id);
        }

        mysqli_commit($conn);

        // === SUKSES: SIMPAN INFO KE SESSION UNTUK POPUP ===
        // PERBAIKAN DI SINI: Ubah 'trx_success' jadi 'success_trx' 
        // agar sesuai dengan dashboard.php
        $_SESSION['success_trx'] = [
            'invoice' => $invoice_code,
            'total' => $server_total,
            'pay' => $cash_amount,
            'change' => $change_amount
        ];
        
        // Redirect kembali ke dashboard
        header("Location: ../pages/kasir/dashboard.php");
        exit;

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('Transaksi Gagal: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
        exit;
    }
}
?>
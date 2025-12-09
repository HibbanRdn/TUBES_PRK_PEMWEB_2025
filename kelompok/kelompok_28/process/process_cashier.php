<?php
// FILE: process/process_cashier.php

// 1. Session Check yang Aman
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php'; 

// Variabel Default
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

// === BAGIAN 1: GET DATA (Untuk tampilan Dashboard) ===
if ($store_id > 0) {
    // Ambil Nama Toko
    $sql_store = "SELECT name, address FROM stores WHERE id = ? LIMIT 1";
    if ($stmt = mysqli_prepare($conn, $sql_store)) {
        mysqli_stmt_bind_param($stmt, "i", $store_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if($row = mysqli_fetch_assoc($res)) {
            $store_name = $row['name'];
            $store_address = $row['address'];
        }
        mysqli_stmt_close($stmt);
    }

    // Ambil Produk
    // PENTING: Hanya ambil jika request GET (bukan saat submit transaksi)
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        // PERUBAHAN: Menghapus 'AND p.stock > 0' 
        // Agar frontend bisa menampilkan produk yang statusnya 'Sold Out/Habis'
        // Tambahkan p.code jika kolom kode produk ada di database Anda
        $query_product = "SELECT p.*, c.name as category_name 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.store_id = ? AND p.is_active = 1 
                          ORDER BY p.name ASC";
                          
        if ($stmt_prod = mysqli_prepare($conn, $query_product)) {
            mysqli_stmt_bind_param($stmt_prod, "i", $store_id);
            mysqli_stmt_execute($stmt_prod);
            $result_prod = mysqli_stmt_get_result($stmt_prod);
            while($row = mysqli_fetch_assoc($result_prod)) {
                $products[] = $row;
            }
            mysqli_stmt_close($stmt_prod);
        }
    }
}

// === BAGIAN 2: PROSES TRANSAKSI (POST) ===
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['process_transaction'])) {
    
    // 1. Ambil & Validasi Data Input
    $cart_data = json_decode($_POST['cart_data'], true);
    
    // Bersihkan format angka (hapus Rp, titik, koma jika ada sisa dari frontend)
    // Namun idealnya frontend sudah mengirim raw number di input hidden
    $post_total = (float) $_POST['total_amount'];
    $post_pay   = (float) $_POST['pay_amount'];
    
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($cart_data) || empty($cart_data)) {
         echo "<script>alert('Data keranjang tidak valid atau kosong!'); window.history.back();</script>"; 
         exit;
    }

    // 2. Hitung Ulang Total di Server (Security: Jangan percaya nominal dari frontend)
    $server_total = 0;
    
    // Kita perlu loop dulu untuk hitung total, validasi harga database nanti bisa ditambahkan
    // Untuk performa, kita percaya harga yang dikirim di cart_data tapi stok divalidasi
    foreach($cart_data as $item) {
        $server_total += ($item['price'] * $item['qty']);
    }

    // Cek Pembayaran
    // Gunakan epsilon 0.01 untuk toleransi floating point comparison
    if ($post_pay < ($server_total - 0.01)) { 
        echo "<script>alert('Uang pembayaran kurang!'); window.history.back();</script>"; 
        exit;
    }

    $change_amount = $post_pay - $server_total;
    // Format Invoice: INV/YYYYMMDD/STOREID/RANDOM
    $invoice_code = "INV/" . date('Ymd') . "/" . $store_id . "/" . strtoupper(substr(uniqid(), -4));
    $date_now = date('Y-m-d H:i:s');

    // 3. Mulai Database Transaction
    mysqli_begin_transaction($conn);

    try {
        // A. Insert ke tabel transactions
        $sql_head = "INSERT INTO transactions (store_id, employee_id, invoice_code, total_price, cash_amount, change_amount, date) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_head = $conn->prepare($sql_head);
        $stmt_head->bind_param("iisddds", $store_id, $employee_id, $invoice_code, $server_total, $post_pay, $change_amount, $date_now);
        
        if (!$stmt_head->execute()) {
            throw new Exception("Gagal membuat invoice: " . $stmt_head->error);
        }
        $trx_id = $conn->insert_id;
        $stmt_head->close();

        // B. Prepare Statement untuk Detail & Update Stok
        $sql_det = "INSERT INTO transaction_details (transaction_id, product_id, qty, price_at_transaction, subtotal) VALUES (?, ?, ?, ?, ?)";
        $stmt_det = $conn->prepare($sql_det);

        // PERUBAHAN PENTING: Update stok dengan kondisi stock >= qty
        // Ini mencegah stok menjadi minus jika ada race condition
        $sql_stk = "UPDATE products SET stock = stock - ? WHERE id = ? AND store_id = ? AND stock >= ?";
        $stmt_stk = $conn->prepare($sql_stk);

        foreach ($cart_data as $item) {
            $p_id = $item['id'];
            $qty = $item['qty'];
            $price = $item['price'];
            $subtotal = $price * $qty;
            
            // 1. Update Stok dulu (Validasi Stok Nyata)
            $stmt_stk->bind_param("iiii", $qty, $p_id, $store_id, $qty);
            $stmt_stk->execute();

            if ($stmt_stk->affected_rows === 0) {
                // Jika 0 row updated, berarti stok tidak cukup di DB atau ID salah
                throw new Exception("Stok tidak mencukupi untuk produk: " . $item['name']);
            }

            // 2. Insert Detail
            $stmt_det->bind_param("iiidd", $trx_id, $p_id, $qty, $price, $subtotal);
            if (!$stmt_det->execute()) {
                throw new Exception("Gagal menyimpan detail produk: " . $item['name']);
            }
        }

        // C. Commit Transaksi
        mysqli_commit($conn);

        // D. Set Session Sukses (Sesuai Frontend Baru)
        $_SESSION['success_trx'] = [
            'invoice' => $invoice_code,
            'total' => $server_total,
            'pay' => $post_pay,
            'change' => $change_amount
        ];
        
        // Redirect
        header("Location: ../pages/kasir/kasir.php");
        exit;

    } catch (Exception $e) {
        mysqli_rollback($conn);
        // Log error jika perlu: error_log($e->getMessage());
        echo "<script>alert('Transaksi Gagal: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
        exit;
    }
}
?>
<?php
session_start();
require_once '../../config/database.php';

// Security Check
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'owner') {
    header("Location: ../../auth/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../pages/owner/settings.php");
    exit;
}

$action = $_POST['action'] ?? '';
$owner_id = $_SESSION['user_id'];

// ========================================
// UPDATE PROFILE OWNER
// ========================================
if ($action === 'update_profile') {
    
    $fullname = trim($_POST['fullname'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    // Validasi input
    if (empty($fullname)) {
        header("Location: ../../pages/owner/settings.php?error=empty_fullname");
        exit;
    }
    
    // Validasi panjang nama
    if (strlen($fullname) < 3) {
        header("Location: ../../pages/owner/settings.php?error=fullname_too_short");
        exit;
    }
    
    // Sanitasi input
    $fullname = htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
    
    // Validasi format nomor telepon (opsional)
    if (!empty($phone)) {
        // Hapus karakter non-digit
        $phone_clean = preg_replace('/[^0-9]/', '', $phone);
        
        // Cek panjang nomor (minimal 10 digit, maksimal 15 digit)
        if (strlen($phone_clean) < 10 || strlen($phone_clean) > 15) {
            header("Location: ../../pages/owner/settings.php?error=invalid_phone");
            exit;
        }
        
        $phone = $phone_clean; // Gunakan nomor yang sudah dibersihkan
    }
    
    // Update database
    $sql = "UPDATE owners SET fullname = ?, phone = ? WHERE id = ?";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssi", $fullname, $phone, $owner_id);
        
        if (mysqli_stmt_execute($stmt)) {
            // Update session
            $_SESSION['fullname'] = $fullname;
            
            mysqli_stmt_close($stmt);
            header("Location: ../../pages/owner/settings.php?success=profile_updated");
            exit;
        } else {
            mysqli_stmt_close($stmt);
            error_log("Database error: " . mysqli_error($conn));
            header("Location: ../../pages/owner/settings.php?error=db_error");
            exit;
        }
    } else {
        error_log("Prepare statement failed: " . mysqli_error($conn));
        header("Location: ../../pages/owner/settings.php?error=db_error");
        exit;
    }
}

// ========================================
// UPDATE/CREATE STORE
// ========================================
elseif ($action === 'update_store') {
    
    $store_name = trim($_POST['store_name'] ?? '');
    $store_phone = trim($_POST['store_phone'] ?? '');
    $store_address = trim($_POST['store_address'] ?? '');
    
    // Validasi input wajib
    if (empty($store_name)) {
        header("Location: ../../pages/owner/settings.php?error=empty_store_name");
        exit;
    }
    
    // Validasi panjang nama toko
    if (strlen($store_name) < 3) {
        header("Location: ../../pages/owner/settings.php?error=store_name_too_short");
        exit;
    }
    
    // Sanitasi input
    $store_name = htmlspecialchars($store_name, ENT_QUOTES, 'UTF-8');
    $store_phone = htmlspecialchars($store_phone, ENT_QUOTES, 'UTF-8');
    $store_address = htmlspecialchars($store_address, ENT_QUOTES, 'UTF-8');
    
    // Validasi format nomor telepon toko (opsional)
    if (!empty($store_phone)) {
        $phone_clean = preg_replace('/[^0-9]/', '', $store_phone);
        
        if (strlen($phone_clean) < 10 || strlen($phone_clean) > 15) {
            header("Location: ../../pages/owner/settings.php?error=invalid_store_phone");
            exit;
        }
        
        $store_phone = $phone_clean;
    }
    
    // Cek apakah owner sudah punya toko
    $check_sql = "SELECT id FROM stores WHERE owner_id = ?";
    $stmt_check = mysqli_prepare($conn, $check_sql);
    
    if (!$stmt_check) {
        error_log("Prepare statement failed: " . mysqli_error($conn));
        header("Location: ../../pages/owner/settings.php?error=db_error");
        exit;
    }
    
    mysqli_stmt_bind_param($stmt_check, "i", $owner_id);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);
    
    $store_exists = mysqli_stmt_num_rows($stmt_check) > 0;
    mysqli_stmt_close($stmt_check);
    
    // Prepare SQL berdasarkan kondisi
    if ($store_exists) {
        // UPDATE toko yang sudah ada
        $sql = "UPDATE stores SET name = ?, phone = ?, address = ? WHERE owner_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssi", $store_name, $store_phone, $store_address, $owner_id);
        }
    } else {
        // INSERT toko baru
        $sql = "INSERT INTO stores (name, phone, address, owner_id) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssi", $store_name, $store_phone, $store_address, $owner_id);
        }
    }
    
    if (!$stmt) {
        error_log("Prepare statement failed: " . mysqli_error($conn));
        header("Location: ../../pages/owner/settings.php?error=db_error");
        exit;
    }
    
    // Execute query
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: ../../pages/owner/settings.php?success=store_updated");
        exit;
    } else {
        error_log("Database error: " . mysqli_error($conn));
        mysqli_stmt_close($stmt);
        header("Location: ../../pages/owner/settings.php?error=db_error");
        exit;
    }
}

// ========================================
// INVALID ACTION
// ========================================
else {
    header("Location: ../../pages/owner/settings.php?error=invalid_action");
    exit;
}

mysqli_close($conn);
?>
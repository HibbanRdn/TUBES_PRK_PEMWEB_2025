<?php
// auth/process_forgot.php
session_start();
require_once '../config/database.php';
require_once '../config/send_email.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $input = trim($_POST['identifier']); 

    if (empty($input)) {
        header("Location: forgot_password.php?status=empty");
        exit;
    }

    // --- CEK EMAIL DI TABEL OWNERS ---
    if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
        
        $sql = "SELECT id, fullname FROM owners WHERE email = ?";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $input);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $id, $fullname);
                mysqli_stmt_fetch($stmt);

                // 1. Generate Token
                $token = bin2hex(random_bytes(32)); 
                $token_hash = hash('sha256', $token); // Hash untuk database
                $expiry = date("Y-m-d H:i:s", time() + 60 * 30); // 30 Menit

                // 2. Simpan Hash ke DB
                $sql_update = "UPDATE owners SET reset_token_hash = ?, reset_token_expires_at = ? WHERE email = ?";
                if ($stmt_update = mysqli_prepare($conn, $sql_update)) {
                    mysqli_stmt_bind_param($stmt_update, "sss", $token_hash, $expiry, $input);
                    mysqli_stmt_execute($stmt_update);
                    
                    // 3. KIRIM EMAIL (SMTP ASLI)
                    if (sendResetEmail($input, $fullname, $token)) {
                        header("Location: forgot_password.php?status=email_sent&email=" . urlencode($input));
                    } else {
                        header("Location: forgot_password.php?status=error_sending"); 
                    }
                    exit;
                }
            } else {
                // Email format benar tapi tidak terdaftar
                header("Location: forgot_password.php?status=not_found");
                exit;
            }
        }
    } 
    // --- CEK STAFF (Username) ---
    else {
        $sql = "SELECT id FROM employees WHERE username = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $input);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                header("Location: forgot_password.php?status=staff_notice");
                exit;
            } else {
                header("Location: forgot_password.php?status=not_found");
                exit;
            }
        }
    }
}
mysqli_close($conn);
?>
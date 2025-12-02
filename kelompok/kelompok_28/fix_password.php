<?php
require_once 'config/database.php';

// Password yang diinginkan
$password_baru = "123456";

// Buat Hash asli menggunakan algoritma BCRYPT
$hash_asli = password_hash($password_baru, PASSWORD_DEFAULT);

// Update semua user (owner, gudang, kasir) agar passwordnya '123456'
$sql = "UPDATE users SET password = '$hash_asli'";

if (mysqli_query($conn, $sql)) {
    echo "<h1>Sukses!</h1>";
    echo "<p>Password untuk semua user (owner, gudang, kasir) telah di-reset menjadi: <strong>123456</strong></p>";
    echo "<p>Hash baru di database: $hash_asli</p>";
    echo "<a href='auth/login.php'>Silakan Login Kembali</a>";
} else {
    echo "Gagal update: " . mysqli_error($conn);
}
?>
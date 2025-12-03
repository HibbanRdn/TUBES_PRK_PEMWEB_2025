<?php
// Pastikan path ke file koneksi benar
require_once 'config/database.php';

// 1. Tentukan Password Baru
$password_plain = "123456";

// 2. Buat Hash Password (BCRYPT)
$password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Password Database</title>
    <style>
        body { font-family: sans-serif; padding: 40px; line-height: 1.6; background: #f4f4f4; }
        .container { background: white; padding: 30px; border-radius: 10px; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .success { color: green; background: #e6fffa; padding: 10px; border-radius: 5px; margin-bottom: 10px; border: 1px solid #b2f5ea; }
        .error { color: red; background: #fff5f5; padding: 10px; border-radius: 5px; margin-bottom: 10px; border: 1px solid #feb2b2; }
        .btn { display: inline-block; background: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; margin-top: 20px; font-weight: bold; }
        .btn:hover { background: #1d4ed8; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛠️ Fix Password Tool</h1>
        <p>Sedang mereset password untuk struktur database baru (Owners & Employees)...</p>
        <hr>

        <?php
        // --- UPDATE TABEL OWNERS ---
        $sql_owners = "UPDATE owners SET password = '$password_hash'";
        if (mysqli_query($conn, $sql_owners)) {
            $affected = mysqli_affected_rows($conn);
            echo "<div class='success'>✅ <b>Tabel Owners:</b> Berhasil! ($affected akun di-reset)</div>";
        } else {
            echo "<div class='error'>❌ <b>Tabel Owners:</b> Gagal - " . mysqli_error($conn) . "</div>";
        }

        // --- UPDATE TABEL EMPLOYEES ---
        $sql_employees = "UPDATE employees SET password = '$password_hash'";
        if (mysqli_query($conn, $sql_employees)) {
            $affected = mysqli_affected_rows($conn);
            echo "<div class='success'>✅ <b>Tabel Employees:</b> Berhasil! ($affected akun di-reset)</div>";
        } else {
            echo "<div class='error'>❌ <b>Tabel Employees:</b> Gagal - " . mysqli_error($conn) . "</div>";
        }
        ?>

        <hr>
        <p>
            Semua password kini menjadi: <strong><?php echo $password_plain; ?></strong><br>
            <small style="color: gray;">Hash Database: <?php echo $password_hash; ?></small>
        </p>

        <center>
            <a href="auth/login.php" class="btn">Login Sekarang &rarr;</a>
        </center>
    </div>
</body>
</html>
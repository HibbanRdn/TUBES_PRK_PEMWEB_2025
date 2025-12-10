<?php
// File: redirect.php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: /auth/login.php");
    exit;
}

$role = $_SESSION['role'] ?? '';

switch ($role) {
    case 'owner':
        header("Location: pages/owner/dashboard.php");
        break;

    case 'admin_gudang':
        header("Location: pages/admin_gudang/inventory.php");
        break;

    case 'kasir':
        header("Location: pages/kasir/kasir.php");
        break;

    default:
        session_destroy();
        header("Location: auth/login.php?error=unknown_role");
        break;
}

exit;
?>
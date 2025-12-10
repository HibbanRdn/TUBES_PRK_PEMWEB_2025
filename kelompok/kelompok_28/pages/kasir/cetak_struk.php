<?php
// FILE: pages/kasir/cetak_struk.php
session_start();
require('../../config/database.php');
require('../../library/fpdf.php');

// 1. Cek Akses
if (!isset($_SESSION['store_id']) || !isset($_GET['id'])) {
    die("Akses ditolak.");
}

$trx_id = $_GET['id'];
$store_id = $_SESSION['store_id'];

// 2. Ambil Data Header
$sql = "SELECT t.*, 
               s.name as store_name, 
               s.address as store_address, 
               s.phone as store_phone, 
               e.fullname as cashier_name
        FROM transactions t
        JOIN stores s ON t.store_id = s.id
        LEFT JOIN employees e ON t.employee_id = e.id
        WHERE t.id = ? AND t.store_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $trx_id, $store_id);
$stmt->execute();
$trx = $stmt->get_result()->fetch_assoc();

if (!$trx) { die("Data tidak ditemukan."); }

// 3. Ambil Item & Simpan ke Array 
$sql_items = "SELECT td.qty, td.price_at_transaction, td.subtotal, p.name 
              FROM transaction_details td
              JOIN products p ON td.product_id = p.id
              WHERE td.transaction_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $trx_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();

$items = [];
while ($row = $result_items->fetch_assoc()) {
    $items[] = $row;
}

// 4. Hitung Tinggi Kertas Dinamis
$height_per_item = 10; 
$base_height = 80; 
$paper_height = $base_height + (count($items) * $height_per_item);

// 5. Setup PDF 
$pdf = new FPDF('P', 'mm', array(80, $paper_height));
$pdf->AddPage();
$pdf->SetMargins(4, 2, 4);
$pdf->SetAutoPageBreak(false);

function rp($angka) { return number_format($angka, 0, ',', '.'); }

// BAGIAN 1: Header Toko
$pdf->SetFont('Arial', 'B', 15);
$pdf->SetX(0); 
$pdf->Cell(80, 6, strtoupper($trx['store_name']), 0, 1, 'C');

$pdf->SetFont('Arial', '', 9);
$pdf->SetX(0);
$pdf->MultiCell(80, 4, $trx['store_address'], 0, 'C');

if (!empty($trx['store_phone'])) {
    $pdf->SetX(0);
    $pdf->Cell(80, 4, 'Telp: ' . $trx['store_phone'], 0, 1, 'C');
}

$pdf->Ln(2);
$pdf->SetX(2); 
$pdf->Cell(76, 0, str_repeat('-', 64), 0, 1, 'C');
$pdf->Ln(3);

// BAGIAN 2: INFO
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(15, 4, 'No Struk', 0, 0);
$pdf->Cell(2, 4, ':', 0, 0);
$pdf->Cell(55, 4, $trx['invoice_code'], 0, 1);

$pdf->Cell(15, 4, 'Tanggal', 0, 0);
$pdf->Cell(2, 4, ':', 0, 0);
$pdf->Cell(55, 4, date('d/m/Y H:i', strtotime($trx['date'])), 0, 1);

$pdf->Cell(15, 4, 'Kasir', 0, 0);
$pdf->Cell(2, 4, ':', 0, 0);
$pdf->Cell(55, 4, substr($trx['cashier_name'] ?? 'Admin', 0, 18), 0, 1);

$pdf->Ln(2);
$pdf->SetX(2);
$pdf->Cell(76, 0, str_repeat('-', 64), 0, 1, 'C');
$pdf->Ln(2);

// BAGIAN 3: ITEM
foreach ($items as $item) {
    // Baris 1: Nama Barang
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(72, 4, $item['name'], 0, 1, 'L');

    // Baris 2: Qty x Harga | Subtotal
    $pdf->SetFont('Arial', '', 9);
    $rincian = $item['qty'] . ' x ' . rp($item['price_at_transaction']);
    $subtotal = rp($item['subtotal']);

    $pdf->Cell(40, 4, '   ' . $rincian, 0, 0, 'L');
    $pdf->Cell(32, 4, $subtotal, 0, 1, 'R');
}

$pdf->Ln(2);
$pdf->SetX(2);
$pdf->Cell(76, 0, str_repeat('-', 64), 0, 1, 'C');
$pdf->Ln(2);

// --- BAGIAN 4: TOTAL ---
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(35, 5, 'TOTAL', 0, 0, 'L');
$pdf->Cell(37, 5, rp($trx['total_price']), 0, 1, 'R');

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(35, 4, 'Tunai', 0, 0, 'L');
$pdf->Cell(37, 4, rp($trx['cash_amount']), 0, 1, 'R');

$pdf->Cell(35, 4, 'Kembali', 0, 0, 'L');
$pdf->Cell(37, 4, rp($trx['change_amount']), 0, 1, 'R');

$pdf->Ln(4);
$pdf->SetX(2);
$pdf->Cell(76, 0, str_repeat('-', 64), 0, 1, 'C');
$pdf->Ln(4);

// --- BAGIAN 5: FOOTER ---
$pdf->SetFont('Arial', '', 9);
$pdf->SetX(0); // Center relative to page
$pdf->Cell(80, 4, 'Terima Kasih', 0, 1, 'C');

$pdf->SetFont('Arial', 'I', 7);
$pdf->SetX(0);
$pdf->Cell(80, 4, 'Barang yang dibeli tidak dapat ditukar', 0, 1, 'C');

$pdf->SetX(0);
$pdf->Cell(80, 4, 'DigiNiaga', 0, 1, 'C');

$pdf->Output('I', 'Struk-' . $trx['invoice_code'] . '.pdf');
?>
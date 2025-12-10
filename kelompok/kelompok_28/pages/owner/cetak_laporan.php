<?php
// FILE: cetak_laporan.php
$host = "localhost";
$user = "root"; 
$pass = "";     
$db   = "db_pos_sme";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$store_id = $_SESSION['store_id'] ?? 1; 

// Filter Tanggal
$start_date = $_GET['start'] ?? date('Y-m-01');
$end_date   = $_GET['end'] ?? date('Y-m-d');

// Ambil Data Toko
$sql_store = "SELECT * FROM stores WHERE id = ?";
$stmt = $conn->prepare($sql_store);
$stmt->bind_param("i", $store_id);
$stmt->execute();
$store_data = $stmt->get_result()->fetch_assoc();

if (!$store_data) die("Data toko tidak ditemukan.");

// PANGGIL LIBRARY FPDF
require('../../library/fpdf.php');

class PDF extends FPDF {
    public $storeName;
    public $storeAddress;
    public $storePhone;
    
    // Header Kertas (Kop Surat)
    function Header() {
        // Nama Toko (Besar & Tebal)
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, strtoupper($this->storeName), 0, 1, 'C');

        // Alamat & Telp
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, $this->storeAddress, 0, 1, 'C');
        $this->Cell(0, 5, 'Telp: ' . $this->storePhone, 0, 1, 'C');

        // Garis Ganda (Kop Surat Resmi)
        $this->Ln(5);
        $this->SetLineWidth(1); // Garis tebal
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->SetLineWidth(0.2); // Garis tipis
        $this->Line(10, $this->GetY()+1, 200, $this->GetY()+1);
        $this->Ln(8);

        // Judul Laporan
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 6, 'LAPORAN TRANSAKSI PENJUALAN', 0, 1, 'C');
        
        // Periode
        global $start_date, $end_date;
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 6, 'Periode: ' . date('d/m/Y', strtotime($start_date)) . ' s/d ' . date('d/m/Y', strtotime($end_date)), 0, 1, 'C');
        $this->Ln(5);

        // --- HEADER TABEL ---
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(230, 230, 230); // Abu-abu muda
        $this->SetTextColor(0,0,0); // Teks Hitam
        $this->SetDrawColor(0,0,0); // Garis Hitam

        // Lebar Kolom (Total = 190mm)
        // No(10) + Waktu(35) + Invoice(40) + Kasir(60) + Total(45)
        $this->Cell(10, 8, 'NO', 1, 0, 'C', true);
        $this->Cell(35, 8, 'WAKTU', 1, 0, 'C', true);
        $this->Cell(40, 8, 'NO INVOICE', 1, 0, 'C', true);
        $this->Cell(60, 8, 'KASIR / STAFF', 1, 0, 'L', true);
        $this->Cell(45, 8, 'TOTAL (Rp)', 1, 1, 'R', true);
    }

    // Footer Halaman (Nomor Halaman)
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Halaman ' . $this->PageNo() . ' dari {nb} | Dicetak oleh DigiNiaga', 0, 0, 'C');
    }
}

// 4. GENERATE PDF
$pdf = new PDF('P', 'mm', 'A4');
$pdf->storeName = $store_data['name'];
$pdf->storeAddress = $store_data['address'];
$pdf->storePhone = $store_data['phone'];

$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);

// 5. QUERY TRANSAKSI
$sql = "SELECT 
            t.invoice_code, 
            t.date, 
            t.total_price, 
            e.fullname as kasir_name
        FROM transactions t
        LEFT JOIN employees e ON t.employee_id = e.id
        WHERE t.store_id = ? 
        AND DATE(t.date) BETWEEN ? AND ?
        ORDER BY t.date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $store_id, $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

$no = 1;
$grand_total = 0;

// 6. LOOP DATA
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(10, 7, $no++, 1, 0, 'C');
        $pdf->Cell(35, 7, date('d/m/Y H:i', strtotime($row['date'])), 1, 0, 'C');
        $pdf->Cell(40, 7, $row['invoice_code'], 1, 0, 'C');
        
        // Nama kasir
        $kasir = $row['kasir_name'] ? $row['kasir_name'] : '-';
        $pdf->Cell(60, 7, '  ' . substr($kasir, 0, 30), 1, 0, 'L'); 
        
        // Harga
        $pdf->Cell(45, 7, number_format($row['total_price'], 0, ',', '.') . '   ', 1, 1, 'R');
        
        $grand_total += $row['total_price'];
    }

    // BARIS TOTAL
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(255, 255, 200); // Kuning Muda
    
    // Label Total
    $pdf->Cell(145, 10, 'GRAND TOTAL PENJUALAN   ', 1, 0, 'R', true);
    
    // Nilai Total
    $pdf->Cell(45, 10, 'Rp ' . number_format($grand_total, 0, ',', '.') . '   ', 1, 1, 'R', true);

} else {
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(190, 15, 'Belum ada transaksi pada periode ini.', 1, 1, 'C');
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
}

// 7. TANDA TANGAN
$pdf->Ln(15); 
$pdf->SetFont('Arial', '', 10);

// Helper untuk Tanggal Indonesia
$bulanIndo = array(
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
);
$tglSekarang = date('d') . ' ' . $bulanIndo[date('m')] . ' ' . date('Y');

// Posisi Tanda Tangan
$pdf->SetX(140);
$pdf->Cell(50, 5, 'Metro, ' . $tglSekarang, 0, 1, 'C');

$pdf->SetX(140);
$pdf->Cell(50, 5, 'Pemilik Toko,', 0, 1, 'C');

$pdf->Ln(25); // TTD

$nama_pemilik = $_SESSION['fullname'] ?? 'Budi Santoso'; 

$pdf->SetX(140);

$pdf->SetFont('Arial', 'B', 10); 
$pdf->Cell(50, 5, '( ' . $nama_pemilik . ' )', 0, 1, 'C');

$pdf->Output('I', 'Laporan_DigiNiaga.pdf');
?>
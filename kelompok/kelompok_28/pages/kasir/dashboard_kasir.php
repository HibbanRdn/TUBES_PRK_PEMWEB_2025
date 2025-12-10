<?php
// FILE: pages/kasir/history.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$host = 'localhost';
$user = 'root'; // Sesuaikan user DB
$pass = '';     // Sesuaikan password DB
$db   = 'db_pos_sme';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Cek Login & Store ID
if (!isset($_SESSION['store_id'])) {
    if (isset($_SESSION['user_id'])) { 
        $emp_id = $_SESSION['user_id'];
        $stmt_emp = $conn->prepare("SELECT store_id, fullname FROM employees WHERE id = ?");
        $stmt_emp->bind_param("i", $emp_id);
        $stmt_emp->execute();
        $res_emp = $stmt_emp->get_result();
        if ($row_emp = $res_emp->fetch_assoc()) {
            $_SESSION['store_id'] = $row_emp['store_id'];
            $_SESSION['fullname'] = $row_emp['fullname'];
        }
    } else {
        header("Location: ../../auth/login.php");
        exit();
    }
}

$store_id = $_SESSION['store_id'];
$fullname = $_SESSION['fullname'] ?? 'Kasir';

// 2. Logika Filter Tanggal
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// 3. Ambil Ringkasan (Summary) Hari Ini/Tanggal Terpilih
$sql_summary = "SELECT 
                    COUNT(id) as total_trx, 
                    SUM(total_price) as total_omset 
                FROM transactions 
                WHERE store_id = ? AND DATE(date) = ?";
$stmt_sum = $conn->prepare($sql_summary);
$stmt_sum->bind_param("is", $store_id, $selected_date);
$stmt_sum->execute();
$res_sum = $stmt_sum->get_result();
$summary_data = $res_sum->fetch_assoc();

$summary = [
    'total_trx' => $summary_data['total_trx'] ?? 0,
    'total_omset' => $summary_data['total_omset'] ?? 0
];

// 4. Ambil Daftar Transaksi & Detail Item
$sql_trx = "SELECT * FROM transactions 
            WHERE store_id = ? AND DATE(date) = ? 
            ORDER BY date DESC";
$stmt_trx = $conn->prepare($sql_trx);
$stmt_trx->bind_param("is", $store_id, $selected_date);
$stmt_trx->execute();
$result_trx = $stmt_trx->get_result();

$transactions = [];

while ($row = $result_trx->fetch_assoc()) {
    $trx_id = $row['id'];
    
    $sql_details = "SELECT td.qty, td.price_at_transaction, td.subtotal, p.name 
                    FROM transaction_details td
                    JOIN products p ON td.product_id = p.id
                    WHERE td.transaction_id = ?";
    
    $stmt_details = $conn->prepare($sql_details);
    $stmt_details->bind_param("i", $trx_id);
    $stmt_details->execute();
    $res_details = $stmt_details->get_result();
    
    $items = [];
    while ($d = $res_details->fetch_assoc()) {
        $items[] = [
            'name' => $d['name'],
            'qty' => $d['qty'],
            'price' => $d['price_at_transaction'], 
            'subtotal' => $d['subtotal']
        ];
    }

    $transactions[] = [
        'id' => $row['id'],
        'code' => $row['invoice_code'], 
        'time' => date('H:i', strtotime($row['date'])), 
        'full_date' => $row['date'],
        'total' => $row['total_price'],
        'pay' => $row['cash_amount'],
        'change' => $row['change_amount'],
        'payment_method' => 'Tunai', 
        'items' => $items
    ];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - DigiNiaga</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-slideUp { animation: slideUp 0.5s ease-out forwards; }
        
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="min-h-screen text-gray-800 relative overflow-x-hidden">

    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
    </div>

    <nav class="glass-effect sticky top-0 z-40 px-6 py-4 flex justify-between items-center shadow-sm">
        <div class="flex items-center gap-3">
            <a href="kasir.php" class="p-2 rounded-xl hover:bg-gray-100 text-gray-500 hover:text-indigo-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="font-bold text-xl text-gray-800">Riwayat Transaksi</h1>
        </div>
        <div class="flex items-center gap-3">
            <div class="hidden md:block text-right">
                <p class="text-sm font-bold"><?= htmlspecialchars($fullname) ?></p>
                <p class="text-xs text-gray-500">Kasir</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold shadow-lg">
                <?= substr($fullname, 0, 1) ?>
            </div>
        </div>
    </nav>

    <main class="relative z-10 p-4 lg:p-8 max-w-7xl mx-auto space-y-6">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-slideUp">
            
            <div class="glass-effect rounded-2xl p-6 shadow-sm">
                <label class="block text-sm font-bold text-gray-500 mb-2 uppercase tracking-wide">Pilih Tanggal</label>
                <form action="" method="GET" class="flex gap-2">
                    <input type="date" name="date" value="<?= $selected_date ?>" 
                           class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl font-bold text-gray-700 focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                    <button type="submit" class="px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition-colors shadow-lg shadow-indigo-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </form>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-6 text-white shadow-lg shadow-blue-200 relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-32 h-32 bg-white opacity-10 rounded-full transform translate-x-10 -translate-y-10 group-hover:scale-110 transition-transform"></div>
                <p class="text-blue-100 font-medium mb-1">Total Transaksi</p>
                <h3 class="text-3xl font-bold"><?= $summary['total_trx'] ?> <span class="text-lg font-normal opacity-70">Struk</span></h3>
                <div class="mt-4 text-xs font-medium bg-white/20 inline-block px-2 py-1 rounded-lg">
                    <?= date('d M Y', strtotime($selected_date)) ?>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
                <div class="absolute right-0 bottom-0 w-24 h-24 bg-green-50 rounded-full mix-blend-multiply group-hover:scale-150 transition-transform duration-500"></div>
                <p class="text-gray-500 font-medium mb-1">Total Omset</p>
                <h3 class="text-3xl font-bold text-gray-800">Rp <?= number_format($summary['total_omset'], 0, ',', '.') ?></h3>
                <p class="text-xs text-green-600 mt-2 flex items-center gap-1 font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    Pendapatan Hari Ini
                </p>
            </div>
        </div>

        <div class="glass-effect rounded-2xl shadow-sm overflow-hidden animate-slideUp" style="animation-delay: 0.1s;">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="font-bold text-lg text-gray-800">Daftar Transaksi</h2>
                <button onclick="window.print()" class="text-sm text-gray-500 hover:text-indigo-600 font-medium flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print Laporan
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 text-gray-500 text-xs uppercase tracking-wider">
                            <th class="p-4 font-bold border-b border-gray-100">Waktu</th>
                            <th class="p-4 font-bold border-b border-gray-100">No. Struk</th>
                            <th class="p-4 font-bold border-b border-gray-100 text-right">Total</th>
                            <th class="p-4 font-bold border-b border-gray-100 text-center">Metode</th>
                            <th class="p-4 font-bold border-b border-gray-100 text-center">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php if(empty($transactions)): ?>
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        <p>Tidak ada transaksi pada tanggal ini.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($transactions as $trx): ?>
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="p-4 font-medium text-gray-600">
                                    <?= $trx['time'] ?>
                                </td>
                                <td class="p-4">
                                    <span class="font-bold text-gray-800 tracking-wide"><?= $trx['code'] ?></span>
                                </td>
                                <td class="p-4 text-right">
                                    <span class="font-bold text-indigo-600">Rp <?= number_format($trx['total'], 0, ',', '.') ?></span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-600 border border-green-200">
                                        TUNAI
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <button onclick='showDetail(<?= json_encode($trx) ?>)' 
                                            class="p-2 bg-white border border-gray-200 rounded-lg text-gray-500 hover:text-indigo-600 hover:border-indigo-200 transition-all shadow-sm active:scale-95">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="detailModal" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity opacity-0">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform scale-95 transition-transform duration-300" id="detailContent">
            
            <div class="bg-gray-50 p-5 border-b border-gray-100 flex justify-between items-start">
                <div>
                    <h3 class="font-bold text-lg text-gray-800">Detail Transaksi</h3>
                    <p class="text-xs text-gray-500 font-mono mt-1" id="modalTrxCode">INV/000</p>
                </div>
                <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 max-h-[60vh] overflow-y-auto custom-scrollbar">
                <div class="space-y-4">
                    <div id="modalItems" class="space-y-3">
                        </div>

                    <div class="border-t border-dashed border-gray-300 my-4"></div>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Total Belanja</span>
                            <span class="font-bold text-gray-800" id="modalTotal">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Tunai</span>
                            <span class="font-medium" id="modalPay">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-green-600 font-bold text-base pt-2">
                            <span>Kembalian</span>
                            <span id="modalChange">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-5 bg-gray-50 border-t border-gray-100 flex gap-3">
                <button onclick="printReceipt()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-indigo-200 flex justify-center items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Struk
                </button>
            </div>
        </div>
    </div>

    <script>
        const formatRupiah = (num) => 'Rp ' + parseInt(num).toLocaleString('id-ID');
        
        // TAMBAHAN BARU: Variabel Global untuk menyimpan ID transaksi yang sedang dibuka
        let currentTrxId = null; 

        function showDetail(trx) {
            const modal = document.getElementById('detailModal');
            const content = document.getElementById('detailContent');
            const itemsContainer = document.getElementById('modalItems');

            // TAMBAHAN BARU: Simpan ID Transaksi
            currentTrxId = trx.id; 

            // Set Header Data
            document.getElementById('modalTrxCode').innerText = `${trx.code} • ${trx.full_date}`;
            
            // Render Items
            itemsContainer.innerHTML = '';
            trx.items.forEach(item => {
                const html = `
                    <div class="flex justify-between items-start">
                        <div class="flex-1 pr-4">
                            <p class="text-sm font-bold text-gray-800">${item.name}</p>
                            <p class="text-xs text-gray-500">${item.qty} x ${formatRupiah(item.price)}</p>
                        </div>
                        <span class="text-sm font-medium text-gray-700">${formatRupiah(item.subtotal)}</span>
                    </div>
                `;
                itemsContainer.insertAdjacentHTML('beforeend', html);
            });

            // Set Summary Data
            document.getElementById('modalTotal').innerText = formatRupiah(trx.total);
            document.getElementById('modalPay').innerText = formatRupiah(trx.pay);
            document.getElementById('modalChange').innerText = formatRupiah(trx.change);

            // Show Modal
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);
        }

        function closeModal() {
            const modal = document.getElementById('detailModal');
            const content = document.getElementById('detailContent');
            
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            modal.classList.add('opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                currentTrxId = null; // Reset ID saat tutup modal
            }, 300);
        }

        // UPDATE FUNGSI PRINT 
        function printReceipt() {
            if (currentTrxId) {
                // Buka tab baru ke file cetak_struk.php dengan membawa ID transaksi
                window.open(`cetak_struk.php?id=${currentTrxId}`, '_blank');
            } else {
                alert('Terjadi kesalahan: ID Transaksi tidak ditemukan.');
            }
        }

        document.getElementById('detailModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('detailModal')) closeModal();
        });
        
        document.addEventListener('keydown', (e) => {
            if(e.key === 'Escape') closeModal();
        });
    </script>
</body>
</html>
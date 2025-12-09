<?php 
// FILE: pages/kasir/dashboard.php

// 1. Cek Session & Backend
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Hubungkan backend
require_once '../../process/process_cashier.php'; 

// 2. Ambil Data Transaksi Sukses (Flash Message)
$trx_data = isset($_SESSION['success_trx']) ? $_SESSION['success_trx'] : null;
if ($trx_data) { unset($_SESSION['success_trx']); }

// 3. Pastikan variable products ada
$products = $products ?? [];

// 4. Setup Header
$page_title = "Kasir - " . ($_SESSION['fullname'] ?? 'Staff');
require_once '../../layout/header.php';
require_once '../../layout/navbar.php';
?>

<div class="max-w-7xl mx-auto mt-6 px-4 sm:px-6 lg:px-8 relative z-10 h-[calc(100vh-140px)]">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-full pb-6">
        
        <div class="lg:col-span-2 flex flex-col gap-4 h-full">
            
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 sticky top-0 z-20">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="searchInput" placeholder="Cari nama barang (Tekan F2)..." 
                           class="block w-full pl-10 pr-4 py-3 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-blue-500 transition-all font-medium text-gray-700">
                </div>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar pr-2">
                <?php if (empty($products)): ?>
                    <div class="flex flex-col items-center justify-center h-64 text-gray-500">
                        <p class="font-bold text-lg">Produk Kosong</p>
                        <p class="text-sm">Belum ada data produk.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-3 gap-4" id="productGrid">
                        <?php foreach ($products as $p): ?>
                            <?php 
                                // PENTING: Gunakan json_encode agar string aman dari tanda kutip/enter yang merusak JS
                                // Contoh: Roti "O" Boy -> aman
                                $js_name = json_encode($p['name']); 
                                $price_fmt = number_format($p['price'], 0, ',', '.');
                            ?>
                            
                            <div onclick='addToCart(<?= $p['id'] ?>, <?= $js_name ?>, <?= $p['price'] ?>, <?= $p['stock'] ?>)' 
                                 class="product-card group bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-blue-300 transition-all cursor-pointer flex flex-col justify-between h-full relative select-none active:scale-95">
                                
                                <div>
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="text-[10px] font-bold bg-gray-100 text-gray-500 px-2 py-1 rounded uppercase tracking-wide">
                                            <?= htmlspecialchars($p['category_name'] ?? 'UMUM') ?>
                                        </span>
                                        
                                        <?php if($p['stock'] <= 0): ?>
                                            <span class="text-[10px] font-bold text-white bg-red-500 px-2 py-1 rounded">HABIS</span>
                                        <?php else: ?>
                                            <span class="text-[10px] font-bold text-gray-400 bg-gray-50 px-2 py-1 rounded">Stok <?= $p['stock'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <h3 class="font-bold text-gray-800 text-lg leading-tight mb-1 line-clamp-2 min-h-[44px]">
                                        <?= htmlspecialchars($p['name']) ?>
                                    </h3>
                                    <div class="text-xs text-gray-400 mb-2">Harga Satuan</div>
                                </div>

                                <div class="flex items-center justify-between mt-auto pt-2 border-t border-dashed border-gray-100">
                                    <span class="font-extrabold text-blue-600 text-lg">Rp<?= $price_fmt ?></span>
                                    <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </div>
                                </div>

                                <?php if($p['stock'] <= 0): ?>
                                    <div class="absolute inset-0 bg-white/60 backdrop-blur-[1px] flex items-center justify-center rounded-2xl cursor-not-allowed z-10" onclick="event.stopPropagation(); alert('Stok Habis!');">
                                        <span class="text-xs font-bold text-red-500 border border-red-500 px-3 py-1 rounded bg-white shadow-sm">STOK HABIS</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="lg:col-span-1 h-full">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 h-full flex flex-col relative overflow-hidden">
                
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-white z-10 shadow-sm">
                    <h2 class="text-lg font-extrabold text-gray-800 flex items-center gap-2">
                        <span>🛒</span> List Belanja 
                        <span id="cartCountBadge" class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full hidden">0</span>
                    </h2>
                    <button type="button" onclick="clearCart()" class="text-xs font-bold text-red-500 hover:bg-red-50 px-3 py-1.5 rounded transition-colors bg-red-50/50">Reset</button>
                </div>

                <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar bg-gray-50/30" id="cartItemsContainer">
                    <div id="emptyCartState" class="flex flex-col items-center justify-center h-full text-center opacity-40 mt-4">
                        <svg class="w-16 h-16 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <p class="text-sm font-medium text-gray-500">Keranjang masih kosong</p>
                    </div>
                </div>

                <div class="p-5 bg-white border-t border-gray-100 shadow-lg z-20">
                    
                    <div class="flex justify-between items-end mb-4">
                        <span class="text-gray-400 font-bold text-xs uppercase tracking-wider">TOTAL TAGIHAN</span>
                        <span class="text-2xl font-black text-gray-800" id="cartTotalDisplay">Rp 0</span>
                    </div>

                    <form action="../../process/process_cashier.php" method="POST" onsubmit="return validateTransaction()">
                        <div class="space-y-3 mb-4">
                            <div class="relative group">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm group-focus-within:text-blue-500">Rp</span>
                                <input type="number" id="payAmount" oninput="calculateChange()" placeholder="Masukan Uang diterima" 
                                    class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl font-bold text-gray-800 focus:border-blue-500 focus:ring-0 transition-all text-right text-lg">
                            </div>
                            
                            <div class="flex justify-between items-center px-2">
                                <span class="text-xs font-bold text-gray-400 uppercase">KEMBALI</span>
                                <span class="font-extrabold text-lg text-gray-400" id="changeAmountDisplay">Rp 0</span>
                            </div>
                        </div>

                        <input type="hidden" name="process_transaction" value="1">
                        <input type="hidden" name="cart_data" id="cartDataInput">
                        <input type="hidden" name="total_amount" id="totalAmountInput">
                        <input type="hidden" name="pay_amount" id="payAmountInput">
                        
                        <button type="submit" id="btnProcess" disabled class="w-full py-3.5 bg-gray-200 text-gray-400 rounded-xl font-bold shadow-none flex items-center justify-center gap-2 cursor-not-allowed transition-all duration-200 text-sm uppercase tracking-wider">
                            <span>💳</span> PROSES BAYAR
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="successModal" class="fixed inset-0 z-[999] hidden items-center justify-center bg-gray-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 to-green-400"></div>
        <div class="mx-auto w-20 h-20 mb-4 bg-green-50 rounded-full flex items-center justify-center">
             <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <h2 class="text-2xl font-black text-gray-800 mb-1">Berhasil!</h2>
        <p class="text-gray-400 text-sm mb-6">Transaksi tersimpan.</p>
        
        <div class="bg-gray-50 rounded-2xl p-5 mb-6 text-sm border border-gray-100">
            <div class="flex justify-between mb-2">
                <span class="text-gray-500">Total</span>
                <span class="font-bold text-gray-800" id="modalTotal">Rp 0</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-500">Kembalian</span>
                <span class="font-extrabold text-lg text-green-600" id="modalChange">Rp 0</span>
            </div>
        </div>
        <button onclick="document.getElementById('successModal').classList.add('hidden')" class="w-full py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700">Tutup</button>
    </div>
</div>

<script>
    // 1. Inisialisasi Keranjang
    let cart = [];

    // Cek Modal Sukses
    const successData = <?= json_encode($trx_data) ?>;
    if (successData) {
        document.getElementById('successModal').classList.remove('hidden');
        document.getElementById('successModal').classList.add('flex');
        document.getElementById('modalTotal').innerText = formatRupiah(successData.total);
        document.getElementById('modalChange').innerText = formatRupiah(successData.change);
    }

    function formatRupiah(num) {
        return 'Rp ' + parseInt(num).toLocaleString('id-ID');
    }

    // --- 2. FUNGSI UTAMA: ADD TO CART ---
    function addToCart(id, name, price, stock) {
        // Debugging di Console untuk cek apakah fungsi terpanggil
        console.log("Menambah Item:", id, name, price);

        // Paksa konversi tipe data (Agar tidak error string vs int)
        id = parseInt(id);
        price = parseFloat(price);
        stock = parseInt(stock);

        if (stock <= 0) { alert('Stok Habis!'); return; }

        // Cari apakah barang sudah ada?
        const existingItem = cart.find(item => item.id === id);

        if (existingItem) {
            // JIKA ADA: Tambah Quantity
            if (existingItem.qty < stock) {
                existingItem.qty++;
            } else {
                alert('Stok maksimal tercapai!');
            }
        } else {
            // JIKA BELUM: Masukkan Barang Baru
            cart.push({
                id: id,
                name: name,
                price: price,
                qty: 1,
                stock: stock
            });
        }

        // Render ulang tampilan
        updateCartUI();
    }

    // --- 3. FUNGSI UPDATE QTY (PLUS MINUS DI KERANJANG) ---
    function updateQty(id, change) {
        const index = cart.findIndex(item => item.id === id);
        if (index !== -1) {
            const item = cart[index];
            const newQty = item.qty + change;

            if (newQty > 0 && newQty <= item.stock) {
                item.qty = newQty;
            } else if (newQty <= 0) {
                if (confirm('Hapus ' + item.name + ' dari keranjang?')) {
                    cart.splice(index, 1);
                }
            } else {
                alert('Stok tidak cukup!');
            }
        }
        updateCartUI();
    }

    // --- 4. FUNGSI RENDER TAMPILAN (MEMBUAT HTML) ---
    function updateCartUI() {
        const container = document.getElementById('cartItemsContainer');
        const emptyState = document.getElementById('emptyCartState');
        const badge = document.getElementById('cartCountBadge');

        // Bersihkan Kontainer
        container.innerHTML = '';

        // Cek jika kosong
        if (cart.length === 0) {
            container.appendChild(emptyState);
            emptyState.classList.remove('hidden');
            badge.classList.add('hidden');
            calculateChange();
            return;
        }

        // Jika ada isi
        emptyState.classList.add('hidden');
        badge.innerText = cart.length;
        badge.classList.remove('hidden');

        // Generate HTML untuk setiap item
        // Menggunakan createElement agar lebih stabil daripada innerHTML string
        cart.forEach(item => {
            const subtotal = item.price * item.qty;
            
            // Template String HTML
            const itemHTML = `
                <div class="flex-1 pr-2 overflow-hidden">
                    <h4 class="font-bold text-gray-800 text-sm truncate" title="${item.name}">${item.name}</h4>
                    <div class="text-[10px] text-gray-400 mt-0.5">@ ${item.price.toLocaleString('id-ID')}</div>
                </div>
                
                <div class="flex items-center gap-1 bg-gray-50 p-1 rounded-lg border border-gray-200 shrink-0">
                    <button onclick="updateQty(${item.id}, -1)" class="w-7 h-7 flex items-center justify-center bg-white rounded shadow-sm text-gray-500 font-bold hover:text-red-600">-</button>
                    <div class="w-8 text-center text-sm font-bold text-gray-700 select-none">${item.qty}</div>
                    <button onclick="updateQty(${item.id}, 1)" class="w-7 h-7 flex items-center justify-center bg-white rounded shadow-sm text-blue-600 font-bold hover:bg-blue-50">+</button>
                </div>

                <div class="ml-3 font-bold text-blue-600 text-sm min-w-[70px] text-right shrink-0">
                    ${subtotal.toLocaleString('id-ID')}
                </div>
            `;

            const div = document.createElement('div');
            div.className = "bg-white p-3 rounded-2xl border border-gray-100 flex justify-between items-center shadow-sm mb-2 hover:shadow-md transition-all";
            div.innerHTML = itemHTML;
            container.appendChild(div);
        });

        calculateChange();
    }

    // --- 5. HITUNG TOTAL & KEMBALIAN ---
    function calculateChange() {
        const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        const payInput = document.getElementById('payAmount');
        const payValue = parseFloat(payInput.value) || 0;
        const change = payValue - total;

        document.getElementById('cartTotalDisplay').innerText = formatRupiah(total);
        const changeLabel = document.getElementById('changeAmountDisplay');
        const btn = document.getElementById('btnProcess');

        if (cart.length > 0 && payValue >= total) {
            changeLabel.innerText = formatRupiah(change);
            changeLabel.className = "font-extrabold text-lg text-green-600";
            
            btn.disabled = false;
            btn.classList.remove('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
            btn.classList.add('bg-blue-600', 'text-white', 'hover:bg-blue-700', 'shadow-lg');
        } else {
            changeLabel.innerText = (total > 0 && payValue > 0) ? "Kurang " + formatRupiah(Math.abs(change)) : "Rp 0";
            changeLabel.className = (total > 0 && payValue > 0) ? "font-extrabold text-lg text-red-500" : "font-extrabold text-lg text-gray-400";
            
            btn.disabled = true;
            btn.classList.add('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
            btn.classList.remove('bg-blue-600', 'text-white', 'hover:bg-blue-700');
        }

        // Simpan ke Input Hidden untuk PHP
        document.getElementById('cartDataInput').value = JSON.stringify(cart);
        document.getElementById('totalAmountInput').value = total;
        document.getElementById('payAmountInput').value = payValue;
    }

    function clearCart() {
        if(cart.length > 0 && confirm('Reset keranjang?')) {
            cart = [];
            document.getElementById('payAmount').value = '';
            updateCartUI();
        }
    }

    function validateTransaction() {
        if (cart.length === 0) { alert('Keranjang kosong!'); return false; }
        const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        const pay = parseFloat(document.getElementById('payAmount').value) || 0;
        if (pay < total) { alert('Uang kurang!'); return false; }
        return true;
    }

    // Search & F2
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        const term = e.target.value.toLowerCase();
        document.querySelectorAll('#productGrid .product-card').forEach(card => {
            const name = card.querySelector('h3').innerText.toLowerCase();
            card.style.display = name.includes(term) ? 'flex' : 'none';
        });
    });
    document.addEventListener('keydown', e => {
        if(e.key === 'F2') { e.preventDefault(); document.getElementById('searchInput').focus(); }
    });
</script>

<?php require_once '../../layout/footer.php'; ?>
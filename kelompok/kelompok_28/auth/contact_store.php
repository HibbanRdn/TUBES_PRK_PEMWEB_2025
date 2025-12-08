<?php
// --- BACKEND: Menangani Request Pencarian (AJAX) ---
if (isset($_GET['action']) && $_GET['action'] == 'search') {
    
    // Matikan error report visual agar tidak merusak format JSON
    error_reporting(0);
    ini_set('display_errors', 0);
    header('Content-Type: application/json');

    // 1. CEK PATH DATABASE (Agar tidak error 404/500)
    $path_subfolder = '../config/database.php';
    $path_root      = 'config/database.php';

    if (file_exists($path_subfolder)) {
        require_once $path_subfolder;
    } elseif (file_exists($path_root)) {
        require_once $path_root;
    } else {
        echo json_encode(['error' => 'File database.php tidak ditemukan. Cek struktur folder.']);
        exit;
    }

    // Cek koneksi dari file database.php
    if (!isset($conn)) {
        echo json_encode(['error' => 'Variabel $conn tidak ditemukan di database.php']);
        exit;
    }

    $q = isset($_GET['q']) ? $_GET['q'] : '';
    $search = "%" . $q . "%";
    
    // 2. QUERY DATABASE
    $sql = "SELECT s.name as store_name, s.address, o.fullname as owner_name, o.email 
            FROM stores s 
            JOIN owners o ON s.owner_id = o.id 
            WHERE s.name LIKE ? 
            LIMIT 10";
            
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $search);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = array();
        
        while ($row = $result->fetch_assoc()) {
            // 3. KEAMANAN (Sanitasi XSS)
            // Membersihkan data agar aman ditampilkan di HTML
            $safe_row = array_map(function($value) {
                return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
            }, $row);
            
            $data[] = $safe_row;
        }
        
        echo json_encode($data);
        $stmt->close();
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Gagal menyiapkan query database']);
    }
    
    exit; // Stop eksekusi HTML di bawah
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Toko - DigiNiaga</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: { brand: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb' } }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in-down { animation: fadeInDown 0.6s ease-out forwards; }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen flex flex-col items-center pt-20 px-4 font-sans">

    <div class="text-center mb-10 animate-fade-in-down">
        <h1 class="text-4xl font-extrabold text-gray-800 mb-3 tracking-tight">Cari Toko Anda</h1>
        <p class="text-gray-500 text-lg">Temukan toko tempat Anda bekerja untuk menghubungi Owner.</p>
    </div>

    <div class="w-full max-w-lg relative mb-8 z-10">
        <div class="absolute inset-y-0 left-0 flex items-center pl-5 pointer-events-none">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
        <input type="text" id="searchInput" 
            class="block w-full pl-14 pr-12 py-5 text-gray-900 border border-gray-200 rounded-2xl bg-white/80 backdrop-blur-sm shadow-xl focus:ring-4 focus:ring-brand-100 focus:border-brand-500 transition-all text-lg placeholder-gray-400 outline-none" 
            placeholder="Ketik nama toko (contoh: Makmur Jaya)..." autocomplete="off" autofocus>
            
        <div id="loading" class="absolute inset-y-0 right-0 flex items-center pr-5 hidden">
            <svg class="animate-spin h-6 w-6 text-brand-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>

    <div id="resultsContainer" class="w-full max-w-lg space-y-4 pb-20">
        <div id="emptyState" class="text-center py-12 opacity-40">
            <div class="w-20 h-20 bg-gray-200 rounded-full mx-auto flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <p class="text-gray-500 font-medium">Silakan ketik nama toko di atas.</p>
        </div>
    </div>

    <a href="login.php" class="fixed bottom-8 bg-white px-6 py-3 rounded-full shadow-lg text-sm font-semibold text-gray-600 hover:text-brand-600 hover:shadow-xl transition-all flex items-center border border-gray-100 transform hover:-translate-y-1">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Login
    </a>

    <script>
        const searchInput = document.getElementById('searchInput');
        const resultsContainer = document.getElementById('resultsContainer');
        const emptyState = document.getElementById('emptyState');
        const loading = document.getElementById('loading');
        let timeout = null;

        searchInput.addEventListener('input', function(e) {
            clearTimeout(timeout);
            const query = e.target.value.trim();

            if (query.length === 0) {
                resultsContainer.innerHTML = '';
                resultsContainer.appendChild(emptyState);
                emptyState.classList.remove('hidden');
                loading.classList.add('hidden');
                return;
            }

            loading.classList.remove('hidden');
            emptyState.classList.add('hidden');

            // Debounce
            timeout = setTimeout(() => {
                fetch(`?action=search&q=${encodeURIComponent(query)}`)
                    .then(response => {
                        // Cek apakah response valid JSON
                        const contentType = response.headers.get("content-type");
                        if (contentType && contentType.indexOf("application/json") !== -1) {
                            return response.json();
                        } else {
                            // Jika bukan JSON, mungkin ada error PHP fatal
                            throw new Error("Respons bukan JSON. Cek path database.");
                        }
                    })
                    .then(data => {
                        loading.classList.add('hidden');
                        if(data.error) {
                            // Jika backend kirim pesan error spesifik
                            resultsContainer.innerHTML = `<p class="text-red-500 text-center text-sm bg-red-50 p-3 rounded-lg border border-red-200">${data.error}</p>`;
                        } else {
                            renderResults(data);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        loading.classList.add('hidden');
                        resultsContainer.innerHTML = `
                            <div class="text-center p-4 bg-red-50 rounded-xl border border-red-100">
                                <p class="text-red-600 font-medium text-sm">Gagal memuat data.</p>
                                <p class="text-xs text-red-400 mt-1">Cek Console (F12) untuk detail error.</p>
                            </div>`;
                    });
            }, 500);
        });

        function renderResults(stores) {
            resultsContainer.innerHTML = '';

            if (!Array.isArray(stores) || stores.length === 0) {
                resultsContainer.innerHTML = `
                    <div class="text-center py-10 bg-white rounded-2xl border border-gray-100 shadow-sm animate-fadeInUp">
                        <p class="text-gray-800 font-semibold">Toko tidak ditemukan.</p>
                        <p class="text-sm text-gray-400 mt-1">Pastikan ejaan nama toko benar.</p>
                    </div>
                `;
                return;
            }

            stores.forEach((store, index) => {
                const subject = encodeURIComponent(`Permintaan Akun Staff - ${store.store_name}`);
                const body = encodeURIComponent(
`Halo Bapak/Ibu ${store.owner_name},

Saya ingin mengajukan permohonan untuk dibuatkan akun Staff/Kasir pada sistem DigiNiaga untuk toko:
Nama Toko: ${store.store_name}
Alamat: ${store.address || '-'}

Mohon informasinya untuk username dan password saya. Terima kasih.`
                );

                const mailtoLink = `mailto:${store.email}?subject=${subject}&body=${body}`;
                const delay = index * 100;

                const card = `
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg hover:border-brand-100 transition-all duration-300 flex justify-between items-center group cursor-default" 
                         style="animation: fadeInUp 0.5s ease-out forwards; animation-delay: ${delay}ms; opacity: 0; transform: translateY(10px);">
                        <div class="pr-4">
                            <h3 class="font-bold text-gray-800 text-lg group-hover:text-brand-600 transition-colors">${store.store_name}</h3>
                            <div class="flex items-start text-sm text-gray-500 mt-2">
                                <svg class="w-4 h-4 mr-1.5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span class="leading-tight">${store.address ? store.address : 'Alamat tidak tersedia'}</span>
                            </div>
                            <div class="text-xs text-gray-400 mt-2 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Owner: <span class="font-medium text-gray-600 ml-1">${store.owner_name}</span>
                            </div>
                        </div>
                        <a href="${mailtoLink}" class="flex flex-col items-center justify-center bg-blue-50 hover:bg-brand-600 text-brand-600 hover:text-white px-5 py-3 rounded-xl transition-all duration-300 shadow-sm hover:shadow-md flex-shrink-0">
                            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span class="text-xs font-bold">Email</span>
                        </a>
                    </div>
                `;
                resultsContainer.insertAdjacentHTML('beforeend', card);
            });
        }
    </script>
    <style>
        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</body>
</html>
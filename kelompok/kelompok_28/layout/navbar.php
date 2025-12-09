<nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-50 shadow-sm">
    <div class="flex items-center gap-3 animate-fadeIn">
        <div class="relative">
            <div class="h-10 w-10 bg-gradient-to-br from-blue-100 to-purple-100 rounded-lg flex items-center justify-center border border-blue-50 shadow-sm relative z-10">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path> </svg>
            </div>
            <div class="absolute inset-0 bg-blue-400 blur-md opacity-20 rounded-lg"></div>
        </div>

        <div class="hidden md:block">
            <span class="block font-bold text-gray-800 text-lg leading-tight tracking-tight">
                <?= isset($store_name) ? htmlspecialchars($store_name) : 'DigiNiaga' ?>
            </span>
            
            <span class="block text-xs text-gray-500 font-medium mt-0.5 max-w-[250px] truncate">
                <?= isset($store_address) ? htmlspecialchars($store_address) : '' ?>
            </span>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <div class="text-right hidden sm:block">
            <p class="text-sm font-bold text-gray-700"><?= isset($_SESSION['fullname']) ? htmlspecialchars($_SESSION['fullname']) : 'User' ?></p>
            
            <p class="text-xs font-semibold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent uppercase tracking-wider">
                <?= isset($_SESSION['role']) ? htmlspecialchars(str_replace('_', ' ', $_SESSION['role'])) : 'STAFF' ?>
            </p>
        </div>
        
        <button onclick="confirmLogout()" class="text-red-500 hover:text-white hover:bg-red-500 transition-all duration-300 p-3 bg-red-50 rounded-xl hover-lift group relative" title="Keluar">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
        </button>
    </div>
</nav>
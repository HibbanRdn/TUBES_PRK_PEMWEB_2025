<div id="logoutModal" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="logoutBackdrop"></div>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="relative w-full max-w-md p-0 bg-white rounded-3xl shadow-2xl transform scale-95 opacity-0 transition-all duration-300 overflow-hidden" id="logoutPanel">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-red-500 to-orange-500"></div>
                <div class="absolute -right-10 -top-10 w-32 h-32 bg-red-50 rounded-full blur-2xl opacity-50"></div>
                <div class="p-8 text-center relative z-10">
                    <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm border border-red-100">
                        <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2 font-display">Konfirmasi Keluar</h3>
                    <p class="text-gray-500 text-sm mb-8 leading-relaxed">Anda yakin ingin mengakhiri sesi ini? <br>Anda harus login kembali untuk mengakses dashboard.</p>
                    <div class="flex gap-4">
                        <button onclick="closeLogoutModal()" class="w-full py-3.5 px-6 rounded-xl border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors duration-200">Batal</button>
                        
                        <a href="../../auth/logout.php" class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-red-600 to-orange-600 text-white font-bold hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2"><span>Ya, Keluar</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Script Global untuk Modal Logout
        const modal = document.getElementById('logoutModal');
        const backdrop = document.getElementById('logoutBackdrop');
        const panel = document.getElementById('logoutPanel');

        function confirmLogout() {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.add('modal-enter-active');
                backdrop.classList.remove('opacity-0');
                panel.classList.remove('scale-95', 'opacity-0');
                panel.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeLogoutModal() {
            backdrop.classList.add('opacity-0');
            panel.classList.remove('scale-100', 'opacity-100');
            panel.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('modal-enter-active');
                modal.classList.add('hidden');
            }, 300);
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) { closeLogoutModal(); }
        });
    </script>
</body>
</html>
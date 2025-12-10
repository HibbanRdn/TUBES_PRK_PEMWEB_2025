<?php
require_once '../config/database.php';
$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';
$error = '';
$valid_token = false;

// Validasi Token 
if ($token && $email) {
    $token_hash = hash('sha256', $token);
    $now = date("Y-m-d H:i:s");
    $sql = "SELECT id FROM owners WHERE email = ? AND reset_token_hash = ? AND reset_token_expires_at > ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "sss", $email, $token_hash, $now);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) == 1) {
            $valid_token = true;
        } else {
            $error = "Link reset password tidak valid atau sudah kadaluarsa.";
        }
    }
} else {
    $error = "Parameter tidak lengkap.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password Baru - DigiNiaga</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .input-glow:focus {
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            position: relative;
            overflow: hidden;
        }
        
        .btn-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }
        
        .btn-gradient:hover::before {
            left: 100%;
        }
        
        .password-strength {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen flex justify-center items-center p-4">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md animate-fadeInUp border border-gray-100">
        
        <div class="flex justify-center mb-6">
            <div class="bg-gradient-to-br from-brand-500 to-brand-700 rounded-full p-4 shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
            </div>
        </div>

        <h2 class="text-3xl font-bold mb-2 text-center gradient-text">Buat Password Baru</h2>
        <p class="text-gray-600 text-center mb-6 text-sm">Masukkan password baru yang aman dan mudah diingat</p>

        <?php if ($valid_token): ?>
            <form action="process_reset.php" method="POST" class="space-y-5" id="resetForm">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Password Baru</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input type="password" name="password" id="password" 
                               class="input-glow bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500 block w-full pl-12 pr-12 py-3.5 transition-all" 
                               placeholder="Minimal 6 karakter" required minlength="6">
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex items-center pr-4">
                            <svg id="eyeIcon" class="w-5 h-5 text-gray-400 hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="flex gap-1 mt-2">
                        <div class="password-strength flex-1 bg-gray-200" id="strength1"></div>
                        <div class="password-strength flex-1 bg-gray-200" id="strength2"></div>
                        <div class="password-strength flex-1 bg-gray-200" id="strength3"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1" id="strengthText">Gunakan kombinasi huruf, angka, dan simbol</p>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Konfirmasi Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <input type="password" name="conf_password" id="conf_password" 
                               class="input-glow bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500 block w-full pl-12 pr-4 py-3.5 transition-all" 
                               placeholder="Ketik ulang password" required>
                    </div>
                    <p class="text-xs text-red-500 mt-1 hidden" id="matchError">Password tidak cocok</p>
                </div>

                <button type="submit" 
                        class="btn-gradient w-full text-white font-bold rounded-xl text-sm px-5 py-4 text-center transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 active:translate-y-0 mt-6">
                    <span class="relative z-10 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Password Baru
                    </span>
                </button>
            </form>

        <?php else: ?>
            <div class="text-center space-y-4">
                <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 text-red-800 p-5 rounded-xl">
                    <svg class="w-12 h-12 text-red-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <p class="font-semibold mb-1">Link Tidak Valid</p>
                    <p class="text-sm"><?= $error ?></p>
                </div>
                
                <a href="forgot_password.php" 
                   class="inline-flex items-center text-sm font-semibold text-brand-600 hover:text-brand-700 transition-colors group">
                    <svg class="w-4 h-4 mr-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                    Minta Link Reset Baru
                </a>
            </div>
        <?php endif; ?>

        <div class="mt-6 text-center pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-500 mb-2">Sudah punya akun?</p>
            <a href="login.php" class="inline-flex items-center text-sm font-semibold text-gray-600 hover:text-brand-600 transition-colors group">
                <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Login
            </a>
        </div>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                if (type === 'text') {
                    eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
                } else {
                    eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
                }
            });
        }
        
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                const strength1 = document.getElementById('strength1');
                const strength2 = document.getElementById('strength2');
                const strength3 = document.getElementById('strength3');
                const strengthText = document.getElementById('strengthText');
                
                let strength = 0;
                if (password.length >= 6) strength++;
                if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
                if (password.match(/[0-9]/) && password.match(/[^a-zA-Z0-9]/)) strength++;
            
                strength1.className = 'password-strength flex-1 bg-gray-200';
                strength2.className = 'password-strength flex-1 bg-gray-200';
                strength3.className = 'password-strength flex-1 bg-gray-200';
                
                if (strength === 1) {
                    strength1.className = 'password-strength flex-1 bg-red-500';
                    strengthText.textContent = 'Password lemah';
                    strengthText.className = 'text-xs text-red-500 mt-1';
                } else if (strength === 2) {
                    strength1.className = 'password-strength flex-1 bg-yellow-500';
                    strength2.className = 'password-strength flex-1 bg-yellow-500';
                    strengthText.textContent = 'Password sedang';
                    strengthText.className = 'text-xs text-yellow-600 mt-1';
                } else if (strength === 3) {
                    strength1.className = 'password-strength flex-1 bg-green-500';
                    strength2.className = 'password-strength flex-1 bg-green-500';
                    strength3.className = 'password-strength flex-1 bg-green-500';
                    strengthText.textContent = 'Password kuat';
                    strengthText.className = 'text-xs text-green-600 mt-1';
                }
            });
        }
        
        const confPassword = document.getElementById('conf_password');
        if (confPassword) {
            confPassword.addEventListener('input', function() {
                const matchError = document.getElementById('matchError');
                if (this.value && this.value !== passwordInput.value) {
                    matchError.classList.remove('hidden');
                } else {
                    matchError.classList.add('hidden');
                }
            });
        }
        
        const resetForm = document.getElementById('resetForm');
        if (resetForm) {
            resetForm.addEventListener('submit', function(e) {
                const password = passwordInput.value;
                const confPass = confPassword.value;
                
                if (password !== confPass) {
                    e.preventDefault();
                    document.getElementById('matchError').classList.remove('hidden');
                    confPassword.focus();
                    return;
                }
                
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                submitBtn.disabled = true;
            });
        }
        
        const inputs = document.querySelectorAll('input[type="password"]');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('scale-[1.02]');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('scale-[1.02]');
            });
        });
    </script>

</body>
</html>
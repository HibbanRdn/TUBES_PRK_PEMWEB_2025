<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title : 'DigiNiaga' ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        .glass-effect { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(5px); }
        .animate-fadeIn { animation: fadeIn 0.6s ease-out; }
        .animate-slideUp { animation: slideUp 0.5s ease-out; }
        .animate-scaleIn { animation: scaleIn 0.4s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes scaleIn { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
        .hover-lift { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .hover-lift:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15); }
        .card-gradient { background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); }
        .stat-icon { position: relative; overflow: hidden; }
        .stat-icon::before { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%); animation: iconGlow 3s ease-in-out infinite; }
        @keyframes iconGlow { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(10px, 10px); } }
        .chart-container { position: relative; background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%); border-radius: 1.5rem; padding: 1.5rem; }
        .modal-enter { opacity: 0; pointer-events: none; transition: opacity 0.3s ease-out; }
        .modal-enter-active { opacity: 1; pointer-events: auto; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 10px; }
    </style>
</head>
<body class="min-h-screen pb-16">
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-50 rounded-full filter blur-3xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-50 rounded-full filter blur-3xl opacity-20"></div>
    </div>
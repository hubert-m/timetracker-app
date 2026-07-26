<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Odzyskiwanie hasła - {{ config('app.name', 'TimeTracker') }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .bg-pattern { background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px); background-size: 40px 40px; }
        .glass-panel { background: rgba(17, 24, 39, 0.7); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .animate-float { animation: float 6s ease-in-out infinite; }
        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-20px); } 100% { transform: translateY(0px); } }
    </style>
</head>
<body class="antialiased bg-gray-900 text-gray-100 selection:bg-indigo-500 selection:text-white relative overflow-x-hidden min-h-screen flex items-center justify-center">

    <div class="fixed inset-0 bg-pattern z-0 opacity-20 pointer-events-none"></div>
    <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-indigo-600 blur-[120px] opacity-40 animate-float pointer-events-none" style="animation-delay: 0s;"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-purple-600 blur-[150px] opacity-30 animate-float pointer-events-none" style="animation-delay: 2s;"></div>

    <div class="relative z-10 w-full max-w-md px-6 py-12">
        <div class="text-center mb-8">
            <a href="/" class="text-3xl font-extrabold tracking-tight text-white hover:opacity-80 transition-opacity">
                Time<span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">Tracker</span>
            </a>
            <p class="text-gray-400 text-sm mt-2 font-light">Odzyskaj dostęp do swojego konta</p>
        </div>

        <x-auth-box initialMode="forgot_password" />
    </div>

</body>
</html>

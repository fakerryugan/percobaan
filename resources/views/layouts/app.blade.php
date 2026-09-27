<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'The Wedding of Dimas & Sarah')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gold: {
                            50: '#fbf9f2',
                            100: '#f5f0df',
                            200: '#ebdebe',
                            300: '#dec697',
                            400: '#d0aa6e',
                            500: '#c5924b',
                            600: '#b07a3c',
                            700: '#8f5d32',
                            800: '#754a2e',
                            900: '#613e29',
                        },
                        navy: {
                            800: '#141c2b',
                            900: '#0b101b',
                            950: '#060910',
                        },
                        champagne: '#F8F5F0',
                        rosewood: '#65000B',
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', 'Georgia', 'serif'],
                        cursive: ['"Great Vibes"', 'cursive'],
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        cinzel: ['"Cinzel"', 'serif'],
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;800&family=Great+Vibes&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- QR Code & Confetti Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>

    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #c5924b;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #b07a3c;
        }

        .gold-gradient-text {
            background: linear-gradient(135deg, #d0aa6e 0%, #c5924b 50%, #8f5d32 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .gold-border {
            border-color: #c5924b;
        }

        .bg-gold-gradient {
            background: linear-gradient(135deg, #d0aa6e 0%, #c5924b 100%);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(208, 170, 110, 0.25);
        }

        .glass-card-dark {
            background: rgba(15, 23, 42, 0.88);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(208, 170, 110, 0.3);
        }

        /* Floating music icon animation */
        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.15); opacity: 0.4; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }
        .music-pulse {
            animation: pulse-ring 2s infinite ease-in-out;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-[#FAF7F2] text-stone-800 font-sans antialiased selection:bg-gold-200 selection:text-gold-900 min-h-screen">
    @yield('content')

    <!-- Flash message toast if any -->
    @if(session('success'))
        <div id="toast-success" class="fixed bottom-6 right-6 z-50 flex items-center p-4 bg-emerald-900/90 text-white rounded-xl shadow-2xl border border-emerald-500/40 max-w-md animate-bounce">
            <i class="fa-solid fa-circle-check text-emerald-400 text-xl mr-3"></i>
            <div>
                <p class="font-medium text-sm">{{ session('success') }}</p>
            </div>
            <button onclick="document.getElementById('toast-success').remove()" class="ml-4 text-emerald-300 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <script>
            setTimeout(() => {
                const el = document.getElementById('toast-success');
                if (el) el.remove();
            }, 6000);
        </script>
    @endif

    @stack('scripts')
</body>
</html>

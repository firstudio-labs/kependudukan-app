<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Desa Digital</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('app-icon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('app-icon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
</head>
<body class="relative min-h-screen overflow-x-hidden bg-gradient-to-br from-white to-[#fcf8fb]">
    <!-- Main Content -->
    <div class="container mx-auto px-4 py-10">
        <!-- Circle Blur Background - Changed from fixed to absolute positioning within a relative container -->
        <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden" aria-hidden="true">
            <!-- Simplified background elements - reduced blur -->
            <div class="absolute w-[300px] h-[300px] bg-[#D1D0EF] rounded-full opacity-90 blur-2xl top-20 left-20"></div>
            <div class="absolute w-[250px] h-[250px] bg-[#EEC1DD] rounded-full opacity-70 blur-2xl top-40 left-10"></div>
            <div class="absolute w-[300px] h-[300px] bg-[#969BE7] rounded-full opacity-60 blur-2xl bottom-40 left-1/5"></div>
        </div>

        <div class="relative z-10 max-w-4xl mx-auto">
            <h2 class="text-3xl font-bold text-black text-center mb-10 flex items-center justify-center">
                <img src="{{ asset('app-icon.ico') }}" alt="Logo" class="w-8 h-8 mr-3">
                Selamat Datang di Portal Layanan Desa
            </h2>

            <div class="mb-8 text-center">
                <p class="text-lg">Silakan isi formulir di bawah ini untuk mengajukan layanan desa</p>
            </div>

            <!-- Form with lazy loading -->
            <div>
                {{-- @if(count($provinces) > 100)
                    <!-- If provinces data is large, paginate or limit it -->
                    <x-pelayanan-form :provinces="$provinces->take(100)" :keperluanList="$keperluanList" />
                    <p class="text-sm text-gray-500 mt-2">* Menampilkan 100 provinsi pertama</p>
                @else --}}
                    <x-pelayanan-form :provinces="$provinces" :keperluanList="$keperluanList" />
                {{-- @endif --}}
            </div>
        </div>
    </div>

    <!-- Scripts - deferred loading -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize AOS with more memory-efficient settings
            AOS.init({
                once: true,  // Changed to true to run animations only once
                disable: window.innerWidth < 768, // Disable on mobile to save memory
                startEvent: 'DOMContentLoaded',
                disableMutationObserver: true, // Improve performance by disabling mutation observer
                throttleDelay: 99, // Increase throttle delay
                offset: 120
            });
        });
    </script>
    @stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Desa Digital</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
</head>
<body class="relative min-h-screen overflow-x-hidden bg-gradient-to-br from-white to-[#fcf8fb]">
    <!-- Main Content -->
    <div class="container mx-auto px-4 py-10">
        <!-- Circle Blur Background - Changed from fixed to absolute positioning within a relative container -->
        <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden" aria-hidden="true">
            <!-- Lingkaran 1 -->
            <div class="absolute w-[300px] h-[300px] bg-[#D1D0EF] rounded-full opacity-90 blur-3xl top-20 left-20"></div>

            <!-- Lingkaran 2 -->
            <div class="absolute w-[250px] h-[250px] bg-[#EEC1DD] rounded-full opacity-70 blur-3xl top-40 left-10"></div>

            <!-- Lingkaran 3 -->
            <div class="absolute w-[300px] h-[300px] bg-[#969BE7] rounded-full opacity-60 blur-3xl bottom-40 left-1/5"></div>
        </div>

        <div class="relative z-10 max-w-4xl mx-auto">
            <h2 class="text-3xl font-bold text-black text-center mb-10" data-aos="fade-down">Selamat Datang di Portal Layanan Desa</h2>

            <div class="mb-8 text-center" data-aos="fade-up">
                <p class="text-lg">Silakan isi formulir di bawah ini untuk mengajukan layanan desa</p>
            </div>

            <!-- Form langsung tanpa card -->
            <div data-aos="fade-up">
                <x-pelayanan-form :provinces="$provinces" :keperluanList="$keperluanList" />
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: false,
            mirror: true,
        });
    </script>
    @stack('scripts')
</body>
</html>

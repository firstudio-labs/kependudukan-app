<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'LADIMAS' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />

    {{ $head ?? '' }}
</head>
<body class="relative min-h-screen overflow-x-hidden">
    <!-- Circle Gradient Backgrounds -->
    <div class="pointer-events-none fixed top-0 left-0 w-[600px] h-[600px] bg-[#969BE7] rounded-full blur-3xl opacity-50 z-0 -translate-x-1/2 -translate-y-1/2"></div>
    <div class="pointer-events-none fixed top-0 right-0 w-[500px] h-[500px] bg-[#EEC1DD] rounded-full blur-3xl opacity-50 z-0 translate-x-1/3 -translate-y-1/3"></div>
    <div class="pointer-events-none fixed bottom-0 left-1/3 w-[700px] h-[700px] bg-[#D1D0EF] rounded-full blur-3xl opacity-50 z-0 -translate-x-1/2 translate-y-1/4"></div>
    <div class="pointer-events-none fixed bottom-0 right-0 w-[400px] h-[400px] bg-[#FCF8FB] rounded-full blur-3xl opacity-60 z-0 translate-x-1/4 translate-y-1/4"></div>

    <!-- Content Wrapper -->
    <div class="relative z-10">
        <!-- Include the guest navbar component -->
        <x-guest-navbar />

        <!-- Main Content -->
        {{ $slot }}

        <!-- Footer -->
        <x-guest-footer />
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: false, // animasi akan aktif setiap elemen masuk viewport
            mirror: true, // animasi muncul saat scroll ke atas
        });
    </script>

    {{ $scripts ?? '' }}
</body>
</html>

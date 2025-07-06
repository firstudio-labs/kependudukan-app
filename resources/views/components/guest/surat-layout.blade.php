@props(['title' => 'Portal Layanan Desa'])

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.6/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.6/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
</head>
<body class="relative min-h-screen overflow-x-hidden bg-gradient-to-br from-white to-[#fcf8fb]">
    <!-- Circle Blur Background -->
    <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden" aria-hidden="true">
        <!-- Lingkaran 1 -->
        <div class="absolute w-[300px] h-[300px] bg-[#D1D0EF] rounded-full opacity-90 blur-3xl top-20 left-20"></div>

        <!-- Lingkaran 2 -->
        <div class="absolute w-[250px] h-[250px] bg-[#EEC1DD] rounded-full opacity-70 blur-3xl top-40 left-10"></div>

        <!-- Lingkaran 3 -->
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-10">
        <div class="relative z-10 max-w-6xl mx-auto">
            {{ $slot }}
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="{{ asset('js/sweet-alert-utils.js') }}"></script>
    <script>
        // Initialize AOS animations
        AOS.init({
            once: false,
            mirror: true,
        });
    </script>
</body>
</html>

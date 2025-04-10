<!-- Guest Navbar Component -->
<nav class="sticky top-0 left-0 right-0 flex justify-between items-center px-4 sm:px-6 py-4 bg-transparent backdrop-blur-sm z-50 transition-all duration-300">
    <!-- Logo/Brand -->
    <a href="/" class="text-xl sm:text-2xl font-bold text-[#4F46E5]">Kependudukan</a>

    <!-- Desktop Navigation -->
    <div class="hidden md:flex items-center justify-end flex-1 gap-8">
        <ul class="flex gap-6 font-medium">
            <li><a href="{{ route('guest.pelayanan.index') }}" class="text-gray-800 hover:text-[#4F46E5] transition-colors">Pelayanan</a></li>
            <li><a href="#harga" class="text-gray-800 hover:text-[#4F46E5] transition-colors">Harga</a></li>
            <li><a href="#tentang" class="text-gray-800 hover:text-[#4F46E5] transition-colors">Tentang</a></li>
        </ul>

        <div class="flex gap-3">
            <a href="{{ route('login') }}" class="bg-white/80 text-gray-800 border border-gray-200/60 px-5 py-2 rounded-full shadow hover:shadow-md hover:border-[#969BE7] transition-all duration-200">Login</a>
            <a href="{{ route('register') }}" class="bg-[#969BE7] text-white px-5 py-2 rounded-full shadow-md hover:shadow-lg hover:bg-[#8286D9] transition-all duration-200">Daftar</a>
        </div>
    </div>

    <!-- Mobile menu button -->
    <button id="mobile-menu-button" class="md:hidden p-2 rounded-lg text-gray-700 hover:bg-gray-100/20 focus:outline-none focus:ring-2 focus:ring-gray-200/50 transition-all">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
    </button>
</nav>

<!-- Mobile Navigation Menu (Hidden by default) -->
<div id="mobile-menu" class="fixed inset-0 bg-white/95 backdrop-blur-lg z-[100] flex flex-col justify-center items-center transform translate-x-full transition-transform duration-300 ease-in-out">
    <!-- Close button -->
    <button id="close-menu-button" class="absolute top-6 right-6 p-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-all">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <!-- Brand/Logo in menu -->
    <div class="text-3xl font-bold text-[#4F46E5] mb-10">Kependudukan</div>

    <!-- Mobile Navigation Links -->
    <ul class="flex flex-col items-center gap-6 text-xl font-medium mb-10">
        <li><a href="{{ route('guest.pelayanan.index') }}" class="text-gray-800 hover:text-[#4F46E5] transition-colors">Pelayanan</a></li>
        <li><a href="#harga" class="text-gray-800 hover:text-[#4F46E5] transition-colors">Harga</a></li>
        <li><a href="#tentang" class="text-gray-800 hover:text-[#4F46E5] transition-colors">Tentang</a></li>
    </ul>

    <!-- Mobile Auth Buttons -->
    <div class="flex flex-col gap-4 w-64">
        <a href="{{ route('login') }}" class="bg-white text-gray-800 border border-gray-200 py-3 rounded-full shadow hover:shadow-md hover:border-[#969BE7] text-center transition-all duration-200">Login</a>
        <a href="{{ route('register') }}" class="bg-[#969BE7] text-white py-3 rounded-full shadow-md hover:shadow-lg hover:bg-[#8286D9] text-center transition-all duration-200">Daftar</a>
    </div>
</div>

<!-- Add JavaScript for mobile menu toggle with improved animation -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const closeMenuButton = document.getElementById('close-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const navbar = document.querySelector('nav');

        // Open menu with slide animation
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.remove('translate-x-full');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        });

        // Close menu with slide animation
        closeMenuButton.addEventListener('click', function() {
            mobileMenu.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.style.overflow = ''; // Re-enable scrolling
            }, 300); // Match the transition duration
        });

        // Close menu when clicking a navigation link
        const mobileNavLinks = mobileMenu.querySelectorAll('a');
        mobileNavLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.style.overflow = '';
                }, 300);
            });
        });

        // Add scroll effect to navbar
        window.addEventListener('scroll', function() {
            if (window.scrollY > 10) {
                navbar.classList.add('shadow-sm', 'py-3', 'backdrop-blur-md');
                navbar.classList.remove('py-4', 'backdrop-blur-sm');
            } else {
                navbar.classList.remove('shadow-sm', 'py-3', 'backdrop-blur-md');
                navbar.classList.add('py-4', 'backdrop-blur-sm');
            }
        });
    });
</script>

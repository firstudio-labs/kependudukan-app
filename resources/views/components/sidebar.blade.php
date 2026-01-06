<nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start">
                <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar"
                    type="button"
                    class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" fill-rule="evenodd"
                            d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                        </path>
                    </svg>
                </button>
                <a href="/" class="flex ms-2 md:me-24 items-center">
                    <img src="{{ asset('app-icon.ico') }}" alt="Logo" class="w-6 h-6 mr-2">
                    <span
                        class="self-center text-sm font-semibold sm:text-base whitespace-nowrap text-transparent bg-clip-text bg-gradient-to-r from-[#2D336B] to-[#4A56A6]">
                        LADIMAS
                    </span>
                </a>
            </div>
        </div>
    </div>
</nav>

<style>
    /* Enhanced smooth dropdown animations */
    #sidebar ul li {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        transform-origin: top;
    }

    /* Smooth dropdown container animations with protrusion effect */
    #sidebar ul li ul {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        z-index: 50;
        overflow: hidden;
        transform: scaleY(0) translateX(-8px) perspective(1000px) rotateY(-5deg);
        transform-origin: top left;
        opacity: 0;
        max-height: 0;
        padding: 0;
        margin: 0;
        border-radius: 0 0 8px 8px;
        box-shadow: -4px 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Active/open state for dropdowns with enhanced protrusion */
    #sidebar ul li ul.dropdown-open {
        transform: scaleY(1) translateX(0) perspective(1000px) rotateY(0deg);
        opacity: 1;
        max-height: 800px; /* Increased height for content */
        padding: 0.5rem 0;
        margin: 0.5rem 0;
        overflow-y: auto; /* Allow scrolling if content exceeds height */
        overflow-x: hidden;
        box-shadow: 4px 8px 20px rgba(45, 51, 107, 0.15),
                    0 4px 12px rgba(0, 0, 0, 0.1),
                    inset 0 1px 0 rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(45, 51, 107, 0.1);
    }

    /* Smooth icon rotation with bounce effect */
    #sidebar ul li button i.fa-chevron-down {
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        transform-origin: center;
    }

    .rotate-180 {
        transform: rotate(180deg) scale(1.1);
    }

    /* Enhanced hover effects for menu items */
    #sidebar ul li a, #sidebar ul li button {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    #sidebar ul li a:hover, #sidebar ul li button:hover {
        background: linear-gradient(135deg, rgba(45, 51, 107, 0.08) 0%, rgba(74, 86, 166, 0.05) 100%);
        transform: translateX(4px);
        box-shadow: 0 2px 8px rgba(45, 51, 107, 0.12);
    }

    /* Subtle glow effect on active items */
    #sidebar ul li a.bg-\[\#2D336B\] {
        box-shadow: 0 4px 12px rgba(45, 51, 107, 0.3);
        transform: translateX(8px);
    }

    /* Enhanced hover effect for active items */
    #sidebar ul li a.bg-\[\#2D336B\]:hover {
        background: linear-gradient(135deg, rgba(74, 86, 166, 0.9) 0%, rgba(45, 51, 107, 0.95) 100%);
        box-shadow: 0 6px 20px rgba(45, 51, 107, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        transform: translateX(12px) scale(1.02);
        color: #ffffff;
    }

    /* Smooth fade-in for dropdown items */
    #sidebar ul li ul li {
        opacity: 0;
        transform: translateY(-10px);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        transition-delay: calc(var(--item-index, 0) * 0.1s);
    }

    #sidebar ul li ul.dropdown-open li {
        opacity: 1;
        transform: translateY(0);
    }

    /* Enhanced nested dropdown animations and visual hierarchy */
    #sidebar ul li ul li ul {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        transform: scaleY(0) translateX(-15px);
        opacity: 0;
        max-height: 0;
        margin-left: 1rem !important;
        position: relative;
    }

    #sidebar ul li ul li ul.dropdown-open {
        transform: scaleY(1) translateX(0);
        opacity: 1;
        max-height: 500px;
    }

    /* Visual indicators for submenu hierarchy */
    #sidebar ul li ul {
        border-left: 3px solid transparent;
        background: linear-gradient(135deg, rgba(45, 51, 107, 0.05) 0%, rgba(74, 86, 166, 0.03) 100%);
        backdrop-filter: blur(1px);
    }

    #sidebar ul li ul.dropdown-open {
        border-left: 3px solid rgba(45, 51, 107, 0.3);
        background: linear-gradient(135deg, rgba(45, 51, 107, 0.08) 0%, rgba(74, 86, 166, 0.05) 100%);
        box-shadow: inset 0 2px 4px rgba(45, 51, 107, 0.1);
    }

    /* Enhanced submenu items styling */
    #sidebar ul li ul li {
        position: relative;
        padding-left: 0.25rem;
    }

    #sidebar ul li ul li::before {
        content: '';
        position: absolute;
        left: -0.5rem;
        top: 50%;
        transform: translateY(-50%);
        width: 6px;
        height: 6px;
        background: rgba(45, 51, 107, 0.4);
        border-radius: 50%;
        opacity: 0;
        transition: all 0.3s ease;
    }

    #sidebar ul li ul.dropdown-open li::before {
        opacity: 1;
        animation: fadeInDot 0.4s ease-out forwards;
    }

    @keyframes fadeInDot {
        0% {
            opacity: 0;
            transform: translateY(-50%) scale(0);
        }
        50% {
            opacity: 0.7;
            transform: translateY(-50%) scale(1.2);
        }
        100% {
            opacity: 1;
            transform: translateY(-50%) scale(1);
        }
    }

    /* Submenu depth indicators */
    #sidebar ul li ul li ul li::before {
        background: rgba(74, 86, 166, 0.5);
        width: 4px;
        height: 4px;
    }

    /* Enhanced hover effects for submenu items */
    #sidebar ul li ul li a:hover {
        background: linear-gradient(135deg, rgba(45, 51, 107, 0.1) 0%, rgba(74, 86, 166, 0.08) 100%);
        transform: translateX(6px);
        box-shadow: 0 2px 8px rgba(45, 51, 107, 0.15);
    }

    #sidebar ul li ul li button:hover {
        background: linear-gradient(135deg, rgba(45, 51, 107, 0.1) 0%, rgba(74, 86, 166, 0.08) 100%);
        transform: translateX(6px);
        box-shadow: 0 2px 8px rgba(45, 51, 107, 0.15);
    }

    /* Level 2 submenu specific styling */
    .submenu-level-2 {
        border-left: 2px solid rgba(74, 86, 166, 0.2) !important;
        background: linear-gradient(135deg, rgba(74, 86, 166, 0.04) 0%, rgba(45, 51, 107, 0.02) 100%) !important;
        margin-left: 0.5rem !important;
    }

    .submenu-level-2.dropdown-open {
        border-left: 2px solid rgba(74, 86, 166, 0.4) !important;
        background: linear-gradient(135deg, rgba(74, 86, 166, 0.06) 0%, rgba(45, 51, 107, 0.04) 100%) !important;
        box-shadow: inset 0 1px 3px rgba(74, 86, 166, 0.1);
    }

    /* Enhanced visual hierarchy indicators */
    #sidebar ul li ul li::after {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, rgba(45, 51, 107, 0.1), transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    #sidebar ul li ul.dropdown-open li:hover::after {
        opacity: 1;
    }

    /* Loading state animation */
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .loading {
        animation: pulse 1.5s ease-in-out infinite;
    }

    /* Smooth sidebar toggle animation */
    #sidebar {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Submenu protrusion indicators */
    #sidebar ul li ul::before {
        content: '';
        position: absolute;
        top: -4px;
        left: 1rem;
        width: 0;
        height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-bottom: 6px solid rgba(45, 51, 107, 0.1);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    #sidebar ul li ul.dropdown-open::before {
        opacity: 1;
    }

    /* Enhanced depth effect for nested submenus */
    #sidebar ul li ul li ul {
        transform: scaleY(0) translateX(-12px) translateZ(-10px) rotateY(-3deg);
        box-shadow: -6px 6px 16px rgba(0, 0, 0, 0.08);
        margin-left: 1.5rem !important;
    }

    #sidebar ul li ul li ul.dropdown-open {
        transform: scaleY(1) translateX(0) translateZ(0) rotateY(0deg);
        box-shadow: 6px 12px 24px rgba(74, 86, 166, 0.12),
                    0 6px 16px rgba(0, 0, 0, 0.08);
    }

    /* Pulse effect for active submenu indicators */
    @keyframes submenuPulse {
        0% { box-shadow: 0 0 0 0 rgba(45, 51, 107, 0.4); }
        70% { box-shadow: 0 0 0 3px rgba(45, 51, 107, 0); }
        100% { box-shadow: 0 0 0 0 rgba(45, 51, 107, 0); }
    }

    .submenu-active {
        animation: submenuPulse 2s infinite;
    }

    /* Enhanced mobile responsiveness */
    @media (max-width: 768px) {
        #sidebar ul li ul {
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 0 0 12px 12px;
        }

        #sidebar ul li a:hover, #sidebar ul li button:hover {
            transform: translateX(2px);
        }

        /* Reduce 3D effects on mobile for performance */
        #sidebar ul li ul {
            transform: scaleY(0) translateX(-4px);
            transform-origin: top;
        }

        #sidebar ul li ul.dropdown-open {
            transform: scaleY(1) translateX(0);
        }
    }
</style>

@php
    // Initialize user variable to avoid undefined variable error
    $user = null;
    $userType = null;

    if (Auth::guard('web')->check()) {
        $user = Auth::guard('web')->user();
        $userType = 'web';
    } elseif (Auth::guard('penduduk')->check()) {
        $user = Auth::guard('penduduk')->user();
        $userType = 'penduduk';
        $user->role = 'user';
    }
@endphp

@if ($user)
    <aside id="sidebar"
        class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0"
        aria-label="Sidebar">
        <div class="h-full px-3 pb-4 overflow-y-auto bg-white relative">
            <ul class="space-y-2 font-medium text-sm flex flex-col relative">
                {{-- Menu berdasarkan role --}}
                @if ($user->role == 'superadmin')
                    <li class="-ml-5">
                        <a href="{{ route('superadmin.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                    {{ request()->routeIs('superadmin.index')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B]' }}">
                            <i class="fa-solid fa-gauge-high text-lg transition-all duration-300"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

















                    <!-- Replace single Master User with Master Users dropdown -->
                    <li class="-ml-5">
                        <button type="button"
                            class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] "
                            data-dropdown="masterUsersDropdown">
                            <i class="fa-solid fa-users text-lg transition-all duration-300"></i>
                            <span>Master Users</span>
                            <i id="dropdown-icon-master-users"
                                class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="masterUsersDropdown" class="hidden space-y-2 pl-6 transition-all duration-300 ease-in-out overflow-hidden">
                            <!-- Users submenu -->
                            <li>
                                <a href="{{ route('superadmin.datamaster.user.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                        {{ request()->routeIs('superadmin.datamaster.user*')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B] ' }}">
                                    <span>Users</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Keep Master Penduduk as a separate dropdown -->
                    <li class="-ml-5">
                        <button type="button"
                            class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] "
                            data-dropdown="pendudukDropdown">
                            <i class="fa-solid fa-id-card text-lg transition-all duration-300"></i>
                            <span>Master Penduduk</span>
                            <i id="dropdown-icon-penduduk"
                                class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="pendudukDropdown" class="hidden space-y-2 pl-6 transition-all duration-300 ease-in-out overflow-hidden">
                            <li>
                                <a href="{{ route('superadmin.biodata.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                        {{ request()->routeIs('superadmin.biodata.index') || request()->routeIs('superadmin.biodata.create') || request()->routeIs('superadmin.biodata.edit') || request()->routeIs('superadmin.biodata.update') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Biodata</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('superadmin.datakk.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                        {{ request()->routeIs('superadmin.datakk.index') || request()->routeIs('superadmin.datakk.edit') || request()->routeIs('superadmin.datakk.update') || request()->routeIs('superadmin.datakk.detail') || request()->routeIs('superadmin.datakk.delete') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Data KK</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Tambah Data KK (menu terpisah) -->
                    <li class="-ml-5">
                        <a href="{{ route('superadmin.datakk.create') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                {{ request()->routeIs('superadmin.datakk.create') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                            <i class="fa-solid fa-plus text-lg transition-all duration-300"></i>
                            <span>Tambah Data KK & Biodata</span>
                        </a>
                    </li>

                    <!-- Kelola Aset dropdown -->
                    <li class="-ml-5">
                        <button type="button"
                            class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] "
                            data-dropdown="kelolaAsetDropdown">
                            <i class="fa-solid fa-boxes-stacked text-lg transition-all duration-300"></i>
                            <span>Kelola Aset</span>
                            <i id="dropdown-icon-kelola-aset"
                                class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="kelolaAsetDropdown" class="hidden space-y-2 pl-6 transition-all duration-300 ease-in-out overflow-hidden">
                            <li>
                                <a href="{{ route('superadmin.datamaster.klasifikasi.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                                            {{ request()->routeIs('superadmin.datamaster.klasifikasi*')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B] ' }}">
                                    <span>Klasifikasi</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('superadmin.datamaster.jenis-aset.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                                            {{ request()->routeIs('superadmin.datamaster.jenis-aset*')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B] ' }}">
                                    <span>Jenis Aset</span>
                                </a>
                            </li>
                        </ul>
                    </li>



                    <!-- In your sidebar component or layout -->
                    <li class="-ml-5">
                        <a href="{{ route('superadmin.datamaster.lapordesa.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                {{ request()->routeIs('superadmin.datamaster.lapordesa.*')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B] ' }}">
                            <i class="fa-solid fa-bullhorn text-lg transition-all duration-300"></i>
                            <span>Master Lapor Desa</span>
                        </a>
                    </li>

                    <!-- Master Surat (moved out but keeping its dropdown) -->
                    <li class="-ml-5">
                        <button type="button"
                            class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] "
                            data-dropdown="suratDropdown">
                            <i class="fa-solid fa-envelope text-lg transition-all duration-300"></i>
                            <span>Surat</span>
                            <i id="dropdown-icon-surat"
                                class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="suratDropdown" class="hidden space-y-2 pl-6 transition-all duration-300 ease-in-out overflow-hidden">
                            <li>
                                <a href="{{ route('superadmin.surat.administrasi.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('superadmin.surat.administrasi*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Surat Administrasi</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('superadmin.surat.kehilangan.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('superadmin.surat.kehilangan*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Surat Kehilangan</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('superadmin.surat.skck.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('superadmin.surat.skck*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Surat SKCK</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('superadmin.surat.domisili.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('superadmin.surat.domisili.index') || request()->routeIs('superadmin.surat.domisili.create') || request()->routeIs('superadmin.surat.domisili.edit') || request()->routeIs('superadmin.surat.domisili.detail') || request()->routeIs('superadmin.surat.domisili.delete') || request()->routeIs('superadmin.surat.domisili.pdf') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Surat Domisili</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('superadmin.surat.domisili-usaha.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('superadmin.surat.domisili-usaha.index') || request()->routeIs('superadmin.surat.domisili-usaha.create') || request()->routeIs('superadmin.surat.domisili-usaha.edit') || request()->routeIs('superadmin.surat.domisili-usaha.detail') || request()->routeIs('superadmin.surat.domisili-usaha.delete') || request()->routeIs('superadmin.surat.domisili-usaha.pdf') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Surat Domisili Usaha</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('superadmin.surat.ahli-waris.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('superadmin.surat.ahli-waris*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Surat Ahli Waris</span>
                                </a>
                            </li>
                            {{-- <li>
                                <a href="{{ route('superadmin.surat.kelahiran.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('superadmin.surat.kelahiran*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Surat Kelahiran</span>
                                </a>
                            </li> --}}
                            {{-- <li>
                                <a href="{{ route('superadmin.surat.kematian.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('superadmin.surat.kematian*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Surat Kematian</span>
                                </a>
                            </li> --}}
                            <li>
                                <a href="{{ route('superadmin.surat.keramaian.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('superadmin.surat.keramaian*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Izin Keramaian</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('superadmin.surat.rumah-sewa.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('superadmin.surat.rumah-sewa*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Surat Rumah Sewa</span>
                                </a>
                            </li>
                            {{-- <li>
                                <a href="{{ route('superadmin.surat.pengantar-ktp.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('superadmin.surat.pengantar-ktp*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Pengantar KTP</span>
                                </a>
                            </li> --}}
                        </ul>
                    </li>

                    <!-- New Master Surat dropdown -->
                    <li class="-ml-5">
                        <button type="button"
                            class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] "
                            data-dropdown="masterSuratDropdown">
                            <i class="fa-solid fa-file-signature text-lg transition-all duration-300"></i>
                            <span>Master Surat</span>
                            <i id="dropdown-icon-master-surat"
                                class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="masterSuratDropdown" class="hidden space-y-2 pl-6 transition-all duration-300 ease-in-out overflow-hidden">
                            <li>
                                <a href="{{ route('superadmin.datamaster.surat.penandatangan.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                        {{ request()->routeIs('superadmin.datamaster.surat.penandatangan*')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B] ' }}">
                                    <span>Penandatangan</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Master Keperluan dropdown -->
                    <li class="-ml-5">
                        <button type="button"
                            class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] "
                            data-dropdown="masterKeperluanDropdown">
                            <i class="fa-solid fa-clipboard-list text-lg transition-all duration-300"></i>
                            <span>Master Keperluan</span>
                            <i id="dropdown-icon-master-keperluan"
                                class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="masterKeperluanDropdown" class="hidden space-y-2 pl-6 transition-all duration-300 ease-in-out overflow-hidden">
                            <li>
                                <a href="{{ route('superadmin.datamaster.masterkeperluan.keperluan.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                        {{ request()->routeIs('superadmin.datamaster.masterkeperluan.keperluan*')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B] ' }}">
                                    <span>Keperluan</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Master Wilayah (moved out but keeping its dropdown) -->
                    <li class="-ml-5">
                        <button type="button"
                            class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] "
                            data-dropdown="wilayahDropdown">
                            <i class="fa-solid fa-map text-lg transition-all duration-300"></i>
                            <span>Master Wilayah</span>
                            <i id="dropdown-icon-wilayah"
                                class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="wilayahDropdown" class="hidden space-y-2 pl-6 transition-all duration-300 ease-in-out overflow-hidden">
                            <li>
                                <a href="{{ route('superadmin.datamaster.wilayah.provinsi.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                        {{ request()->routeIs('superadmin.datamaster.wilayah.provinsi*')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B] ' }}">
                                    <span>Provinsi</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('superadmin.datamaster.wilayah.kabupaten.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                        {{ request()->routeIs('superadmin.datamaster.wilayah.kabupaten*')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B] ' }}">
                                    <span>Kabupaten</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('superadmin.datamaster.wilayah.kecamatan.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                        {{ request()->routeIs('superadmin.datamaster.wilayah.kecamatan*')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B] ' }}">
                                    <span>Kecamatan</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('superadmin.datamaster.wilayah.desa.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                        {{ request()->routeIs('superadmin.datamaster.wilayah.desa*')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B] ' }}">
                                    <span>Desa</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                     <!-- Master Warungku (Superadmin) -->
                    <li class="-ml-5">
                        <a href="{{ route('superadmin.datamaster.warungku.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                {{ request()->routeIs('superadmin.datamaster.warungku.*')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B] ' }}">
                            <i class="fa-solid fa-store text-lg transition-all duration-300"></i>
                            <span>Master Warungku</span>
                        </a>
                    </li>

                    <!-- Pengguna Mobile (Superadmin) -->
                    <li class="-ml-5">
                        <a href="{{ route('superadmin.mobile-users.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                {{ request()->routeIs('superadmin.mobile-users.*')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B] ' }}">
                            <i class="fa-solid fa-mobile-screen-button text-lg transition-all duration-300"></i>
                            <span>Pengguna Mobile</span>
                        </a>
                    </li>

                    <!-- Add Logout Button for Superadmin -->
                    <li class="-ml-5 mt-8">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-red-100 hover:text-red-700">
                                <i class="fa-solid fa-right-from-bracket text-lg transition-all duration-300"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>


                @elseif ($user->role == 'admin desa')
                <li class="mb-4">
                    <a href="{{ route('admin.desa.profile.index') }}"
                        class="flex flex-col items-center p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition-all duration-300">
                        <div class="w-16 h-16 rounded-full overflow-hidden mb-2 border-2 border-[#2D336B]">
                            @if ($user->foto_pengguna)
                                <img src="{{ asset('storage/' . $user->foto_pengguna) }}" alt="Foto Pengguna"
                                    class="w-full h-full object-cover">
                            @else
                                <img src="https://flowbite.com/docs/images/people/profile-picture-5.jpg" alt="Foto Pengguna"
                                    class="w-full h-full object-cover">
                            @endif
                        </div>
                        <span class="text-[#2D336B] font-semibold">{{ $user->username }}</span>
                        <span class="text-xs text-gray-500">{{ ucfirst($user->role) }}</span>
                    </a>
                </li>

                <!-- Dashboard Menu -->
                <li class="-ml-5">
                    <a href="{{ route('admin.desa.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                        {{ request()->routeIs('admin.desa.index') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                        <i class="fa-solid fa-gauge-high text-lg transition-all duration-300"></i>
                        <span>Dashboard</span>
                    </a>
                </li>


                    <!-- Master Penduduk Dropdown -->
                    <li class="-ml-5">
                        <button type="button"
                            class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] "
                            data-dropdown="pendudukDropdown">
                            <i class="fa-solid fa-id-card text-lg transition-all duration-300"></i>
                            <span>Master Penduduk</span>
                            <i id="dropdown-icon-penduduk"
                                class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="pendudukDropdown" class="hidden space-y-2 pl-6 transition-all duration-300 ease-in-out overflow-hidden">
                            <li>
                                <a href="{{ route('admin.desa.biodata.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 {{ request()->routeIs('admin.desa.biodata.index') || request()->routeIs('admin.desa.biodata.edit') || request()->routeIs('admin.desa.biodata.update') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Biodata</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.datakk.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 {{ request()->routeIs('admin.desa.datakk.index') || request()->routeIs('admin.desa.datakk.edit') || request()->routeIs('admin.desa.datakk.update') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Data KK</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.datakk.create') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 {{ request()->routeIs('admin.desa.datakk.create') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Tambah Data KK & Biodata</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.biodata-approval.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 {{ request()->routeIs('admin.desa.biodata-approval.*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Approval Biodata</span>
                                </a>
                            </li>
                        </ul>
                    </li>



                    <!-- Informasi Desa Dropdown -->
                    <li class="-ml-5">
                        <button type="button"
                            class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] "
                            data-dropdown="informasiDesaDropdown">
                            <i class="fa-solid fa-circle-info text-lg transition-all duration-300"></i>
                            <span>Informasi Desa</span>
                            <i id="dropdown-icon-informasi-desa" class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="informasiDesaDropdown" class="hidden space-y-2 pl-6 transition-all duration-300 ease-in-out overflow-hidden">
                            <li>
                                <a href="{{ route('admin.desa.berita-desa.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                    {{ request()->routeIs('admin.desa.berita-desa.*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Berita Desa</span>
                                </a>
                            </li>
                            <li>
                                <button type="button"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] "
                                    data-dropdown="informasiLaporDesaDropdown">
                                    <span>Lapor Desa</span>
                                    <i id="dropdown-icon-informasi-lapor" class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                                </button>
                                <ul id="informasiLaporDesaDropdown" class="hidden space-y-2 pl-8 transition-all duration-300 ease-in-out overflow-hidden submenu-level-2">
                                    <li>
                                        <a href="{{ route('admin.desa.datamaster.lapordesa.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                            {{ request()->routeIs('admin.desa.datamaster.lapordesa.*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                            <span>Master Lapor Desa</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.desa.laporan-desa.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                            {{ request()->routeIs('admin.desa.laporan-desa.*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                            <span>Daftar Laporan Desa</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.agenda.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 {{ request()->routeIs('admin.desa.agenda.*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Agenda Desa</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.pengumuman.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 {{ request()->routeIs('admin.desa.pengumuman.*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Pengumuman</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- Master Surat  -->
                    <li class="-ml-5">
                        <button type="button"
                            class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] "
                            data-dropdown="suratDropdown">
                            <i class="fa-solid fa-envelope text-lg transition-all duration-300"></i>
                            <span>Surat</span>
                            <i id="dropdown-icon-surat"
                                class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="suratDropdown" class="hidden space-y-2 pl-4 transition-all duration-300 ease-in-out overflow-hidden">
                            <li>
                                <a href="{{ route('admin.desa.surat.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 {{ request()->routeIs('admin.desa.surat.index') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Semua Surat</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.surat.administrasi.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 {{ request()->routeIs('admin.desa.surat.administrasi*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Surat Administrasi</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.surat.kehilangan.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                    {{ request()->routeIs('admin.desa.surat.kehilangan*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Surat Kehilangan</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.surat.skck.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                    {{ request()->routeIs('admin.desa.surat.skck*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Surat SKCK</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.surat.domisili.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                    {{ request()->routeIs('admin.desa.surat.domisili.index') || request()->routeIs('admin.desa.surat.domisili.create') || request()->routeIs('admin.desa.surat.domisili.edit') || request()->routeIs('admin.desa.surat.domisili.detail') || request()->routeIs('admin.desa.surat.domisili.delete') || request()->routeIs('admin.desa.surat.domisili.pdf') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Surat Domisili</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.surat.domisili-usaha.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                    {{ request()->routeIs('admin.desa.surat.domisili-usaha.index') || request()->routeIs('admin.desa.surat.domisili-usaha.create') || request()->routeIs('admin.desa.surat.domisili-usaha.edit') || request()->routeIs('admin.desa.surat.domisili-usaha.detail') || request()->routeIs('admin.desa.surat.domisili-usaha.delete') || request()->routeIs('admin.desa.surat.domisili-usaha.pdf') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Surat Domisili Usaha</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.surat.ahli-waris.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 {{ request()->routeIs('admin.desa.surat.ahli-waris*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Surat Ahli Waris</span>
                                </a>
                            </li>
                            {{-- <li>
                                <a href="{{ route('admin.desa.surat.kelahiran.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 {{ request()->routeIs('admin.desa.surat.kelahiran*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Surat Kelahiran</span>
                                </a>
                            </li> --}}
                            {{-- <li>
                                <a href="{{ route('admin.desa.surat.kematian.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 {{ request()->routeIs('admin.desa.surat.kematian*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Surat Kematian</span>
                                </a>
                            </li> --}}
                            <li>
                                <a href="{{ route('admin.desa.surat.keramaian.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 {{ request()->routeIs('admin.desa.surat.keramaian*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Izin Keramaian</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.surat.rumah-sewa.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 {{ request()->routeIs('admin.desa.surat.rumah-sewa*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Surat Rumah Sewa</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.buku-tamu.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 {{ request()->routeIs('admin.desa.buku-tamu*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Buku Tamu</span>
                                </a>
                            </li>
                            {{-- <li>
                                <a href="{{ route('admin.desa.surat.pengantar-ktp.index') }}"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 {{ request()->routeIs('admin.desa.surat.pengantar-ktp*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Pengantar KTP</span>
                                </a>
                            </li> --}}
                        </ul>
                    </li>

                    <!-- Profil Desa Dropdown -->
                    <li class="-ml-5">
                        <button type="button"
                            class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] "
                            data-dropdown="profilDesaDropdown">
                            <i class="fa-solid fa-home text-lg transition-all duration-300"></i>
                            <span>Profil Desa</span>
                            <i id="dropdown-icon-profil-desa" class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="profilDesaDropdown" class="hidden space-y-2 pl-6 transition-all duration-300 ease-in-out overflow-hidden">
                            <li>
                                <a href="{{ route('admin.desa.usaha.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                    {{ request()->routeIs('admin.desa.usaha.*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Usaha Desa</span>
                                </a>
                            </li>
                            <li>
                                <button type="button"
                                    class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] "
                                    data-dropdown="profilSaranaDropdown">
                                    <span>Master Sarana Umum</span>
                                    <i id="dropdown-icon-profil-sarana" class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                                </button>
                                <ul id="profilSaranaDropdown" class="hidden space-y-2 pl-6 transition-all duration-300 ease-in-out overflow-hidden">
                                    <li>
                                        <a href="{{ route('admin.desa.kategori-sarana.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                            {{ request()->routeIs('admin.desa.kategori-sarana.*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                            <span>Kategori Sarana</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.desa.sarana-umum.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                            {{ request()->routeIs('admin.desa.sarana-umum.*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                            <span>Sarana Umum</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.warungku.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                    {{ request()->routeIs('admin.desa.warungku.*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Warungku</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.kesenian-budaya.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                    {{ request()->routeIs('admin.desa.kesenian-budaya.*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Kesenian & Budaya</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.abdes.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                    {{ request()->routeIs('admin.desa.abdes.*') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>APBDES</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Master Tagihan (dropdown) -->
                    <li class="-ml-5">
                        <button type="button"
                            class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] "
                            data-dropdown="masterTagihanDropdown">
                            <i class="fa-solid fa-receipt text-lg transition-all duration-300"></i>
                            <span>Master Tagihan</span>
                            <i id="dropdown-icon-master-tagihan" class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="masterTagihanDropdown" class="hidden space-y-2 pl-6 transition-all duration-300 ease-in-out overflow-hidden">
                            <li>
                                <a href="{{ route('admin.desa.master-tagihan.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                            {{ request()->routeIs('admin.desa.master-tagihan.index')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B] ' }}">
                                    <span>Kategori & Sub Kategori</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.master-tagihan.tagihan.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                            {{ request()->routeIs('admin.desa.master-tagihan.tagihan.index')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B] ' }}">
                                    <span>Tagihan</span>
                                </a>
                            </li>
                        </ul>
                    </li>



                    <!-- Pengguna Mobile -->
                    <li class="-ml-5">
                        <a href="{{ route('admin.desa.pengguna-mobile.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('admin.desa.pengguna-mobile.*')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B] ' }}">
                            <i class="fa-solid fa-mobile-screen-button text-lg transition-all duration-300"></i>
                            <span>Pengguna Mobile</span>
                        </a>
                    </li>


                    <!-- Add Logout Button for admin.desa -->
                    <li class="-ml-5 mt-8">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-red-100 hover:text-red-700">
                                <i class="fa-solid fa-right-from-bracket text-lg transition-all duration-300"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                @elseif ($user->role == 'admin kabupaten')
                    <!-- Add Profile Section for Admin Kabupaten -->
                    <li class="mb-4">
                        <a href="{{ route('admin.kabupaten.profile.index') }}"
                            class="flex flex-col items-center p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition-all duration-300">
                            <div class="w-16 h-16 rounded-full overflow-hidden mb-2 border-2 border-[#2D336B]">
                                @if ($user->foto_pengguna)
                                    <img src="{{ asset('storage/' . $user->foto_pengguna) }}" alt="Foto Pengguna"
                                        class="w-full h-full object-cover">
                                @else
                                    <img src="https://flowbite.com/docs/images/people/profile-picture-5.jpg" alt="Foto Pengguna"
                                        class="w-full h-full object-cover">
                                @endif
                            </div>
                            <span class="text-[#2D336B] font-semibold">{{ $user->username }}</span>
                            <span class="text-xs text-gray-500">{{ ucfirst($user->role) }}</span>
                        </a>
                    </li>

                    <li class="-ml-5">
                        <a href="{{ route('admin.kabupaten.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                {{ request()->routeIs('admin.kabupaten.index')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B] ' }}">
                            <i class="fa-solid fa-gauge-high text-lg transition-all duration-300"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <!-- Add Logout Button -->
                    <li class="-ml-5 mt-auto">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-red-100 hover:text-red-700">
                                <i class="fa-solid fa-right-from-bracket text-lg transition-all duration-300"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                @elseif ($user->role == 'operator')
                    <li class="-ml-5">
                        <a href="{{ route('operator.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                {{ request()->routeIs('operator.index')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B] ' }}">
                            <i class="fa-solid fa-gauge-high text-lg transition-all duration-300"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                @elseif ($user->role == 'user')
                    <li class="-ml-5">
                        <a href="/user/index" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                    {{ request()->is('user/index')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B] ' }}">
                            <i class="fa-regular fa-clipboard text-lg transition-all duration-300"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li class="-ml-5">
                        <a href="{{ route('user.profile.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                {{ request()->routeIs('user.profile*')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B] ' }}">
                            <i class="fa-solid fa-user text-lg transition-all duration-300"></i>
                            <span>Profile</span>
                        </a>
                    </li>



                    <li class="-ml-5">
                        <a href="{{ route('user.riwayat-surat.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                {{ request()->routeIs('user.riwayat-surat*')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B] ' }}">
                            <i class="fa-solid fa-history text-lg transition-all duration-300"></i>
                            <span>Riwayat Surat</span>
                        </a>
                    </li>

                    <li class="-ml-5">
                        <a href="{{ route('user.laporan-desa.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                {{ request()->routeIs('user.laporan-desa*')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B] ' }}">
                            <i class="fa-solid fa-bullhorn text-lg transition-all duration-300"></i>
                            <span>Lapor Desa</span>
                        </a>
                    </li>

                    <li class="-ml-5">
                        <button type="button"
                            class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] "
                            data-dropdown="userBeritaDropdown">
                            <i class="fa-solid fa-newspaper text-lg transition-all duration-300"></i>
                            <span>Berita Desa</span>
                            <i id="dropdown-icon-user-berita" class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="userBeritaDropdown" class="hidden space-y-2 pl-6 transition-all duration-300 ease-in-out overflow-hidden">
                            <li>
                                <a href="{{ route('user.berita-desa.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                        {{ request()->routeIs('user.berita-desa.index')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B] ' }}">
                                    <span>Daftar Berita</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('user.berita-desa.create') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                        {{ request()->routeIs('user.berita-desa.create')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B] ' }}">
                                    <span>Buat Berita Desa</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="-ml-5">
                        <a href="{{ route('user.pengumuman.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                {{ request()->routeIs('user.pengumuman*')
                    ? 'bg-[#2D336B] text-white '
                    : 'text-[#2D336B] ' }}">
                            <i class="fa-solid fa-bullhorn text-lg transition-all duration-300"></i>
                            <span>Pengumuman</span>
                        </a>
                    </li>

                    <!-- Warungku (User) - ditempatkan tepat di atas Logout -->
                    <li class="-ml-5">
                        <button type="button"
                            class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] "
                            data-dropdown="userWarungkuDropdown">
                            <i class="fa-solid fa-store text-lg transition-all duration-300"></i>
                            <span>Warungku</span>
                            <i id="dropdown-icon-user-warungku" class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="userWarungkuDropdown" class="hidden space-y-2 pl-6 transition-all duration-300 ease-in-out overflow-hidden">
                            <li>
                                <a href="{{ route('user.warungku.index') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                        {{ request()->routeIs('user.warungku.index') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Semua Produk</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('user.warungku.my') }}" class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300
                                                                                                                        {{ request()->routeIs('user.warungku.my') || request()->routeIs('user.warungku.create') || request()->routeIs('user.warungku.edit') ? 'bg-[#2D336B] text-white ' : 'text-[#2D336B] ' }}">
                                    <span>Produk Saya</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Add Logout Button for User (Penduduk) -->
                    <li class="-ml-5 mt-8">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center w-full p-3 pl-4 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-red-100 hover:text-red-700">
                                <i class="fa-solid fa-right-from-bracket text-lg transition-all duration-300"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                @endif
            </ul>
        </div>
    </aside>
@endif

<script>
    function toggleDropdown(id, event) {
        // Prevent default behavior and stop event propagation
        if (event) {
            event.preventDefault();
            event.stopImmediatePropagation();
        }

        const dropdown = document.getElementById(id);
        if (!dropdown) {
            console.warn('Dropdown not found:', id);
            return;
        }

        const isHidden = dropdown.classList.contains('hidden');

        // Handle specific dropdown icons based on their ID with enhanced visual feedback
        const iconMap = {
            'pendudukDropdown': 'dropdown-icon-penduduk',
            'wilayahDropdown': 'dropdown-icon-wilayah',
            'suratDropdown': 'dropdown-icon-surat',
            'masterSuratDropdown': 'dropdown-icon-master-surat',
            'masterUsersDropdown': 'dropdown-icon-master-users',
            'masterKeperluanDropdown': 'dropdown-icon-master-keperluan',
            'kelolaAsetDropdown': 'dropdown-icon-kelola-aset',
            'profilSaranaDropdown': 'dropdown-icon-profil-sarana',
            'informasiLaporDesaDropdown': 'dropdown-icon-informasi-lapor',
            'profilDesaDropdown': 'dropdown-icon-profil-desa',
            'informasiDesaDropdown': 'dropdown-icon-informasi-desa',
            'masterTagihanDropdown': 'dropdown-icon-master-tagihan',
            'userBeritaDropdown': 'dropdown-icon-user-berita',
            'userWarungkuDropdown': 'dropdown-icon-user-warungku'
        };

        // Enhanced visual feedback for parent menu items
        const parentButton = event ? event.target.closest('button[data-dropdown]') : null;
        if (parentButton) {
            if (isHidden) {
                parentButton.style.background = 'linear-gradient(135deg, rgba(45, 51, 107, 0.08) 0%, rgba(74, 86, 166, 0.05) 100%)';
                parentButton.style.boxShadow = 'inset 0 2px 4px rgba(45, 51, 107, 0.1)';
            } else {
                parentButton.style.background = '';
                parentButton.style.boxShadow = '';
            }
        }

        const iconId = iconMap[id];
        if (iconId) {
            const icon = document.getElementById(iconId);
            if (icon) {
                // Add subtle bounce effect to icon rotation
                icon.style.transform = isHidden ?
                    'rotate(180deg) scale(1.1)' :
                    'rotate(0deg) scale(1)';
            }
        }

        // Clear any existing timeouts to prevent conflicts
        if (dropdown._hideTimeout) {
            clearTimeout(dropdown._hideTimeout);
            dropdown._hideTimeout = null;
        }

        // Clear any existing animation timeouts
        if (dropdown._animationTimeout) {
            clearTimeout(dropdown._animationTimeout);
            dropdown._animationTimeout = null;
        }

        if (isHidden) {
            // Show dropdown with enhanced protrusion animation
            dropdown.classList.remove('hidden');

            // Add visual feedback to parent button
            if (parentButton) {
                parentButton.classList.add('submenu-active');
            }

            // Force reflow to ensure animation starts properly
            dropdown.offsetHeight;

            // Add dropdown-open class after a tiny delay for smooth animation
            dropdown._animationTimeout = setTimeout(() => {
                dropdown.classList.add('dropdown-open');

                // Animate individual items with staggered delay and protrusion effect
                const items = dropdown.querySelectorAll('li');
                items.forEach((item, index) => {
                    item.style.setProperty('--item-index', index);
                    // Add slight delay for each item to create wave effect
                    item.style.animationDelay = `${index * 50}ms`;
                    // Force reflow for each item
                    item.offsetHeight;
                });
            }, 10);
        } else {
            // Hide dropdown with smooth animation
            dropdown.classList.remove('dropdown-open');

            // Remove visual feedback from parent button
            if (parentButton) {
                parentButton.classList.remove('submenu-active');
            }

            // Reset individual item animations
            const items = dropdown.querySelectorAll('li');
            items.forEach((item) => {
                item.style.setProperty('--item-index', '0');
                item.style.animationDelay = '';
            });

            // Use timeout to allow CSS transition to complete before hiding
            dropdown._hideTimeout = setTimeout(() => {
                dropdown.classList.add('hidden');
                dropdown._hideTimeout = null;
                dropdown._animationTimeout = null;
            }, 450); // Slightly longer than CSS transition for safety
        }
    }

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', () => {
        const toggleButton = document.querySelector('[data-drawer-toggle="logo-sidebar"]');
        const sidebar = document.getElementById('sidebar');

        if (toggleButton && sidebar) {
            // Toggle sidebar on mobile
            toggleButton.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.remove('-translate-x-full');
                } else {
                    sidebar.classList.add('-translate-x-full');
                }
            });
        }

        // Use event delegation for all dropdown toggles
        document.addEventListener('click', (e) => {
            // Only handle clicks on dropdown toggle buttons
            const button = e.target.closest('button[data-dropdown]');
            if (!button) return;

            // Get dropdown ID from data attribute
            const dropdownId = button.getAttribute('data-dropdown');
            if (dropdownId) {
                e.preventDefault();
                e.stopImmediatePropagation();
                toggleDropdown(dropdownId, e);
            }
        }, true); // Use capture phase to intercept before other listeners

        // Auto-open dropdowns if a child link is active with smooth animation
        const checkDropdownForActiveItems = (dropdownId, iconId) => {
            const dropdown = document.getElementById(dropdownId);
            const icon = document.getElementById(iconId);

            if (!dropdown) return;

            // Check if any child link has the active class
            const hasActiveChild = dropdown.querySelector('a.bg-\\[\\#2D336B\\]');

            if (hasActiveChild) {
                dropdown.classList.remove('hidden');

                // Add visual feedback to parent button
                const parentBtn = document.querySelector(`button[data-dropdown="${dropdownId}"]`);
                if (parentBtn) {
                    parentBtn.classList.add('submenu-active');
                }

                // Force reflow for smooth animation
                dropdown.offsetHeight;

                // Smoothly open the dropdown with protrusion effect
                setTimeout(() => {
                    dropdown.classList.add('dropdown-open');

                    // Animate individual items with protrusion
                    const items = dropdown.querySelectorAll('li');
                    items.forEach((item, index) => {
                        item.style.setProperty('--item-index', index);
                        item.style.animationDelay = `${index * 50}ms`;
                        // Force reflow for each item
                        item.offsetHeight;
                    });

                    // Rotate icon smoothly with enhanced effect
                    if (icon) {
                        icon.style.transform = 'rotate(180deg) scale(1.1)';
                        icon.style.filter = 'drop-shadow(0 2px 4px rgba(45, 51, 107, 0.3))';
                    }
                }, 100); // Slightly longer delay for page load effect
            }
        };

        // Check each dropdown for auto-open
        const dropdownsToCheck = [
            ['pendudukDropdown', 'dropdown-icon-penduduk'],
            ['suratDropdown', 'dropdown-icon-surat'],
            ['wilayahDropdown', 'dropdown-icon-wilayah'],
            ['masterSuratDropdown', 'dropdown-icon-master-surat'],
            ['masterUsersDropdown', 'dropdown-icon-master-users'],
            ['kelolaAsetDropdown', 'dropdown-icon-kelola-aset'],
            ['masterKeperluanDropdown', 'dropdown-icon-master-keperluan'],
            ['profilSaranaDropdown', 'dropdown-icon-profil-sarana'],
            ['informasiLaporDesaDropdown', 'dropdown-icon-informasi-lapor'],
            ['profilDesaDropdown', 'dropdown-icon-profil-desa'],
            ['informasiDesaDropdown', 'dropdown-icon-informasi-desa'],
            ['masterTagihanDropdown', 'dropdown-icon-master-tagihan'],
            ['userBeritaDropdown', 'dropdown-icon-user-berita'],
            ['userWarungkuDropdown', 'dropdown-icon-user-warungku']
        ];

        // Delay auto-open slightly to ensure DOM is fully ready
        setTimeout(() => {
            dropdownsToCheck.forEach(([dropdownId, iconId]) => {
                checkDropdownForActiveItems(dropdownId, iconId);
            });
        }, 100);
    });
</script>

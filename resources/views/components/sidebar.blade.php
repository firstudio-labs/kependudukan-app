<nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start">
                <button
                    data-drawer-target="logo-sidebar"
                    data-drawer-toggle="logo-sidebar"
                    aria-controls="logo-sidebar"
                    type="button"
                    class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
                >
                    <span class="sr-only">Open sidebar</span>
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                    </svg>
                </button>
                <a href="/" class="flex ms-2 md:me-24">
                    <span class="self-center text-sm font-semibold sm:text-base whitespace-nowrap text-transparent bg-clip-text bg-gradient-to-r from-[#2D336B] to-[#4A56A6]">
                        Kependudukan
                    </span>
                </a>
            </div>
            <div class="flex items-center">
                <div class="flex items-center ms-3">
                    <button
                        type="button"
                        class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300"
                        aria-expanded="false"
                        data-dropdown-toggle="dropdown-user"
                    >
                        <span class="sr-only">Open user menu</span>
                        <img class="w-8 h-8 rounded-full" src="https://flowbite.com/docs/images/people/profile-picture-5.jpg" alt="user photo">
                    </button>
                </div>
            </div>
        </div>
    </div>
</nav>
@php
    $user = Auth::user();
@endphp

<aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0" aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white">
        <ul class="space-y-2 font-medium text-sm">
            {{-- Menu umum yang bisa diakses oleh semua role --}}
            <li>
                {{-- <a href="{{ route('dashboard') }}" class="flex items-center p-2 text-[#2D336B] rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5 text-[#2D336B]" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                        <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/>
                        <path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/>
                    </svg>
                    <span class="ms-3">Dashboard</span>
                </a> --}}
            </li>

            {{-- Menu berdasarkan role --}}
            @if ($user->role == 'superadmin')
            <li class="-ml-5">
                <a href="{{ route('superadmin.index') }}"
                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                        {{ request()->routeIs('superadmin.index')
                            ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                            : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                    <i class="fa-solid fa-gauge-high text-lg transition-all duration-300"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Replace single Master User with Master Users dropdown -->
            <li class="-ml-5">
                <button type="button"
                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white"
                    onclick="toggleDropdown('masterUsersDropdown')">
                    <i class="fa-solid fa-users text-lg transition-all duration-300"></i>
                    <span>Master Users</span>
                    <i id="dropdown-icon-master-users" class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                </button>
                <ul id="masterUsersDropdown" class="hidden space-y-2 pl-6">
                    <!-- Users submenu -->
                    <li>
                        <a href="{{ route('superadmin.datamaster.user.index') }}"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                {{ request()->routeIs('superadmin.datamaster.user*')
                                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <span>Users</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Keep Master Penduduk as a separate dropdown -->
            <li class="-ml-5">
                <button type="button"
                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white"
                    onclick="toggleDropdown('pendudukDropdown')">
                    <i class="fa-solid fa-id-card text-lg transition-all duration-300"></i>
                    <span>Master Penduduk</span>
                    <i id="dropdown-icon-penduduk" class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                </button>
                <ul id="pendudukDropdown" class="hidden space-y-2 pl-6">
                    <li>
                        <a href="{{ route('superadmin.biodata.index') }}"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                {{ request()->routeIs('superadmin.biodata*')
                                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <span>Biodata</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('superadmin.datakk.index') }}"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                {{ request()->routeIs('superadmin.datakk*')
                                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <span>Data KK</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Master Surat (moved out but keeping its dropdown) -->
            <li class="-ml-5">
                <button type="button"
                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white"
                    onclick="toggleDropdown('suratDropdown')">
                    <i class="fa-solid fa-envelope text-lg transition-all duration-300"></i>
                    <span>Surat</span>
                    <i id="dropdown-icon-surat" class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                </button>
                <ul id="suratDropdown" class="hidden space-y-2 pl-6">
                    <li>
                        <a href="{{ route('superadmin.surat.administrasi.index') }}"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                {{ request()->routeIs('superadmin.surat.administrasi*')
                                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <span>Administrasi Umum</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('superadmin.surat.kehilangan.index') }}"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                {{ request()->routeIs('superadmin.surat.kehilangan*')
                                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <span>Surat Kehilangan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('superadmin.surat.skck.index') }}"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                {{ request()->routeIs('superadmin.surat.skck*')
                                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <span>Surat SKCK</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('superadmin.surat.domisili.index') }}"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                {{ request()->routeIs('superadmin.surat.domisili*') && !request()->routeIs('superadmin.surat.domisili-usaha*')
                                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <span>Keterangan Domisili</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('superadmin.surat.domisili-usaha.index') }}"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                {{ request()->routeIs('superadmin.surat.domisili-usaha*')
                                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <span>Domisili Usaha</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('superadmin.surat.ahli-waris.index') }}"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                {{ request()->routeIs('superadmin.surat.ahli-waris*')
                                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <span>Surat Ahli Waris</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('superadmin.surat.kelahiran.index') }}"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                {{ request()->routeIs('superadmin.surat.kelahiran*')
                                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <span>Keterangan Kelahiran</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('superadmin.surat.kematian.index') }}"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                {{ request()->routeIs('superadmin.surat.kematian*')
                                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <span>Keterangan Kematian</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('superadmin.surat.keramaian.index') }}"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                {{ request()->routeIs('superadmin.surat.keramaian*')
                                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <span>Izin Keramaian</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('superadmin.surat.rumah-sewa.index') }}"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                {{ request()->routeIs('superadmin.surat.rumah-sewa*')
                                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <span>Izin Rumah Sewa</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('superadmin.surat.pengantar-ktp.index') }}"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                {{ request()->routeIs('superadmin.surat.pengantar-ktp*')
                                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <span>Pengantar KTP</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- New Master Surat dropdown -->
            <li class="-ml-5">
                <button type="button"
                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white"
                    onclick="toggleDropdown('masterSuratDropdown')">
                    <i class="fa-solid fa-file-signature text-lg transition-all duration-300"></i>
                    <span>Master Surat</span>
                    <i id="dropdown-icon-master-surat" class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                </button>
                <ul id="masterSuratDropdown" class="hidden space-y-2 pl-6">
                    <li>
                        <a href="{{ route('superadmin.datamaster.surat.penandatangan.index') }}"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                {{ request()->routeIs('superadmin.datamaster.surat.penandatangan*')
                                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <span>Penandatangan</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Master Wilayah (moved out but keeping its dropdown) -->
            <li class="-ml-5">
                <button type="button"
                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white"
                    onclick="toggleDropdown('wilayahDropdown')">
                    <i class="fa-solid fa-map text-lg transition-all duration-300"></i>
                    <span>Master Wilayah</span>
                    <i id="dropdown-icon-wilayah" class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                </button>
                <ul id="wilayahDropdown" class="hidden space-y-2 pl-6">
                    <li>
                        <a href="{{ route('superadmin.datamaster.wilayah.provinsi.index') }}"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                {{ request()->routeIs('superadmin.datamaster.wilayah.provinsi*')
                                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <span>Provinsi</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('superadmin.datamaster.wilayah.kabupaten.index') }}"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                {{ request()->routeIs('superadmin.datamaster.wilayah.kabupaten*')
                                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <span>Kabupaten</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('superadmin.datamaster.wilayah.kecamatan.index') }}"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                {{ request()->routeIs('superadmin.datamaster.wilayah.kecamatan*')
                                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <span>Kecamatan</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('superadmin.datamaster.wilayah.desa.index') }}"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                {{ request()->routeIs('superadmin.datamaster.wilayah.desa*')
                                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <span>Desa</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- Add logout button below the Master menu -->
            <li class="-ml-5 mt-6">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-red-500 hover:text-white">
                        <i class="fa-solid fa-sign-out-alt text-lg transition-all duration-300"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </li>
            @elseif ($user->role == 'admin')
                <li>
                    {{-- <a href="{{ route('admin.reports') }}" class="flex items-center p-2 text-[#2D336B] rounded-lg hover:bg-gray-100">
                        <i class="fa-solid fa-chart-line text-[#2D336B]"></i>
                        <span class="ms-3">Laporan</span>
                    </a> --}}
                </li>
            @elseif ($user->role == 'operator')
                <li>
                    {{-- <a href="{{ route('operator.tasks') }}" class="flex items-center p-2 text-[#2D336B] rounded-lg hover:bg-gray-100">
                        <i class="fa-solid fa-tasks text-[#2D336B]"></i>
                        <span class="ms-3">Tugas</span>
                    </a> --}}
                </li>
            @elseif ($user->role == 'user')
            <li class="-ml-5">
                <a href="/user/index"
                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                        {{ request()->is('user/index')
                            ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                            : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                    <i class="fa-regular fa-clipboard text-lg transition-all duration-300"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <!-- Add logout button for user role too -->
            <li class="-ml-5 mt-6">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-red-500 hover:text-white">
                        <i class="fa-solid fa-sign-out-alt text-lg transition-all duration-300"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </li>
            @endif
        </ul>
    </div>
</aside>

<script>
    function toggleDropdown(id) {
        const dropdown = document.getElementById(id);

        // Handle specific dropdown icons based on their ID
        if (id === 'pendudukDropdown') {
            const icon = document.getElementById('dropdown-icon-penduduk');
            icon.classList.toggle('rotate-180');
        } else if (id === 'wilayahDropdown') {
            const icon = document.getElementById('dropdown-icon-wilayah');
            icon.classList.toggle('rotate-180');
        } else if (id === 'suratDropdown') {
            const icon = document.getElementById('dropdown-icon-surat');
            icon.classList.toggle('rotate-180');
        } else if (id === 'masterSuratDropdown') {
            const icon = document.getElementById('dropdown-icon-master-surat');
            icon.classList.toggle('rotate-180');
        } else if (id === 'masterUsersDropdown') {
            const icon = document.getElementById('dropdown-icon-master-users');
            icon.classList.toggle('rotate-180');
        }

        dropdown.classList.toggle('hidden');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const toggleButton = document.querySelector('[data-drawer-toggle="logo-sidebar"]');
        const sidebar = document.getElementById('sidebar');

        // Tambahkan event listener ke tombol
        toggleButton.addEventListener('click', () => {
            if (sidebar.classList.contains('-translate-x-full')) {
                // Tampilkan sidebar
                sidebar.classList.remove('-translate-x-full');
            } else {
                // Sembunyikan sidebar
                sidebar.classList.add('-translate-x-full');
            }
        });

        // Auto-open dropdowns if a child link is active
        const checkDropdownForActiveItems = (dropdownId, iconId) => {
            const dropdown = document.getElementById(dropdownId);
            const icon = document.getElementById(iconId);

            if (!dropdown) return;

            // Check if any child link has the active class (bg-[#2D336B])
            const hasActiveChild = dropdown.querySelector('a.bg-\\[\\#2D336B\\]');

            if (hasActiveChild) {
                // Open the dropdown
                dropdown.classList.remove('hidden');
                // Rotate the icon
                if (icon) icon.classList.add('rotate-180');
            }
        };

        // Check each dropdown
        checkDropdownForActiveItems('pendudukDropdown', 'dropdown-icon-penduduk');
        checkDropdownForActiveItems('suratDropdown', 'dropdown-icon-surat');
        checkDropdownForActiveItems('wilayahDropdown', 'dropdown-icon-wilayah');
        checkDropdownForActiveItems('masterSuratDropdown', 'dropdown-icon-master-surat');
        checkDropdownForActiveItems('masterUsersDropdown', 'dropdown-icon-master-users');
    });
</script>

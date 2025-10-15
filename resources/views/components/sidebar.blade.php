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
        <div class="h-full px-3 pb-4 overflow-y-auto bg-white">
            <ul class="space-y-2 font-medium text-sm">
                {{-- Menu berdasarkan role --}}
                @if ($user->role == 'superadmin')
                    <li class="-ml-5">
                        <a href="{{ route('superadmin.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
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
                            <i id="dropdown-icon-master-users"
                                class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="masterUsersDropdown" class="hidden space-y-2 pl-6">
                            <!-- Users submenu -->
                            <li>
                                <a href="{{ route('superadmin.datamaster.user.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
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
                            <i id="dropdown-icon-penduduk"
                                class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="pendudukDropdown" class="hidden space-y-2 pl-6">
                            <li>
                                <a href="{{ route('superadmin.biodata.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                        {{ request()->routeIs('superadmin.biodata.index') || request()->routeIs('superadmin.biodata.create') || request()->routeIs('superadmin.biodata.edit') || request()->routeIs('superadmin.biodata.update') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Biodata</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('superadmin.datakk.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                        {{ request()->routeIs('superadmin.datakk.index') || request()->routeIs('superadmin.datakk.edit') || request()->routeIs('superadmin.datakk.update') || request()->routeIs('superadmin.datakk.detail') || request()->routeIs('superadmin.datakk.delete') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Data KK</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Tambah Data KK (menu terpisah) -->
                    <li class="-ml-5">
                        <a href="{{ route('superadmin.datakk.create') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                {{ request()->routeIs('superadmin.datakk.create') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <i class="fa-solid fa-plus text-lg transition-all duration-300"></i>
                            <span>Tambah Data KK</span>
                        </a>
                    </li>

                    <!-- Kelola Aset dropdown -->
                    <li class="-ml-5">
                        <button type="button"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white"
                            onclick="toggleDropdown('kelolaAsetDropdown')">
                            <i class="fa-solid fa-boxes-stacked text-lg transition-all duration-300"></i>
                            <span>Kelola Aset</span>
                            <i id="dropdown-icon-kelola-aset"
                                class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="kelolaAsetDropdown" class="hidden space-y-2 pl-6">
                            <li>
                                <a href="{{ route('superadmin.datamaster.klasifikasi.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                                            {{ request()->routeIs('superadmin.datamaster.klasifikasi*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Klasifikasi</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('superadmin.datamaster.jenis-aset.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                                            {{ request()->routeIs('superadmin.datamaster.jenis-aset*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Jenis Aset</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    

                    <!-- In your sidebar component or layout -->
                    <li class="-ml-5">
                        <a href="{{ route('superadmin.datamaster.lapordesa.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                {{ request()->routeIs('superadmin.datamaster.lapordesa.*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <i class="fa-solid fa-bullhorn text-lg transition-all duration-300"></i>
                            <span>Master Lapor Desa</span>
                        </a>
                    </li>

                    <!-- Master Surat (moved out but keeping its dropdown) -->
                    <li class="-ml-5">
                        <button type="button"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white"
                            onclick="toggleDropdown('suratDropdown')">
                            <i class="fa-solid fa-envelope text-lg transition-all duration-300"></i>
                            <span>Surat</span>
                            <i id="dropdown-icon-surat"
                                class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="suratDropdown" class="hidden space-y-2 pl-6">
                            <li>
                                <a href="{{ route('superadmin.surat.administrasi.index') }}"
                                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('superadmin.surat.administrasi*') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Surat Administrasi</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('superadmin.surat.kehilangan.index') }}"
                                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('superadmin.surat.kehilangan*') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Surat Kehilangan</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('superadmin.surat.skck.index') }}"
                                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('superadmin.surat.skck*') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Surat SKCK</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('superadmin.surat.domisili.index') }}"
                                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('superadmin.surat.domisili.index') || request()->routeIs('superadmin.surat.domisili.create') || request()->routeIs('superadmin.surat.domisili.edit') || request()->routeIs('superadmin.surat.domisili.detail') || request()->routeIs('superadmin.surat.domisili.delete') || request()->routeIs('superadmin.surat.domisili.pdf') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Surat Domisili</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('superadmin.surat.domisili-usaha.index') }}"
                                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('superadmin.surat.domisili-usaha.index') || request()->routeIs('superadmin.surat.domisili-usaha.create') || request()->routeIs('superadmin.surat.domisili-usaha.edit') || request()->routeIs('superadmin.surat.domisili-usaha.detail') || request()->routeIs('superadmin.surat.domisili-usaha.delete') || request()->routeIs('superadmin.surat.domisili-usaha.pdf') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Surat Domisili Usaha</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('superadmin.surat.ahli-waris.index') }}"
                                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('superadmin.surat.ahli-waris*') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Surat Ahli Waris</span>
                                </a>
                            </li>
                            {{-- <li>
                                <a href="{{ route('superadmin.surat.kelahiran.index') }}"
                                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('superadmin.surat.kelahiran*') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Surat Kelahiran</span>
                                </a>
                            </li> --}}
                            {{-- <li>
                                <a href="{{ route('superadmin.surat.kematian.index') }}"
                                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('superadmin.surat.kematian*') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Surat Kematian</span>
                                </a>
                            </li> --}}
                            <li>
                                <a href="{{ route('superadmin.surat.keramaian.index') }}"
                                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('superadmin.surat.keramaian*') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Izin Keramaian</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('superadmin.surat.rumah-sewa.index') }}"
                                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('superadmin.surat.rumah-sewa*') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Surat Rumah Sewa</span>
                                </a>
                            </li>
                            {{-- <li>
                                <a href="{{ route('superadmin.surat.pengantar-ktp.index') }}"
                                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('superadmin.surat.pengantar-ktp*') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Pengantar KTP</span>
                                </a>
                            </li> --}}
                        </ul>
                    </li>

                    <!-- New Master Surat dropdown -->
                    <li class="-ml-5">
                        <button type="button"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white"
                            onclick="toggleDropdown('masterSuratDropdown')">
                            <i class="fa-solid fa-file-signature text-lg transition-all duration-300"></i>
                            <span>Master Surat</span>
                            <i id="dropdown-icon-master-surat"
                                class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="masterSuratDropdown" class="hidden space-y-2 pl-6">
                            <li>
                                <a href="{{ route('superadmin.datamaster.surat.penandatangan.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                        {{ request()->routeIs('superadmin.datamaster.surat.penandatangan*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Penandatangan</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Master Keperluan dropdown -->
                    <li class="-ml-5">
                        <button type="button"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white"
                            onclick="toggleDropdown('masterKeperluanDropdown')">
                            <i class="fa-solid fa-clipboard-list text-lg transition-all duration-300"></i>
                            <span>Master Keperluan</span>
                            <i id="dropdown-icon-master-keperluan"
                                class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="masterKeperluanDropdown" class="hidden space-y-2 pl-6">
                            <li>
                                <a href="{{ route('superadmin.datamaster.masterkeperluan.keperluan.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                        {{ request()->routeIs('superadmin.datamaster.masterkeperluan.keperluan*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Keperluan</span>
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
                            <i id="dropdown-icon-wilayah"
                                class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="wilayahDropdown" class="hidden space-y-2 pl-6">
                            <li>
                                <a href="{{ route('superadmin.datamaster.wilayah.provinsi.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                        {{ request()->routeIs('superadmin.datamaster.wilayah.provinsi*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Provinsi</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('superadmin.datamaster.wilayah.kabupaten.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                        {{ request()->routeIs('superadmin.datamaster.wilayah.kabupaten*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Kabupaten</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('superadmin.datamaster.wilayah.kecamatan.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                        {{ request()->routeIs('superadmin.datamaster.wilayah.kecamatan*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Kecamatan</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('superadmin.datamaster.wilayah.desa.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                        {{ request()->routeIs('superadmin.datamaster.wilayah.desa*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Desa</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                     <!-- Master Warungku (Superadmin) -->
                    <li class="-ml-5">
                        <a href="{{ route('superadmin.datamaster.warungku.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                {{ request()->routeIs('superadmin.datamaster.warungku.*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <i class="fa-solid fa-store text-lg transition-all duration-300"></i>
                            <span>Master Warungku</span>
                        </a>
                    </li>

                    <!-- Add Logout Button for Superadmin -->
                    <li class="-ml-5 mt-8">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-red-100 hover:text-red-700">
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
                    <a href="{{ route('admin.desa.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                        {{ request()->routeIs('admin.desa.index') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                        <i class="fa-solid fa-gauge-high text-lg transition-all duration-300"></i>
                        <span>Dashboard</span>
                    </a>
                </li>


                    <!-- Master Penduduk Dropdown -->
                    <li class="-ml-5">
                        <button type="button"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white"
                            onclick="toggleDropdown('pendudukDropdown')">
                            <i class="fa-solid fa-id-card text-lg transition-all duration-300"></i>
                            <span>Master Penduduk</span>
                            <i id="dropdown-icon-penduduk"
                                class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="pendudukDropdown" class="hidden space-y-2 pl-6">
                            <li>
                                <a href="{{ route('admin.desa.biodata.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                            {{ request()->routeIs('admin.desa.biodata.index') || request()->routeIs('admin.desa.biodata.edit') || request()->routeIs('admin.desa.biodata.update') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Biodata</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.datakk.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                            {{ request()->routeIs('admin.desa.datakk.index') || request()->routeIs('admin.desa.datakk.edit') || request()->routeIs('admin.desa.datakk.update') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Data KK</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.biodata-approval.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                            {{ request()->routeIs('admin.desa.biodata-approval.*') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Approval Biodata</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Tambah Data KK (menu terpisah) -->
                    <li class="-ml-5">
                        <a href="{{ route('admin.desa.datakk.create') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                {{ request()->routeIs('admin.desa.datakk.create') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <i class="fa-solid fa-plus text-lg transition-all duration-300"></i>
                            <span>Tambah Data KK</span>
                        </a>
                    </li>

                    <!-- Master Lapor Desa -->
                    <li class="-ml-5">
                        <a href="{{ route('admin.desa.datamaster.lapordesa.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                        {{ request()->routeIs('admin.desa.datamaster.lapordesa.*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <i class="fa-solid fa-bullhorn text-lg transition-all duration-300"></i>
                            <span>Master Lapor Desa</span>
                        </a>
                    </li>
                    <!-- Master Surat  -->
                    <li class="-ml-5">
                        <button type="button"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white"
                            onclick="toggleDropdown('suratDropdown')">
                            <i class="fa-solid fa-envelope text-lg transition-all duration-300"></i>
                            <span>Surat</span>
                            <i id="dropdown-icon-surat"
                                class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="suratDropdown" class="hidden space-y-2 pl-6">
                            <li>
                                <a href="{{ route('admin.desa.surat.index') }}"
                                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                    {{ request()->routeIs('admin.desa.surat.index') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Semua Surat</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.surat.administrasi.index') }}"
                                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                    {{ request()->routeIs('admin.desa.surat.administrasi*') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Surat Administrasi</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.surat.kehilangan.index') }}"
                                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                    {{ request()->routeIs('admin.desa.surat.kehilangan*') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Surat Kehilangan</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.surat.skck.index') }}"
                                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                    {{ request()->routeIs('admin.desa.surat.skck*') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Surat SKCK</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.surat.domisili.index') }}"
                                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                    {{ request()->routeIs('admin.desa.surat.domisili.index') || request()->routeIs('admin.desa.surat.domisili.create') || request()->routeIs('admin.desa.surat.domisili.edit') || request()->routeIs('admin.desa.surat.domisili.detail') || request()->routeIs('admin.desa.surat.domisili.delete') || request()->routeIs('admin.desa.surat.domisili.pdf') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Surat Domisili</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.surat.domisili-usaha.index') }}"
                                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                    {{ request()->routeIs('admin.desa.surat.domisili-usaha.index') || request()->routeIs('admin.desa.surat.domisili-usaha.create') || request()->routeIs('admin.desa.surat.domisili-usaha.edit') || request()->routeIs('admin.desa.surat.domisili-usaha.detail') || request()->routeIs('admin.desa.surat.domisili-usaha.delete') || request()->routeIs('admin.desa.surat.domisili-usaha.pdf') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Surat Domisili Usaha</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.surat.ahli-waris.index') }}"
                                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                    {{ request()->routeIs('admin.desa.surat.ahli-waris*') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Surat Ahli Waris</span>
                                </a>
                            </li>
                            {{-- <li>
                                <a href="{{ route('admin.desa.surat.kelahiran.index') }}"
                                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                    {{ request()->routeIs('admin.desa.surat.kelahiran*') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Surat Kelahiran</span>
                                </a>
                            </li> --}}
                            {{-- <li>
                                <a href="{{ route('admin.desa.surat.kematian.index') }}"
                                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                    {{ request()->routeIs('admin.desa.surat.kematian*') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Surat Kematian</span>
                                </a>
                            </li> --}}
                            <li>
                                <a href="{{ route('admin.desa.surat.keramaian.index') }}"
                                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                    {{ request()->routeIs('admin.desa.surat.keramaian*') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Izin Keramaian</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.surat.rumah-sewa.index') }}"
                                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                    {{ request()->routeIs('admin.desa.surat.rumah-sewa*') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Surat Rumah Sewa</span>
                                </a>
                            </li>
                            {{-- <li>
                                <a href="{{ route('admin.desa.surat.pengantar-ktp.index') }}"
                                    class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                    {{ request()->routeIs('admin.desa.surat.pengantar-ktp*') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Pengantar KTP</span>
                                </a>
                            </li> --}}
                        </ul>
                    </li>

                    <!-- Laporan Desa -->
                    <li class="-ml-5">
                        <a href="{{ route('admin.desa.laporan-desa.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('admin.desa.laporan-desa.*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <i class="fa-solid fa-clipboard-list text-lg transition-all duration-300"></i>
                            <span>Laporan Desa</span>
                        </a>
                    </li>

                    <!-- Berita Desa -->
                    <li class="-ml-5">
                        <a href="{{ route('admin.desa.berita-desa.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                            {{ request()->routeIs('admin.desa.berita-desa.*') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <i class="fa-solid fa-newspaper text-lg transition-all duration-300"></i>
                            <span>Berita Desa</span>
                        </a>
                    </li>

                    <!-- Agenda Desa -->
                    <li class="-ml-5">
                        <a href="{{ route('admin.desa.agenda.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                        {{ request()->routeIs('admin.desa.agenda.*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <i class="fa-solid fa-calendar-days text-lg transition-all duration-300"></i>
                            <span>Agenda Desa</span>
                        </a>
                    </li>

                    <!-- Usaha Desa -->
                    <li class="-ml-5">
                        <a href="{{ route('admin.desa.usaha.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                        {{ request()->routeIs('admin.desa.usaha.*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <i class="fa-solid fa-store text-lg transition-all duration-300"></i>
                            <span>Usaha Desa</span>
                        </a>
                    </li>

                    <!-- Warungku: daftar informasi usaha seluruh penduduk desa -->
                    <li class="-ml-5">
                        <a href="{{ route('admin.desa.warungku.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                        {{ request()->routeIs('admin.desa.warungku.*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <i class="fa-solid fa-basket-shopping text-lg transition-all duration-300"></i>
                            <span>Warungku</span>
                        </a>
                    </li>

                    <!-- Sarana Umum -->
                    <li class="-ml-5">
                        <button type="button"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white"
                            onclick="toggleDropdown('saranaUmumDropdown')">
                            <i class="fa-solid fa-building text-lg transition-all duration-300"></i>
                            <span>Sarana Umum</span>
                            <i id="dropdown-icon-sarana-umum" class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="saranaUmumDropdown" class="hidden space-y-2 pl-6">
                            <li>
                                <a href="{{ route('admin.desa.kategori-sarana.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                    {{ request()->routeIs('admin.desa.kategori-sarana.*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Kategori Sarana</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.sarana-umum.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                    {{ request()->routeIs('admin.desa.sarana-umum.*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Sarana Umum</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Kesenian & Budaya -->
                    <li class="-ml-5">
                        <a href="{{ route('admin.desa.kesenian-budaya.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                        {{ request()->routeIs('admin.desa.kesenian-budaya.*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <i class="fa-solid fa-masks-theater text-lg transition-all duration-300"></i>
                            <span>Kesenian & Budaya</span>
                        </a>
                    </li>

                    <!-- APBDes -->
                    <li class="-ml-5">
                        <a href="{{ route('admin.desa.abdes.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                        {{ request()->routeIs('admin.desa.abdes.*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <i class="fa-solid fa-sack-dollar text-lg transition-all duration-300"></i>
                            <span>APBDES</span>
                        </a>
                    </li>

                    <!-- Master Tagihan (dropdown) -->
                    <li class="-ml-5">
                        <button type="button"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white"
                            onclick="toggleDropdown('masterTagihanDropdown')">
                            <i class="fa-solid fa-receipt text-lg transition-all duration-300"></i>
                            <span>Master Tagihan</span>
                            <i id="dropdown-icon-master-tagihan" class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="masterTagihanDropdown" class="hidden space-y-2 pl-6">
                            <li>
                                <a href="{{ route('admin.desa.master-tagihan.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                            {{ request()->routeIs('admin.desa.master-tagihan.index')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Kategori & Sub Kategori</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.desa.master-tagihan.tagihan.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                            {{ request()->routeIs('admin.desa.master-tagihan.tagihan.index')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Tagihan</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Pengumuman -->
                    <li class="-ml-5">
                        <a href="{{ route('admin.desa.pengumuman.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('admin.desa.pengumuman.*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <i class="fa-solid fa-bullhorn text-lg transition-all duration-300"></i>
                            <span>Pengumuman</span>
                        </a>
                    </li>

                    <!-- Pengguna Mobile -->
                    <li class="-ml-5">
                        <a href="{{ route('admin.desa.pengguna-mobile.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                    {{ request()->routeIs('admin.desa.pengguna-mobile.*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <i class="fa-solid fa-mobile-screen-button text-lg transition-all duration-300"></i>
                            <span>Pengguna Mobile</span>
                        </a>
                    </li>


                    <!-- Add Logout Button for admin.desa -->
                    <li class="-ml-5 mt-8">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-red-100 hover:text-red-700">
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
                        <a href="{{ route('admin.kabupaten.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                {{ request()->routeIs('admin.kabupaten.index')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <i class="fa-solid fa-gauge-high text-lg transition-all duration-300"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <!-- Add Logout Button -->
                    <li class="-ml-5 mt-auto">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-red-100 hover:text-red-700">
                                <i class="fa-solid fa-right-from-bracket text-lg transition-all duration-300"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                @elseif ($user->role == 'operator')
                    <li class="-ml-5">
                        <a href="{{ route('operator.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                {{ request()->routeIs('operator.index')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <i class="fa-solid fa-gauge-high text-lg transition-all duration-300"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                @elseif ($user->role == 'user')
                    <li class="-ml-5">
                        <a href="/user/index" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                                                                    {{ request()->is('user/index')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <i class="fa-regular fa-clipboard text-lg transition-all duration-300"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li class="-ml-5">
                        <a href="{{ route('user.profile.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                {{ request()->routeIs('user.profile*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <i class="fa-solid fa-user text-lg transition-all duration-300"></i>
                            <span>Profile</span>
                        </a>
                    </li>



                    <li class="-ml-5">
                        <a href="{{ route('user.riwayat-surat.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                {{ request()->routeIs('user.riwayat-surat*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <i class="fa-solid fa-history text-lg transition-all duration-300"></i>
                            <span>Riwayat Surat</span>
                        </a>
                    </li>

                    <li class="-ml-5">
                        <a href="{{ route('user.laporan-desa.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                {{ request()->routeIs('user.laporan-desa*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <i class="fa-solid fa-bullhorn text-lg transition-all duration-300"></i>
                            <span>Lapor Desa</span>
                        </a>
                    </li>

                    <li class="-ml-5">
                        <button type="button"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white"
                            onclick="toggleDropdown('userBeritaDropdown')">
                            <i class="fa-solid fa-newspaper text-lg transition-all duration-300"></i>
                            <span>Berita Desa</span>
                            <i id="dropdown-icon-user-berita" class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="userBeritaDropdown" class="hidden space-y-2 pl-6">
                            <li>
                                <a href="{{ route('user.berita-desa.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                        {{ request()->routeIs('user.berita-desa.index')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Daftar Berita</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('user.berita-desa.create') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                        {{ request()->routeIs('user.berita-desa.create')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Buat Berita Desa</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="-ml-5">
                        <a href="{{ route('user.pengumuman.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                {{ request()->routeIs('user.pengumuman*')
                    ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]'
                    : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                            <i class="fa-solid fa-bullhorn text-lg transition-all duration-300"></i>
                            <span>Pengumuman</span>
                        </a>
                    </li>

                    <!-- Warungku (User) - ditempatkan tepat di atas Logout -->
                    <li class="-ml-5">
                        <button type="button"
                            class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white"
                            onclick="toggleDropdown('userWarungkuDropdown')">
                            <i class="fa-solid fa-store text-lg transition-all duration-300"></i>
                            <span>Warungku</span>
                            <i id="dropdown-icon-user-warungku" class="fa-solid fa-chevron-down ml-auto transition-all duration-300"></i>
                        </button>
                        <ul id="userWarungkuDropdown" class="hidden space-y-2 pl-6">
                            <li>
                                <a href="{{ route('user.warungku.index') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                        {{ request()->routeIs('user.warungku.index') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
                                    <span>Semua Produk</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('user.warungku.my') }}" class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300
                                                                                                                        {{ request()->routeIs('user.warungku.my') || request()->routeIs('user.warungku.create') || request()->routeIs('user.warungku.edit') ? 'bg-[#2D336B] text-white hover:bg-[#D1D5DB] hover:text-[#2D336B]' : 'text-[#2D336B] hover:bg-[#D1D5DB] hover:text-white' }}">
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
                                class="flex items-center w-full p-3 pl-6 gap-3 rounded-r-full transition-all duration-300 text-[#2D336B] hover:bg-red-100 hover:text-red-700">
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
        } else if (id === 'masterKeperluanDropdown') {
            const icon = document.getElementById('dropdown-icon-master-keperluan');
            icon.classList.toggle('rotate-180');
        } else if (id === 'kelolaAsetDropdown') {
            const icon = document.getElementById('dropdown-icon-kelola-aset');
            icon.classList.toggle('rotate-180');
        }

        dropdown.classList.toggle('hidden');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const toggleButton = document.querySelector('[data-drawer-toggle="logo-sidebar"]');
        const sidebar = document.getElementById('sidebar');

        // Toggle sidebar on mobile
        toggleButton.addEventListener('click', () => {
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
            } else {
                sidebar.classList.add('-translate-x-full');
            }
        });

        // Initialize user dropdown menu
        const userMenuButton = document.querySelector('[data-dropdown-toggle="dropdown-user"]');
        const userMenu = document.getElementById('dropdown-user');

        if (userMenuButton && userMenu) {
            userMenuButton.addEventListener('click', () => {
                userMenu.classList.toggle('hidden');
            });

            // Close menu when clicking outside
            document.addEventListener('click', (event) => {
                if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                    userMenu.classList.add('hidden');
                }
            });
        }

        // Auto-open dropdowns if a child link is active
        const checkDropdownForActiveItems = (dropdownId, iconId) => {
            const dropdown = document.getElementById(dropdownId);
            const icon = document.getElementById(iconId);

            if (!dropdown) return;

            // Check if any child link has the active class
            const hasActiveChild = dropdown.querySelector('a.bg-\\[\\#2D336B\\]');

            if (hasActiveChild) {
                dropdown.classList.remove('hidden');
                if (icon) icon.classList.add('rotate-180');
            }
        };

        // Check each dropdown
        checkDropdownForActiveItems('pendudukDropdown', 'dropdown-icon-penduduk');
        checkDropdownForActiveItems('suratDropdown', 'dropdown-icon-surat');
        checkDropdownForActiveItems('wilayahDropdown', 'dropdown-icon-wilayah');
        checkDropdownForActiveItems('masterSuratDropdown', 'dropdown-icon-master-surat');
        checkDropdownForActiveItems('masterUsersDropdown', 'dropdown-icon-master-users');
        checkDropdownForActiveItems('kelolaAsetDropdown', 'dropdown-icon-kelola-aset');
        checkDropdownForActiveItems('masterKeperluanDropdown', 'dropdown-icon-master-keperluan');

        // User Warungku dropdown auto-open
        checkDropdownForActiveItems('userWarungkuDropdown', 'dropdown-icon-user-warungku');
        checkDropdownForActiveItems('userWarungkuDropdownUser', 'dropdown-icon-user-warungku-user');
        checkDropdownForActiveItems('userWarungkuDropdownUserBlock', 'dropdown-icon-user-warungku-user-block');
        checkDropdownForActiveItems('userWarungkuUser', 'dropdown-icon-user-warungku-user2');
    });
</script>

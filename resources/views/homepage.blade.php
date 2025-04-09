<x-guest-layout>
    <!-- Hero Section -->
    <section class="flex flex-col-reverse md:flex-row items-center justify-between px-6 md:px-20 py-16 overflow-visible relative">
        <div class="max-w-xl space-y-6" data-aos="fade-right">
            <h1 class="text-4xl md:text-5xl font-extrabold text-black">Selamat Datang di Portal Layanan Desa</h1>
            <p class="text-lg text-black">Solusi cerdas untuk manajemen data warga, pelaporan cepat, dan analisis statistik di berbagai daerah.</p>
            <div class="flex gap-4">
                <a href="#pelayanan" class="bg-[#969BE7] text-white px-6 py-3 rounded-full shadow-md hover:shadow-lg hover:opacity-90 transition duration-200 inline-block">Mulai Sekarang</a>
            </div>
        </div>
        <!-- Modern Mobile Mockup with Glass Cards (HP pakai border) -->
        <div class="relative w-[240px] h-[480px] rounded-[2rem] border-[5px] border-black shadow-2xl overflow-visible bg-white mx-auto md:mx-0 scale-105 rotate-[-3deg] transition-all duration-300 ease-in-out" data-aos="fade-left">
            <!-- Notch -->
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-20 h-4 bg-black rounded-b-xl z-10"></div>
            <!-- Status Bar (iPhone style) -->
            <div class="absolute top-1 left-0 w-full px-4 flex justify-between items-center text-[10px] text-gray-700 font-medium z-20">
                <!-- Jam -->
                <span>9:41</span>

                <!-- Ikon status -->
                <div class="flex items-center space-x-1 scale-90">
                    <!-- Sinyal (bars) -->
                    <div class="flex space-x-[1px] items-end">
                        <div class="w-[2px] h-[10px] bg-gray-700 rounded-sm"></div>
                        <div class="w-[2px] h-[8px] bg-gray-700 rounded-sm"></div>
                        <div class="w-[2px] h-[6px] bg-gray-700 rounded-sm"></div>
                        <div class="w-[2px] h-[4px] bg-gray-700 rounded-sm"></div>
                    </div>

                    <!-- Wi-Fi (dot + waves) -->
                    <div class="relative w-4 h-4">
                        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-[3px] h-[3px] bg-gray-700 rounded-full"></div>
                        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-[10px] h-[10px] border-[1px] border-gray-700 rounded-full"></div>
                    </div>

                    <!-- Baterai -->
                    <div class="relative flex items-center">
                        <div class="w-5 h-2.5 border border-gray-700 rounded-sm flex items-center justify-end pr-[1px]">
                            <div class="w-[60%] h-[70%] bg-gray-700 rounded-sm"></div>
                        </div>
                        <div class="w-[1.5px] h-[6px] bg-gray-700 ml-[1px] rounded-sm"></div>
                    </div>
                </div>
            </div>

            <!-- Phone Screen Content -->
            <div class="p-4 pt-6 text-xs h-full overflow-y-auto rounded-[2rem]">
                <h2 class="text-sm font-semibold text-gray-800">Halo, Alex</h2>
                <p class="text-[11px] text-gray-500 mb-2">Statistik Kependudukan</p>

                <div class="mb-3">
                    <h3 class="font-bold text-xl text-indigo-600">84.897 Jiwa</h3>
                    <p class="text-gray-600 text-[11px]">Total Penduduk</p>
                </div>

                <!-- Pie Chart -->
                <div class="flex justify-center mb-3">
                    <img src="{{ asset('images/statistik.jpg') }}" alt="Statistik Warga" class="w-36 h-36 object-contain">
                </div>

                <!-- Mini Stats -->
                <div class="grid grid-cols-2 gap-2 text-[10px] text-gray-700">
                    <div class="bg-white/60 p-2 rounded-xl shadow-sm backdrop-blur-md">
                        <p class="font-semibold">Usia Produktif</p>
                        <p>65%</p>
                    </div>
                    <div class="bg-white/60 p-2 rounded-xl shadow-sm backdrop-blur-md">
                        <p class="font-semibold">Anak-anak</p>
                        <p>18%</p>
                    </div>
                    <div class="bg-white/60 p-2 rounded-xl shadow-sm backdrop-blur-md">
                        <p class="font-semibold">Lansia</p>
                        <p>12%</p>
                    </div>
                    <div class="bg-white/60 p-2 rounded-xl shadow-sm backdrop-blur-md">
                        <p class="font-semibold">Pekerja</p>
                        <p>70%</p>
                    </div>
                </div>
            </div>

            <!-- Floating Cards (tanpa border) -->
            <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-[75%] w-52 bg-white/80 p-4 rounded-2xl shadow-2xl backdrop-blur-xl z-30"
                data-aos="fade-up" data-aos-duration="800">
                <p class="text-[11px] text-gray-600">Statistik Mingguan</p>
                <h4 class="text-lg font-bold text-indigo-600">+1.245 Jiwa</h4>
                <p class="text-[10px] text-gray-500">Pertumbuhan Penduduk</p>
            </div>

            <div class="absolute -left-24 top-28 w-40 bg-white/70 p-4 rounded-2xl shadow-xl backdrop-blur-md text-[11px] z-20"
                data-aos="fade-right" data-aos-duration="800" data-aos-delay="100">
                <p class="text-blue-500 font-semibold">Laki-laki</p>
                <p class="text-gray-800 font-medium text-sm">43.667 Jiwa</p>
            </div>

            <div class="absolute -left-24 bottom-4 w-40 bg-white/70 p-4 rounded-2xl shadow-xl backdrop-blur-md text-[11px] z-20"
                data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                <p class="text-indigo-600 font-semibold">Jumlah KK</p>
                <p class="text-gray-800 font-medium text-sm">22.315 KK</p>
            </div>

            <div class="absolute -right-24 bottom-20 w-40 bg-white/70 p-4 rounded-2xl shadow-xl backdrop-blur-md text-[11px] z-20"
                data-aos="fade-left" data-aos-duration="800" data-aos-delay="300">
                <p class="text-pink-500 font-semibold">Perempuan</p>
                <p class="text-gray-800 font-medium text-sm">41.230 Jiwa</p>
            </div>
        </div>
    </section>

    <!-- Section: Pelayanan Form -->
    <section id="pelayanan" class="bg-[#FCF8FB] px-6 md:px-20 py-20 rounded-t-3xl shadow-lg" data-aos="fade-up">
        <h2 class="text-3xl font-bold text-black text-center mb-12">Layanan Desa</h2>

        <x-pelayanan-form :provinces="$provinces" :keperluanList="$keperluanList" />
    </section>

    <!-- Section: Statistik -->
    <section class="bg-white text-black px-6 md:px-20 py-16" data-aos="zoom-in">
        <div class="grid md:grid-cols-3 gap-10 text-center">
            <div>
                <h4 class="text-4xl font-bold">1.2M+</h4>
                <p>Penduduk Tercatat</p>
            </div>
            <div>
                <h4 class="text-4xl font-bold">500+</h4>
                <p>Instansi Terdaftar</p>
            </div>
            <div>
                <h4 class="text-4xl font-bold">97%</h4>
                <p>Tingkat Kepuasan</p>
            </div>
        </div>
    </section>
</x-guest-layout>

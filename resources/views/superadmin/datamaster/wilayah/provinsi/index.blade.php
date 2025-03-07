<x-layout>
    <div class="p-4 mt-14">
        @if(session('success'))
            <div id="success-alert" class="fixed top-5 right-5 z-50 flex items-center p-4 mb-4 text-white bg-green-500 rounded-lg shadow-lg transition-opacity duration-500">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 11.414V8a1 1 0 10-2 0v5.414l-.707-.707a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 00-1.414-1.414l-.707.707z" clip-rule="evenodd"></path>
                </svg>
                <span>{{ session('success') }}</span>
                <button onclick="closeAlert()" class="ml-4 text-white focus:outline-none">
                    ✖
                </button>
            </div>
        @endif

        <!-- Alert Error -->
        @if(session('error'))
            <div id="error-alert" class="fixed top-5 right-5 z-50 flex items-center p-4 mb-4 text-white bg-red-500 rounded-lg shadow-lg transition-opacity duration-500">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 11.414V8a1 1 0 10-2 0v5.414l-.707-.707a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 00-1.414-1.414l-.707.707z" clip-rule="evenodd"></path>
                </svg>
                <span>{{ session('error') }}</span>
                <button onclick="closeAlert()" class="ml-4 text-white focus:outline-none">
                    ✖
                </button>
            </div>
        @endif

        <!-- Judul H1 -->
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Master Data Provinsi</h1>

        <!-- Bar untuk Search dan Tambah Pasien -->
        <div class="flex justify-between items-center mb-4">
            <!-- Input Pencarian -->
<!-- Input Pencarian -->
<form method="GET" action="" class="relative">
    <input
        type="text"
        name="search"
        id="search"
        value=""
        class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
        placeholder="Cari data kk..."
    />
    <button type="submit" class="absolute top-1/2 left-2 w-5 h-5 text-gray-400 transform -translate-y-1/2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 1110.15-10.15 7.5 7.5 0 01-10.15 10.15z" />
        </svg>
    </button>
</form>

            <button
    type="button"
    onclick="window.location.href='{{ route('superadmin.datamaster.job.create') }}'"
    class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-sm px-5 py-2.5 flex items-center space-x-2"
>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
    </svg>
    <span>Tambah Data KK</span>
</button>


        </div>


        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                    <tr>
                        <th scope="col" class="px-6 py-3">ID</th>
                        <th scope="col" class="px-6 py-3">Nama Provinsi</th>
                        <th scope="col" class="px-6 py-3">Jumlah Kabupaten</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($provinces as $province)
                        <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $province['id'] }}</td>
                            <td class="px-6 py-4">{{ $province['name'] }}</td>
                            <td class="px-6 py-4">{{ $province['kabupaten_count'] ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center">Tidak ada data provinsi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <script>
            function getKabupaten(provinceId) {
                window.location.href = `/wilayah/provinsi/${provinceId}/kabupaten`;
            }
        </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function closeAlert(alertId) {
            document.getElementById(alertId).classList.add('hidden');
        }

        setTimeout(function() {
            const successAlert = document.getElementById('successAlert');
            if (successAlert) {
                successAlert.classList.add('opacity-0', 'transition-opacity', 'duration-1000');
                setTimeout(() => successAlert.classList.add('hidden'), 1000);
            }

            const errorAlert = document.getElementById('errorAlert');
            if (errorAlert) {
                errorAlert.classList.add('opacity-0', 'transition-opacity', 'duration-1000');
                setTimeout(() => errorAlert.classList.add('hidden'), 1000);
            }
        }, 5000);


    </script>
</x-layout>

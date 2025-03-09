<x-layout>
    <div class="p-4 mt-14">
        <!-- Alert Sukses -->
        {{-- @if(session('success'))
            <div id="successAlert" class="flex items-center p-4 mb-4 text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-green-800 dark:text-green-300 relative" role="alert">
                <svg class="w-5 h-5 mr-2 text-green-800 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="font-medium">Sukses!</span> {{ session('success') }}
                <button type="button" class="absolute top-2 right-2 text-green-800 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-900 rounded-lg p-1 transition-all duration-300" onclick="closeAlert('successAlert')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        <!-- Alert Error -->
        @if(session('error'))
            <div id="errorAlert" class="flex items-center p-4 mb-4 text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-red-800 dark:text-red-300 relative" role="alert">
                <svg class="w-5 h-5 mr-2 text-red-800 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636L5.636 18.364M5.636 5.636l12.728 12.728"></path>
                </svg>
                <span class="font-medium">Gagal!</span> {{ session('error') }}
                <button type="button" class="absolute top-2 right-2 text-red-800 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-900 rounded-lg p-1 transition-all duration-300" onclick="closeAlert('errorAlert')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif --}}

        <!-- Judul H1 -->
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Pekerjaan</h1>

        <!-- Form Tambah Pekerjaan -->
        <form method="POST" action="{{ route('jobs.store') }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            <div class="grid grid-cols-1 gap-4">
                <!-- Kode -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">Kode</label>
                    <input type="text" id="code" name="code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Nama -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" id="name" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="w-full bg-indigo-500 text-white p-2 rounded-md shadow-md hover:bg-indigo-600">Simpan</button>
            </div>
        </form>
    </div>
</x-layout>

<!-- Add SweetAlert CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Replace existing alert script with SweetAlert
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{{ session('error') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif
</script>

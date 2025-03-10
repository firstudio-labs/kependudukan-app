<x-layout>
    <div class="p-4 mt-14">


        <!-- Judul H1 -->
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Pekerjaan</h1>

        <!-- Form Tambah Pekerjaan -->
        <form method="POST" action="{{ route('jobs.store') }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            <div class="grid grid-cols-1 gap-4">
                <!-- Kode -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">Kode</label>
                    <input type="text" id="code" name="code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" placeholder="Masukkan kode min:3" minlength="3" required>
                </div>

                <!-- Nama -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" id="name" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" placeholder="Masukkan nama min:5" minlength="5" required>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="w-full bg-indigo-500 text-white p-2 rounded-md shadow-md hover:bg-indigo-600">Simpan</button>
            </div>
        </form>
    </div>
</x-layout>


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

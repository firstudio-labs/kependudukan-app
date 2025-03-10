<x-layout>
    <div class="p-4 mt-14">
        <!-- Judul H1 -->
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Pekerjaan</h1>

        <!-- Form Edit Pekerjaan -->
        <form action="{{ route('jobs.update', $job['id']) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-4">
                <!-- Kode -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">Kode</label>
                    <input type="text" id="code" name="code" value="{{ $job['code'] }}" placeholder="Minimal 3 karakter" minlength="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Nama -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" id="name" name="name" value="{{ $job['name'] }}" placeholder="Minimal 5 karakter" minlength="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="w-full bg-indigo-500 text-white p-2 rounded-md shadow-md hover:bg-indigo-600">Update</button>
            </div>
        </form>
    </div>


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
</x-layout>

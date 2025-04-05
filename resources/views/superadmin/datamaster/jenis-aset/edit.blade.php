<x-layout>
    <div class="p-4 mt-14">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Jenis Aset</h1>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('superadmin.datamaster.jenis-aset.update', $jenisAset->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 mb-6">
                    <div>
                        <label for="kode" class="block text-sm font-medium text-gray-700 mb-1">Kode <span
                                class="text-red-600">*</span></label>
                        <input type="number" name="kode" id="kode" value="{{ old('kode', $jenisAset->kode) }}"
                            class="w-full p-2.5 text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            required>
                        @error('kode')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Masukkan kode numerik unik untuk jenis aset</p>
                    </div>

                    <div>
                        <label for="jenis_aset" class="block text-sm font-medium text-gray-700 mb-1">Jenis
                            Aset <span class="text-red-600">*</span></label>
                        <input type="text" name="jenis_aset" id="jenis_aset"
                            value="{{ old('jenis_aset', $jenisAset->jenis_aset) }}"
                            class="w-full p-2.5 text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            required>
                        @error('jenis_aset')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="4"
                            class="w-full p-2.5 text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('keterangan', $jenisAset->keterangan) }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button"
                        onclick="window.location.href='{{ route('superadmin.datamaster.jenis-aset.index') }}'"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-[#7886C7] hover:bg-[#2D336B] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#5C69A7]">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Form validation enhancements if needed
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');
            form.addEventListener('submit', function (event) {
                const kode = document.getElementById('kode').value;
                const jenisAset = document.getElementById('jenis_aset').value;

                if (!kode || !jenisAset) {
                    event.preventDefault();
                    alert('Kode dan Jenis Aset tidak boleh kosong!');
                }
            });
        });

        // SweetAlert for success/error messages
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
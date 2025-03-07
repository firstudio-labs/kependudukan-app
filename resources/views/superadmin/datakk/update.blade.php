<x-layout>
    <div class="p-4 mt-14">
        <!-- Pesan Sukses/Gagal -->
        @if(session('success'))
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
        @endif

        <!-- Judul H1 -->
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Data KK</h1>

        <!-- Form Edit Data KK -->
        <form method="POST" action="{{ route('kk.update', $kk->id) }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT') <!-- Method Spoofing untuk UPDATE -->

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Kolom 1 -->
                <div>
                    <label for="kk" class="block text-sm font-medium text-gray-700">No KK</label>
                    <input type="text" name="kk" id="kk" value="{{ $kk->kk }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2 bg-gray-100" readonly>
                </div>
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" name="full_name" id="full_name" value="{{ $kk->full_name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2 bg-gray-100" readonly>
                </div>
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea name="address" id="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>{{ $kk->address }}</textarea>
                </div>
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Kode Pos</label>
                    <input type="text" name="postal_code" id="postal_code" value="{{ $kk->postal_code }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>
                <div>
                    <label for="rt" class="block text-sm font-medium text-gray-700">RT</label>
                    <input type="text" name="rt" id="rt" value="{{ $kk->rt }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>
                <div>
                    <label for="rw" class="block text-sm font-medium text-gray-700">RW</label>
                    <input type="text" name="rw" id="rw" value="{{ $kk->rw }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>
                <div>
                    <label for="jml_anggota_kk" class="block text-sm font-medium text-gray-700">Jumlah Anggota Keluarga</label>
                    <input type="number" name="jml_anggota_kk" id="jml_anggota_kk" value="{{ $kk->jml_anggota_kk }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>
                <div>
                    <label for="telepon" class="block text-sm font-medium text-gray-700">Telepon</label>
                    <input type="text" name="telepon" id="telepon" value="{{ $kk->telepon }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ $kk->email }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>
            </div>

            <!-- Kategori: Data Wilayah -->
            <div class="mt-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Data Wilayah</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="province_id" class="block text-sm font-medium text-gray-700">Provinsi</label>
                        <select name="province_id" id="province_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province['id'] }}" {{ $kk->province_id == $province['id'] ? 'selected' : '' }}>
                                    {{ $province['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="district_id" class="block text-sm font-medium text-gray-700">Kabupaten</label>
                        <select name="district_id" id="district_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Pilih Kabupaten</option>
                        </select>
                    </div>
                    <div>
                        <label for="kecamatan" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                        <select name="kecamatan" id="kecamatan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Pilih Kecamatan</option>
                            <option value="Coblong" {{ $kk->kecamatan == 'Coblong' ? 'selected' : '' }}>Coblong</option>
                            <option value="Tembalang" {{ $kk->kecamatan == 'Tembalang' ? 'selected' : '' }}>Tembalang</option>
                            <option value="Gubeng" {{ $kk->kecamatan == 'Gubeng' ? 'selected' : '' }}>Gubeng</option>
                            <!-- Tambahkan opsi kecamatan lainnya -->
                        </select>
                    </div>
                    <div>
                        <label for="desa_kelurahan" class="block text-sm font-medium text-gray-700">Desa/Kelurahan</label>
                        <select name="desa_kelurahan" id="desa_kelurahan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Pilih Desa/Kelurahan</option>
                            <option value="Dago" {{ $kk->desa_kelurahan == 'Dago' ? 'selected' : '' }}>Dago</option>
                            <option value="Tembalang" {{ $kk->desa_kelurahan == 'Tembalang' ? 'selected' : '' }}>Tembalang</option>
                            <option value="Gubeng" {{ $kk->desa_kelurahan == 'Gubeng' ? 'selected' : '' }}>Gubeng</option>
                            <!-- Tambahkan opsi desa/kelurahan lainnya -->
                        </select>
                    </div>
                    <div>
                        <label for="dusun" class="block text-sm font-medium text-gray-700">Dusun/Dukuh/Kampung</label>
                        <select name="dusun" id="dusun" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                            <option value="">Pilih Dusun/Dukuh/Kampung</option>
                            <option value="Dusun 1" {{ $kk->dusun == 'Dusun 1' ? 'selected' : '' }}>Dusun 1</option>
                            <option value="Dusun 2" {{ $kk->dusun == 'Dusun 2' ? 'selected' : '' }}>Dusun 2</option>
                            <option value="Dusun 3" {{ $kk->dusun == 'Dusun 3' ? 'selected' : '' }}>Dusun 3</option>
                            <!-- Tambahkan opsi dusun lainnya -->
                        </select>
                    </div>
                </div>
            </div>

            <!-- Kategori: Alamat di Luar Negeri -->
            <div class="mt-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Alamat di Luar Negeri (diisi oleh WNI di luar wilayah NKRI)</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="alamat_luar_negeri" class="block text-sm font-medium text-gray-700">Alamat Luar Negeri</label>
                        <textarea name="alamat_luar_negeri" id="alamat_luar_negeri" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">{{ $kk->alamat_luar_negeri }}</textarea>
                    </div>
                    <div>
                        <label for="kota" class="block text-sm font-medium text-gray-700">Kota</label>
                        <input type="text" name="kota" id="kota" value="{{ $kk->kota }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>
                    <div>
                        <label for="negara_bagian" class="block text-sm font-medium text-gray-700">Provinsi/Negara Bagian</label>
                        <input type="text" name="negara_bagian" id="negara_bagian" value="{{ $kk->negara_bagian }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>
                    <div>
                        <label for="negara" class="block text-sm font-medium text-gray-700">Negara</label>
                        <input type="text" name="negara" id="negara" value="{{ $kk->negara }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>
                    <div>
                        <label for="kode_pos_luar_negeri" class="block text-sm font-medium text-gray-700">Kode Pos Luar Negeri</label>
                        <input type="text" name="kode_pos_luar_negeri" id="kode_pos_luar_negeri" value="{{ $kk->kode_pos_luar_negeri }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan dan Batal -->
            <div class="mt-6 flex justify-end space-x-4">
                <a href="{{ route('superadmin.datakk.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">Batal</a>
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">Simpan Perubahan</button>
            </div>
        </form>
    </div>

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

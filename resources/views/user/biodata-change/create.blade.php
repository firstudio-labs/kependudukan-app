<x-layout>
    <div class="p-4 mt-6 max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Ajukan Perubahan Biodata</h1>

        <div class="bg-blue-50 border border-blue-200 text-blue-800 p-3 rounded mb-4">
            Perubahan akan diproses setelah disetujui admin desa.
        </div>

        <form method="POST" action="{{ route('user.biodata-change.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">NIK</label>
                <input value="{{ $nik }}" disabled class="mt-1 w-full border rounded p-2 bg-gray-100" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input name="full_name" value="{{ old('full_name', $currentData['full_name'] ?? $currentData['name'] ?? '') }}" class="mt-1 w-full border rounded p-2" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nomor KK</label>
                    <input name="kk" value="{{ old('kk', $currentData['kk'] ?? '') }}" class="mt-1 w-full border rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                    <input name="gender" value="{{ old('gender', $currentData['gender'] ?? '') }}" class="mt-1 w-full border rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Umur</label>
                    <input name="age" value="{{ old('age', $currentData['age'] ?? '') }}" class="mt-1 w-full border rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                    <input name="birth_place" value="{{ old('birth_place', $currentData['birth_place'] ?? '') }}" class="mt-1 w-full border rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                    <input name="birth_date" value="{{ old('birth_date', $currentData['birth_date'] ?? '') }}" class="mt-1 w-full border rounded p-2" />
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea name="address" class="mt-1 w-full border rounded p-2" rows="2">{{ old('address', $currentData['address'] ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">RT</label>
                    <input name="rt" value="{{ old('rt', $currentData['rt'] ?? '') }}" class="mt-1 w-full border rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">RW</label>
                    <input name="rw" value="{{ old('rw', $currentData['rw'] ?? '') }}" class="mt-1 w-full border rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Provinsi</label>
                    <input name="province_name" value="{{ old('province_name', $currentData['province_name'] ?? '') }}" class="mt-1 w-full border rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kabupaten</label>
                    <input name="district_name" value="{{ old('district_name', $currentData['district_name'] ?? '') }}" class="mt-1 w-full border rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kecamatan</label>
                    <input name="sub_district_name" value="{{ old('sub_district_name', $currentData['sub_district_name'] ?? '') }}" class="mt-1 w-full border rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Desa</label>
                    <input name="village_name" value="{{ old('village_name', $currentData['village_name'] ?? '') }}" class="mt-1 w-full border rounded p-2" />
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('user.biodata-change.index') }}" class="px-4 py-2 border rounded">Batal</a>
                <button type="submit" class="bg-[#4A47DC] text-white px-4 py-2 rounded">Kirim Permintaan</button>
            </div>
        </form>
    </div>
</x-layout>



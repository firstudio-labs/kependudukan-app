<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Biodata</h1>

        <div class="flex justify-between items-center mb-4">
            <form method="GET" action="" class="relative w-full max-w-xs">
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari data biodata..." />
                <button type="submit" class="absolute top-1/2 left-2 w-5 h-5 text-gray-400 transform -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 1110.15-10.15 7.5 7.5 0 01-10.15 10.15z" />
                    </svg>
                </button>
            </form>

            <div class="flex space-x-2">



                <!-- Tombol Export -->
                <form method="GET" action="{{ route('admin.desa.biodata.index') }}">
                    @csrf
                    <input type="hidden" name="export" value="1">
                    <button type="submit"
                        class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Export Excel</span>
                    </button>
                </form>

                {{-- <button type="button" onclick="window.location.href='{{ route('admin.desa.biodata.create') }}'"
                    class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-sm px-5 py-2.5 flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Data Biodata</span>
                </button> --}}



            </div>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Foto</th>
                        <th class="px-6 py-3">NIK</th>
                        <th class="px-6 py-3">Nama Lengkap</th>
                        <th class="px-6 py-3">Alamat</th>
                        <th class="px-6 py-3">RT</th>
                        <th class="px-6 py-3">RW</th>
                        <th class="px-6 py-3">SHDK</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $pagination = $citizens['data']['pagination'] ?? [
                            'current_page' => 1,
                            'items_per_page' => 10,
                            'total_items' => 0
                        ];

                        $currentPage = $pagination['current_page'];
                        $itemsPerPage = $pagination['items_per_page'];
                        $totalItems = $pagination['total_items'];
                        $startNumber = ($currentPage - 1) * $itemsPerPage + 1;
                        $endNumber = min($startNumber + $itemsPerPage - 1, $totalItems);
                    @endphp
                    @forelse($citizens['data']['citizens'] ?? [] as $index => $citizen)
                        <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ $startNumber + $index }}</th>
                            <td class="px-6 py-4">
                                <div id="photo-{{ $citizen['nik'] }}" class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </td>
                            <td class="px-6 py-4">{{ $citizen['nik'] }}</td>
                            <td class="px-6 py-4">{{ $citizen['full_name'] }}</td>
                            <td class="px-6 py-4">{{ $citizen['address'] }}</td>
                            <td class="px-6 py-4">{{ $citizen['rt'] ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $citizen['rw'] ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $citizen['family_status'] }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    @if($citizen['status'] == 'Active') bg-green-100 text-green-800
                                    @elseif($citizen['status'] == 'Inactive') bg-yellow-100 text-yellow-800
                                    @elseif($citizen['status'] == 'Deceased') bg-red-100 text-red-800
                                    @elseif($citizen['status'] == 'Moved') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $citizen['status'] ?? 'Active' }}
                                </span>
                            </td>
                            <td class="flex items-center px-6 py-4 space-x-2">
                                <button onclick="showDetailModal({{ json_encode($citizen) }})"
                                    class="text-blue-600 hover:text-blue-800" aria-label="Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <a href="{{ route('admin.desa.biodata.edit', ['nik' => $citizen['nik'], 'page' => $currentPage]) }}"
                                    class="text-yellow-600 hover:text-yellow-800" aria-label="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form
                                    action="{{ route('admin.desa.biodata.destroy', ['id' => $citizen['nik'], 'page' => $currentPage]) }}"
                                    method="POST" onsubmit="return confirmDelete(event)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-red-600 hover:underline ml-3">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if(isset($citizens['data']['pagination']) && $citizens['data']['pagination']['total_page'] > 0)
                @php
                    // Create a proper pagination object that the component can use
                    $paginationData = new \Illuminate\Pagination\LengthAwarePaginator(
                        $citizens['data']['citizens'] ?? [],
                        $citizens['data']['pagination']['total_items'] ?? 0,
                        $citizens['data']['pagination']['items_per_page'] ?? 10,
                        $citizens['data']['pagination']['current_page'] ?? 1,
                        [
                            'path' => request()->url(),
                            'query' => request()->query(),
                        ]
                    );
                @endphp

                <x-pagination :data="$paginationData" />
            @else
                <div class="px-4 py-3 text-sm text-gray-700">
                    Showing 0 results
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Detail -->
    <div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <!-- Modal Backdrop -->
            <div class="fixed inset-0 bg-black opacity-50"></div>

            <!-- Modal Content -->
            <div class="relative w-full max-w-4xl bg-white rounded-lg shadow-xl overflow-hidden">
                <!-- Modal Header -->
                <div
                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-[#7886C7] bg-gray-50">
                    <h3 class="text-xl font-semibold text-[#2D336B]">Detail Biodata</h3>
                    <button onclick="closeDetailModal()"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center">
                        <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-4 md:p-5 overflow-y-auto max-h-[70vh]">
                    <!-- Foto Diri Section -->
                    <div class="mb-6 flex justify-center">
                        <div class="text-center">
                            <div id="detailPhoto" class="w-32 h-32 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2">
                                <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div id="photoStatus" class="text-sm text-gray-500">Memuat foto...</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Informasi Pribadi -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold mb-4 text-[#7886C7]">Informasi Pribadi</h4>
                            <div class="space-y-3">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">NIK</span>
                                    <span id="detailNIK" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Nomor KK</span>
                                    <span id="detailKK" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Nama Lengkap</span>
                                    <span id="detailFullName" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Jenis Kelamin</span>
                                    <span id="detailGender" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Tempat, Tanggal Lahir</span>
                                    <span class="font-medium">
                                        <span id="detailBirthPlace"></span>,
                                        <span id="detailBirthDate"></span>
                                    </span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Usia</span>
                                    <span id="detailAge" class="font-medium"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Alamat -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold mb-4 text-[#7886C7]">Informasi Alamat</h4>
                            <div class="space-y-3">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Alamat Lengkap</span>
                                    <span id="detailAddress" class="font-medium"></span>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">RT</span>
                                        <span id="detailRT" class="font-medium"></span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">RW</span>
                                        <span id="detailRW" class="font-medium"></span>
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Desa/Kelurahan</span>
                                    <span id="detailVillageId" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Kecamatan</span>
                                    <span id="detailSubDistrictId" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Kabupaten/Kota</span>
                                    <span id="detailDistrictId" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Provinsi</span>
                                    <span id="detailProvinceId" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Kode Pos</span>
                                    <span id="detailPostalCode" class="font-medium"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Lainnya -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold mb-4 text-[#7886C7]">Informasi Lainnya</h4>
                            <div class="space-y-3">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Status Kewarganegaraan</span>
                                    <span id="detailCitizenStatus" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Agama</span>
                                    <span id="detailReligion" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Golongan Darah</span>
                                    <span id="detailBloodType" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Status Pendidikan</span>
                                    <span id="detailEducationStatus" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Pekerjaan</span>
                                    <span id="detailJobName" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Status dalam Keluarga</span>
                                    <span id="detailFamilyStatus" class="font-medium"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Orangtua -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold mb-4 text-[#7886C7]">Informasi Orangtua</h4>
                            <div class="space-y-3">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Nama Ayah</span>
                                    <span id="detailFather" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">NIK Ayah</span>
                                    <span id="detailNikFather" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Nama Ibu</span>
                                    <span id="detailMother" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">NIK Ibu</span>
                                    <span id="detailNikMother" class="font-medium"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Dokumen Penduduk -->
                        <div class="bg-gray-50 p-4 rounded-lg col-span-2">
                            <h4 class="text-lg font-semibold mb-4 text-[#7886C7]">Dokumen Penduduk</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <!-- Foto Diri -->
                                <div class="border rounded-lg p-3">
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">Foto Diri</h5>
                                    <div id="detailFotoDiri" class="text-center">
                                        <div class="animate-pulse bg-gray-200 rounded-lg h-24 w-full mb-2"></div>
                                        <span class="text-xs text-gray-500">Memuat...</span>
                                    </div>
                                </div>

                                <!-- Foto KTP -->
                                <div class="border rounded-lg p-3">
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">Foto KTP</h5>
                                    <div id="detailFotoKtp" class="text-center">
                                        <div class="animate-pulse bg-gray-200 rounded-lg h-24 w-full mb-2"></div>
                                        <span class="text-xs text-gray-500">Memuat...</span>
                                    </div>
                                </div>

                                <!-- Akta Kelahiran -->
                                <div class="border rounded-lg p-3">
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">Akta Kelahiran</h5>
                                    <div id="detailFotoAkta" class="text-center">
                                        <div class="animate-pulse bg-gray-200 rounded-lg h-24 w-full mb-2"></div>
                                        <span class="text-xs text-gray-500">Memuat...</span>
                                    </div>
                                </div>

                                <!-- Ijazah -->
                                <div class="border rounded-lg p-3">
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">Ijazah</h5>
                                    <div id="detailIjazah" class="text-center">
                                        <div class="animate-pulse bg-gray-200 rounded-lg h-24 w-full mb-2"></div>
                                        <span class="text-xs text-gray-500">Memuat...</span>
                                    </div>
                                </div>

                                <!-- Foto KK -->
                                <div class="border rounded-lg p-3">
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">Foto KK</h5>
                                    <div id="detailFotoKk" class="text-center">
                                        <div class="animate-pulse bg-gray-200 rounded-lg h-24 w-full mb-2"></div>
                                        <span class="text-xs text-gray-500">Memuat...</span>
                                    </div>
                                </div>

                                <!-- Foto Rumah -->
                                <div class="border rounded-lg p-3">
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">Foto Rumah</h5>
                                    <div id="detailFotoRumah" class="text-center">
                                        <div class="animate-pulse bg-gray-200 rounded-lg h-24 w-full mb-2"></div>
                                        <span class="text-xs text-gray-500">Memuat...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Anggota Keluarga (Readonly) -->
                        <div class="bg-gray-50 p-4 rounded-lg col-span-2">
                            <h4 class="text-lg font-semibold mb-4 text-[#7886C7]">Data Anggota Keluarga</h4>
                            <div id="familyMembersSection">
                                <div class="flex items-center space-x-2 text-sm text-gray-500">
                                    <div class="animate-spin rounded-full h-5 w-5 border-t-2 border-b-2 border-[#7886C7]"></div>
                                    <span>Memuat data anggota keluarga...</span>
                                </div>
                            </div>
                        </div>

                        <!-- Data Warung → Produk (Readonly) -->
                        <div class="bg-gray-50 p-4 rounded-lg col-span-2">
                            <h4 class="text-lg font-semibold mb-2 text-[#7886C7]">Data Warung → Produk</h4>
                            <div id="warungProdukSection" class="text-sm text-gray-600">
                                <span class="text-gray-500">Tidak ada data warung.</span>
                            </div>
                        </div>

                        <!-- Data Input Laporan (Readonly) -->
                        <div class="bg-gray-50 p-4 rounded-lg col-span-2">
                            <h4 class="text-lg font-semibold mb-2 text-[#7886C7]">Data Input Laporan</h4>
                            <div id="laporanInputSection" class="text-sm text-gray-600">
                                <span class="text-gray-500">Tidak ada laporan.</span>
                            </div>
                        </div>

                        <!-- Data Input Surat (Readonly) -->
                        <div class="bg-gray-50 p-4 rounded-lg col-span-2">
                            <h4 class="text-lg font-semibold mb-2 text-[#7886C7]">Data Input Surat</h4>
                            <div id="suratInputSection" class="text-sm text-gray-600">
                                <span class="text-gray-500">Tidak ada riwayat surat.</span>
                            </div>
                        </div>

                        <!-- Data Input Berita (Readonly) -->
                        <div class="bg-gray-50 p-4 rounded-lg col-span-2">
                            <h4 class="text-lg font-semibold mb-2 text-[#7886C7]">Data Input Berita</h4>
                            <div id="beritaInputSection" class="text-sm text-gray-600">
                                <span class="text-gray-500">Tidak ada berita.</span>
                            </div>
                        </div>

                        <!-- Data Tagihan (Readonly) -->
                        <div class="bg-gray-50 p-4 rounded-lg col-span-2">
                            <h4 class="text-lg font-semibold mb-2 text-[#7886C7]">Data Tagihan</h4>
                            <div id="tagihanSection" class="text-sm text-gray-600">
                                <span class="text-gray-500">Tidak ada tagihan.</span>
                            </div>
                        </div>

                        <!-- Data Aset (Readonly) -->
                        <div class="bg-gray-50 p-4 rounded-lg col-span-2">
                            <h4 class="text-lg font-semibold mb-2 text-[#7886C7]">Data Aset</h4>
                            <div id="asetSection" class="text-sm text-gray-600">
                                <span class="text-gray-500">Tidak ada aset.</span>
                            </div>
                        </div>

                        

                        <!-- Domisili -->
                        <div class="bg-gray-50 p-4 rounded-lg col-span-2">
                            <h4 class="text-lg font-semibold mb-3 text-[#7886C7]">Domisili</h4>
                            <div>
                                <div class="text-sm text-gray-700 mb-2">Lokasi Domisili</div>
                                <div id="domisiliMap" class="w-full h-80 md:h-96 rounded-lg border" style="height:24rem; min-height:20rem;"></div>
                                <div class="mt-3 text-sm text-gray-700 flex flex-col md:flex-row md:items-center md:space-x-6 space-y-1 md:space-y-0">
                                    <div>Alamat: <span id="domisiliAlamat">-</span></div>
                                    <div>Koordinat: <span id="domisiliKoordinat">-</span></div>
                                </div>
                            </div>
                            <div id="domisiliSection" class="text-sm text-gray-600 mt-4"></div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200 rounded-b">
                    <button onclick="closeDetailModal()" type="button"
                        class="text-white bg-gray-500 hover:bg-gray-600 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg text-sm px-5 py-2.5 text-center">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // File name display for Excel import
            const fileInput = document.getElementById('excel_file');
            const fileNameDisplay = document.getElementById('file-name');

            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (fileInput.files.length > 0) {
                        fileNameDisplay.textContent = 'File selected: ' + fileInput.files[0].name;
                    } else {
                        fileNameDisplay.textContent = '';
                    }
                });
            }

            // Kode SweetAlert dan fungsi lainnya
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#2D336B',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#2D336B',
                    timer: 5000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            @endif

            @if(session('import_errors'))
                Swal.fire({
                    icon: 'error',
                    title: 'Import Error',
                    html: "{!! session('import_errors') !!}",
                    confirmButtonColor: '#2D336B',
                    timer: 8000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            @endif
        });

        // API config
        const baseUrl = 'https://api-kependudukan.desaverse.id/api';
        const apiKey = '{{ config('services.kependudukan.key') }}';

        // Cache for location names and codes
        const locationCache = {};

        // Function to fetch location data based on ID and type
        async function fetchLocationData(type, id) {
            if (!id) return null;

            // Check cache first
            const cacheKey = `${type}_${id}`;
            if (locationCache[cacheKey]) {
                return locationCache[cacheKey];
            }

            try {
                switch(type) {
                    case 'province':
                        // For provinces, directly fetch all provinces and find by ID
                        const provResponse = await axios.get(`${baseUrl}/provinces`, {
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-API-Key': apiKey
                            }
                        });

                        if (provResponse.data && provResponse.data.data) {
                            const province = provResponse.data.data.find(p => String(p.id) === String(id));
                            if (province) {
                                locationCache[cacheKey] = province;
                                return province;
                            }
                        }
                        break;

                    case 'district':
                        // For districts, need to iterate through provinces to find the district
                        const provincesResponse = await axios.get(`${baseUrl}/provinces`, {
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-API-Key': apiKey
                            }
                        });

                        if (provincesResponse.data && provincesResponse.data.data) {
                            for (const province of provincesResponse.data.data) {
                                try {
                                    const distResponse = await axios.get(`${baseUrl}/districts/${province.code}`, {
                                        headers: {
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json',
                                            'X-API-Key': apiKey
                                        }
                                    });

                                    if (distResponse.data && distResponse.data.data) {
                                        const district = distResponse.data.data.find(d => String(d.id) === String(id));
                                        if (district) {
                                            district.province = province;
                                            locationCache[cacheKey] = district;
                                            return district;
                                        }
                                    }
                                } catch (e) {
                                    continue;
                                }
                            }
                        }
                        break;

                    case 'subdistrict':
                        let parentDistrictData = null;
                        const parentDistrictId = arguments[2];

                        if (parentDistrictId) {
                            parentDistrictData = await fetchLocationData('district', parentDistrictId);
                        }

                        if (parentDistrictData) {
                            try {
                                const subdistResponse = await axios.get(`${baseUrl}/sub-districts/${parentDistrictData.code}`, {
                                    headers: {
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json',
                                        'X-API-Key': apiKey
                                    }
                                });

                                if (subdistResponse.data && subdistResponse.data.data) {
                                    const subdistrict = subdistResponse.data.data.find(sd => String(sd.id) === String(id));
                                    if (subdistrict) {
                                        subdistrict.district = parentDistrictData;
                                        locationCache[cacheKey] = subdistrict;
                                        return subdistrict;
                                    }
                                }
                            } catch (e) {
                                // Continue to full search
                            }
                        }

                        const allProvincesForSubdist = await axios.get(`${baseUrl}/provinces`, {
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-API-Key': apiKey
                            }
                        });

                        if (allProvincesForSubdist.data && allProvincesForSubdist.data.data) {
                            for (const province of allProvincesForSubdist.data.data) {
                                try {
                                    const districtsInProvince = await axios.get(`${baseUrl}/districts/${province.code}`, {
                                        headers: {
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json',
                                            'X-API-Key': apiKey
                                        }
                                    });

                                    if (districtsInProvince.data && districtsInProvince.data.data) {
                                        for (const district of districtsInProvince.data.data) {
                                            try {
                                                const subdistrictsInDistrict = await axios.get(`${baseUrl}/sub-districts/${district.code}`, {
                                                    headers: {
                                                        'Accept': 'application/json',
                                                        'Content-Type': 'application/json',
                                                        'X-API-Key': apiKey
                                                    }
                                                });

                                                if (subdistrictsInDistrict.data && subdistrictsInDistrict.data.data) {
                                                    const subdistrict = subdistrictsInDistrict.data.data.find(sd => String(sd.id) === String(id));
                                                    if (subdistrict) {
                                                        district.province = province;
                                                        subdistrict.district = district;
                                                        locationCache[cacheKey] = subdistrict;
                                                        return subdistrict;
                                                    }
                                                }
                                            } catch (e) {
                                                continue;
                                            }
                                        }
                                    }
                                } catch (e) {
                                    continue;
                                }
                            }
                        }
                        break;

                    case 'village':
                        const parentSubdistrictId = arguments[2];
                        let parentSubdistrictData = null;

                        if (parentSubdistrictId) {
                            const parentDistrictId = arguments[3];
                            parentSubdistrictData = await fetchLocationData('subdistrict', parentSubdistrictId, parentDistrictId);
                        }

                        if (parentSubdistrictData) {
                            try {
                                const villageResponse = await axios.get(`${baseUrl}/villages/${parentSubdistrictData.code}`, {
                                    headers: {
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json',
                                        'X-API-Key': apiKey
                                    }
                                });

                                if (villageResponse.data && villageResponse.data.data) {
                                    const village = villageResponse.data.data.find(v => String(v.id) === String(id));
                                    if (village) {
                                        village.subdistrict = parentSubdistrictData;
                                        locationCache[cacheKey] = village;
                                        return village;
                                    }
                                }
                            } catch (e) {
                                // Continue to full search
                            }
                        }

                        const allProvincesForVillage = await axios.get(`${baseUrl}/provinces`, {
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-API-Key': apiKey
                            }
                        });

                        if (allProvincesForVillage.data && allProvincesForVillage.data.data) {
                            for (const province of allProvincesForVillage.data.data) {
                                try {
                                    const districtsInProvince = await axios.get(`${baseUrl}/districts/${province.code}`, {
                                        headers: {
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json',
                                            'X-API-Key': apiKey
                                        }
                                    });

                                    if (districtsInProvince.data && districtsInProvince.data.data) {
                                        for (const district of districtsInProvince.data.data) {
                                            try {
                                                const subdistrictsInDistrict = await axios.get(`${baseUrl}/sub-districts/${district.code}`, {
                                                    headers: {
                                                        'Accept': 'application/json',
                                                        'Content-Type': 'application/json',
                                                        'X-API-Key': apiKey
                                                    }
                                                });

                                                if (subdistrictsInDistrict.data && subdistrictsInDistrict.data.data) {
                                                    for (const subdistrict of subdistrictsInDistrict.data.data) {
                                                        try {
                                                            const villagesInSubdistrict = await axios.get(`${baseUrl}/villages/${subdistrict.code}`, {
                                                                headers: {
                                                                    'Accept': 'application/json',
                                                                    'Content-Type': 'application/json',
                                                                    'X-API-Key': apiKey
                                                                }
                                                            });

                                                            if (villagesInSubdistrict.data && villagesInSubdistrict.data.data) {
                                                                const village = villagesInSubdistrict.data.data.find(v => String(v.id) === String(id));
                                                                if (village) {
                                                                    district.province = province;
                                                                    subdistrict.district = district;
                                                                    village.subdistrict = subdistrict;
                                                                    locationCache[cacheKey] = village;
                                                                    return village;
                                                                }
                                                            }
                                                        } catch (e) {
                                                            continue;
                                                        }
                                                    }
                                                }
                                            } catch (e) {
                                                continue;
                                            }
                                        }
                                    }
                                } catch (e) {
                                    continue;
                                }
                            }
                        }
                        break;
                }

                return null;
            } catch (error) {
                console.error("Error fetching location data:", error);
                return null;
            }
        }

        // Delete confirmation function
        function confirmDelete(event) {
            event.preventDefault();
            const form = event.target;

            Swal.fire({
                title: 'Konfirmasi Hapus Data',
                text: "Data biodata yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2D336B',
                cancelButtonColor: '#d33',
                confirmButtonText: '<i class="fa-solid fa-check"></i> Ya, hapus!',
                cancelButtonText: '<i class="fa-solid fa-times"></i> Batal',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                },
                backdrop: `rgba(0,0,23,0.4)`,
                reverseButtons: true,
                focusConfirm: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus Data...',
                        html: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    form.submit();
                }
            });

            return false;
        }

        // Variable untuk menyimpan NIK saat ini
        let currentNIK = '';

        // Updated showDetailModal function dengan optimasi
        async function showDetailModal(biodata) {
            currentNIK = biodata.nik || '';

            document.getElementById('detailModal').classList.remove('hidden');
            const modalBody = document.querySelector('#detailModal .p-4.md\\:p-5.overflow-y-auto');
            const originalContent = modalBody.innerHTML;

            modalBody.innerHTML = `
                <div class="flex justify-center items-center py-10">
                    <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-[#7886C7]"></div>
                </div>
                <div class="text-center text-gray-500 mt-3">Memuat data detail...</div>
            `;

            // Konversi data sebelum ditampilkan
            const genderMap = {
                '1': 'Laki-laki',
                '2': 'Perempuan'
            };
            const citizenStatusMap = {
                '1': 'WNI',
                '2': 'WNA'
            };
            const bloodTypeMap = {
                '1': 'A', '2': 'B', '3': 'AB', '4': 'O',
                '5': 'A+', '6': 'A-', '7': 'B+', '8': 'B-',
                '9': 'AB+', '10': 'AB-', '11': 'O+', '12': 'O-', '13': 'Tidak Tahu'
            };
            const religionMap = {
                '1': 'Islam', '2': 'Kristen', '3': 'Katolik', '4': 'Hindu',
                '5': 'Buddha', '6': 'Konghucu', '7': 'Kepercayaan Terhadap Tuhan YME'
            };
            const educationStatusMap = {
                '1': 'Tidak/Belum Sekolah', '2': 'Belum Tamat SD/Sederajat',
                '3': 'Tamat SD/Sederajat', '4': 'SLTP/Sederajat',
                '5': 'SLTA/Sederajat', '6': 'Diploma I/II',
                '7': 'Akademi/Diploma III/S. Muda', '8': 'Diploma IV/Strata I',
                '9': 'Strata II', '10': 'Strata III'
            };
            const familyStatusMap = {
                '1': 'Anak', '2': 'Kepala Keluarga', '3': 'Istri',
                '4': 'Orang Tua', '5': 'Mertua', '6': 'Cucu', '7': 'Famili Lain'
            };

            try {
                // OPTIMASI: Load data secara parallel dengan timeout
                const promises = [];
                const timeout = 5000;

                // 1. Load location data dengan timeout
                if (biodata.province_id) {
                    promises.push(
                        Promise.race([
                            fetchLocationData('province', biodata.province_id),
                            new Promise(resolve => setTimeout(() => resolve(null), timeout))
                        ])
                    );
                } else {
                    promises.push(Promise.resolve(null));
                }

                if (biodata.district_id) {
                    promises.push(
                        Promise.race([
                            fetchLocationData('district', biodata.district_id),
                            new Promise(resolve => setTimeout(() => resolve(null), timeout))
                        ])
                    );
                } else {
                    promises.push(Promise.resolve(null));
                }

                if (biodata.sub_district_id) {
                    promises.push(
                        Promise.race([
                            fetchLocationData('subdistrict', biodata.sub_district_id, biodata.district_id),
                            new Promise(resolve => setTimeout(() => resolve(null), timeout))
                        ])
                    );
                } else {
                    promises.push(Promise.resolve(null));
                }

                if (biodata.village_id) {
                    promises.push(
                        Promise.race([
                            fetchLocationData('village', biodata.village_id, biodata.sub_district_id, biodata.district_id),
                            new Promise(resolve => setTimeout(() => resolve(null), timeout))
                        ])
                    );
                } else {
                    promises.push(Promise.resolve(null));
                }

                // 2. Load job data dengan timeout
                if (biodata.job_type_id) {
                    const jobPromise = Promise.race([
                        axios.get(`${baseUrl}/jobs`, {
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-API-Key': apiKey
                            }
                        }).then(response => {
                            if (response.data && response.data.data) {
                                return response.data.data.find(j => String(j.id) === String(biodata.job_type_id)) || null;
                            }
                            return null;
                        }).catch(error => {
                            console.error("Error fetching job data:", error);
                            return null;
                        }),
                        new Promise(resolve => setTimeout(() => resolve(null), timeout))
                    ]);
                    promises.push(jobPromise);
                } else {
                    promises.push(Promise.resolve(null));
                }

                // 3. Load documents dengan timeout (hanya jika ada NIK)
                if (biodata.nik) {
                    const documentsPromise = Promise.race([
                        fetch(`/admin/family-member/${biodata.nik}/documents`)
                            .then(response => response.json())
                            .catch(error => {
                                console.error('Error loading documents:', error);
                                return { success: false, documents: null };
                            }),
                        new Promise(resolve => setTimeout(() => resolve({ success: false, documents: null }), timeout))
                    ]);
                    promises.push(documentsPromise);
                } else {
                    promises.push(Promise.resolve({ success: false, documents: null }));
                }

                // Wait for all promises to resolve
                const [provinceData, districtData, subdistrictData, villageData, jobData, documentsData] = await Promise.all(promises);

                // Kembalikan konten asli modal
                modalBody.innerHTML = originalContent;

                // Set values dengan konversi
                document.getElementById('detailGender').innerText = genderMap[biodata.gender] || biodata.gender || '-';
                document.getElementById('detailCitizenStatus').innerText = citizenStatusMap[biodata.citizen_status] || biodata.citizen_status || '-';
                document.getElementById('detailBloodType').innerText = bloodTypeMap[biodata.blood_type] || biodata.blood_type || '-';
                document.getElementById('detailReligion').innerText = religionMap[biodata.religion] || biodata.religion || '-';
                document.getElementById('detailEducationStatus').innerText = educationStatusMap[biodata.education_status] || biodata.education_status || '-';
                document.getElementById('detailFamilyStatus').innerText = familyStatusMap[biodata.family_status] || biodata.family_status || '-';

                // Improved format date function
                const formatDate = (dateStr) => {
                    if (!dateStr || dateStr === " " || dateStr === "null") return '-';
                    try {
                        let date;
                        if (/^\d{2}\/\d{2}\/\d{4}$/.test(dateStr)) {
                            const parts = dateStr.split('/');
                            date = new Date(parseInt(parts[2]), parseInt(parts[1]) - 1, parseInt(parts[0]));
                        } else if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
                            const parts = dateStr.split('-');
                            date = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                        } else {
                            date = new Date(dateStr);
                        }
                        if (isNaN(date.getTime())) return '-';
                        return date.toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        });
                    } catch (error) {
                        return '-';
                    }
                };

                // Set nilai-nilai lainnya
                document.getElementById('detailNIK').innerText = biodata.nik || '-';
                document.getElementById('detailKK').innerText = biodata.kk || '-';
                document.getElementById('detailFullName').innerText = biodata.full_name || '-';
                document.getElementById('detailBirthDate').innerText = formatDate(biodata.birth_date);
                document.getElementById('detailAge').innerText = biodata.age ? `${biodata.age} Tahun` : '-';
                document.getElementById('detailBirthPlace').innerText = biodata.birth_place || '-';

                // Set basic address data
                document.getElementById('detailAddress').innerText = biodata.address || '-';
                document.getElementById('detailRT').innerText = biodata.rt || '-';
                document.getElementById('detailRW').innerText = biodata.rw || '-';
                document.getElementById('detailPostalCode').innerText = biodata.postal_code || '-';

                // Set data orangtua
                document.getElementById('detailFather').innerText = biodata.father || '-';
                document.getElementById('detailNikFather').innerText = biodata.nik_father || '-';
                document.getElementById('detailMother').innerText = biodata.mother || '-';
                document.getElementById('detailNikMother').innerText = biodata.nik_mother || '-';

                // Set location data
                document.getElementById('detailProvinceId').innerText = provinceData ? provinceData.name : (biodata.province_id || '-');
                document.getElementById('detailDistrictId').innerText = districtData ? districtData.name : (biodata.district_id || '-');
                document.getElementById('detailSubDistrictId').innerText = subdistrictData ? subdistrictData.name : (biodata.sub_district_id || '-');
                document.getElementById('detailVillageId').innerText = villageData ? villageData.name : (biodata.village_id || '-');

                // Set job data
                document.getElementById('detailJobName').innerText = jobData ? jobData.name : (biodata.job_name || '-');

                // Set dokumen
                if (documentsData.success && documentsData.documents) {
                    loadDocument('detailFotoDiri', documentsData.documents.foto_diri, 'Foto Diri');
                    loadDocument('detailFotoKtp', documentsData.documents.foto_ktp, 'Foto KTP');
                    loadDocument('detailFotoAkta', documentsData.documents.foto_akta, 'Akta Kelahiran');
                    loadDocument('detailIjazah', documentsData.documents.ijazah, 'Ijazah');
                    loadDocument('detailFotoKk', documentsData.documents.foto_kk, 'Foto KK');
                    loadDocument('detailFotoRumah', documentsData.documents.foto_rumah, 'Foto Rumah');
                } else {
                    setDocumentNotAvailable('detailFotoDiri');
                    setDocumentNotAvailable('detailFotoKtp');
                    setDocumentNotAvailable('detailFotoAkta');
                    setDocumentNotAvailable('detailIjazah');
                    setDocumentNotAvailable('detailFotoKk');
                    setDocumentNotAvailable('detailFotoRumah');
                }

                // Foto utama (foto diri di atas)
                if (biodata.nik) {
                    const photoDiv = document.getElementById('detailPhoto');
                    const photoStatus = document.getElementById('photoStatus');
                    photoStatus.textContent = 'Memuat foto...';
                    photoDiv.innerHTML = `<div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-[#7886C7]"></div>`;
                    if (documentsData.success && documentsData.documents && documentsData.documents.foto_diri && documentsData.documents.foto_diri.preview_url) {
                        photoDiv.innerHTML = `<img src="${documentsData.documents.foto_diri.preview_url}" alt="Foto Diri" class="w-32 h-32 rounded-full object-cover">`;
                        photoStatus.textContent = '';
                    } else {
                        photoDiv.innerHTML = `<svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>`;
                        photoStatus.textContent = 'Foto belum diunggah';
                    }
                }

                // Muat Data Anggota Keluarga (berdasarkan KK)
                if (biodata.kk) {
                    loadFamilyMembersByKK(biodata.kk);
                } else {
                    setFamilyMembersEmpty('Nomor KK tidak tersedia');
                }

                // Muat data lain berbasis NIK secara paralel
                if (biodata.nik) {
                    loadAllRelatedByNik(biodata.nik);
                }

            } catch (error) {
                modalBody.innerHTML = originalContent;
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Data',
                    text: 'Terjadi kesalahan saat memuat detail. Silahkan coba lagi.',
                    timer: 3000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end',
                });
            }
        }

        // Pastikan closeDetailModal global
        window.closeDetailModal = function() {
            document.getElementById('detailModal').classList.add('hidden');
        };

        // Function untuk memuat dokumen
        function loadDocument(elementId, docInfo, docType) {
            const element = document.getElementById(elementId);

            if (docInfo && docInfo.exists) {
                if (docInfo.preview_url) {
                    // Jika ada preview URL (gambar)
                    element.innerHTML = `
                        <a href="/admin/family-member/${currentNIK}/document/${docType.toLowerCase().replace(/\s+/g, '_')}/view"
                           target="_blank" class="block">
                            <img src="${docInfo.preview_url}"
                                 alt="${docType}"
                                 class="h-24 w-full object-cover rounded-lg border border-gray-200 hover:opacity-75 transition-opacity">
                        </a>
                        <span class="text-xs text-green-600 mt-1 block">✓ Tersedia</span>
                        <span class="text-xs text-gray-500">${new Date(docInfo.updated_at).toLocaleDateString('id-ID')}</span>
                    `;
                } else if (docInfo.extension && ['pdf'].includes(docInfo.extension.toLowerCase())) {
                    // Jika PDF
                    element.innerHTML = `
                        <a href="/admin/family-member/${currentNIK}/document/${docType.toLowerCase().replace(/\s+/g, '_')}/view"
                           target="_blank" class="block">
                            <div class="h-24 w-full bg-red-100 rounded-lg border border-red-200 flex items-center justify-center hover:bg-red-200 transition-colors">
                                <div class="text-center">
                                    <svg class="w-8 h-8 mx-auto text-red-500 mb-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-xs text-red-600 font-medium">PDF</span>
                                </div>
                            </div>
                        </a>
                        <span class="text-xs text-green-600 mt-1 block">✓ Tersedia</span>
                        <span class="text-xs text-gray-500">${new Date(docInfo.updated_at).toLocaleDateString('id-ID')}</span>
                    `;
                }
            } else {
                setDocumentNotAvailable(elementId);
            }
        }

        // Function untuk menandai dokumen tidak tersedia
        function setDocumentNotAvailable(elementId) {
            const element = document.getElementById(elementId);
            element.innerHTML = `
                <div class="h-24 w-full bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center">
                    <div class="text-center">
                        <svg class="w-8 h-8 mx-auto text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="text-xs text-gray-500">Belum diunggah</span>
                    </div>
                </div>
                <span class="text-xs text-gray-400 mt-1 block">-</span>
            `;
        }

        // ====== Anggota Keluarga ======
        function setFamilyMembersEmpty(reason = '') {
            const container = document.getElementById('familyMembersSection');
            container.innerHTML = `
                <div class="text-sm text-gray-500">Tidak ada data anggota keluarga${reason ? ` (${reason})` : ''}.</div>
            `;
        }

        function renderFamilyMembersTable(members) {
            if (!Array.isArray(members) || members.length === 0) {
                setFamilyMembersEmpty();
                return;
            }

            const rows = members.map((m, i) => `
                <tr class="border-b">
                    <td class="px-3 py-2 text-gray-700">${i + 1}</td>
                    <td class="px-3 py-2 font-medium text-gray-900">${m.full_name || '-'}</td>
                    <td class="px-3 py-2 text-gray-700">${m.nik || '-'}</td>
                    <td class="px-3 py-2 text-gray-700">${m.family_status || '-'}</td>
                </tr>
            `).join('');

            const html = `
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs uppercase bg-[#e6e8ed] text-gray-700">
                            <tr>
                                <th class="px-3 py-2">No</th>
                                <th class="px-3 py-2">Nama</th>
                                <th class="px-3 py-2">NIK</th>
                                <th class="px-3 py-2">SHDK</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            ${rows}
                        </tbody>
                    </table>
                </div>
            `;
            document.getElementById('familyMembersSection').innerHTML = html;
        }

        function loadFamilyMembersByKK(kkNumber) {
            const container = document.getElementById('familyMembersSection');
            container.innerHTML = `
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <div class="animate-spin rounded-full h-5 w-5 border-t-2 border-b-2 border-[#7886C7]"></div>
                    <span>Memuat data anggota keluarga...</span>
                </div>
            `;

            fetch(`/getFamilyMembers?kk=${encodeURIComponent(kkNumber)}`)
                .then(r => r.json())
                .then(resp => {
                    if (resp && (resp.status === 'OK' || resp.success === true)) {
                        const data = resp.data || [];
                        renderFamilyMembersTable(data);
                    } else {
                        setFamilyMembersEmpty('tidak ditemukan');
                    }
                })
                .catch(() => setFamilyMembersEmpty('gagal memuat'));
        }

        // ====== Loader Paralel Data Terkait (by NIK) ======
        function loadAllRelatedByNik(nik) {
            const timeoutMs = 15000; // beri waktu lebih lama di produksi agar tidak time out
            const withTimeout = (p) => Promise.race([
                p, new Promise(resolve => setTimeout(() => resolve({ success: false, data: [] }), timeoutMs))
            ]);

            Promise.all([
                withTimeout(fetch(`/admin/biodata/warung-produk/${nik}`).then(r => r.json()).catch(() => ({ success:false }))),
                withTimeout(fetch(`/admin/biodata/laporan/${nik}`).then(r => r.json()).catch(() => ({ success:false }))),
                withTimeout(fetch(`/admin/biodata/domisili/${nik}`).then(r => r.json()).catch(() => ({ success:false }))),
                withTimeout(fetch(`/admin/biodata/berita/${nik}`).then(r => r.json()).catch(() => ({ success:false }))),
                withTimeout(fetch(`/admin/biodata/tagihan/${nik}`).then(r => r.json()).catch(() => ({ success:false }))),
                withTimeout(fetch(`/admin/biodata/aset/${nik}`).then(r => r.json()).catch(() => ({ success:false }))),
                withTimeout(fetch(`/admin/biodata/penduduk-location/${nik}`).then(r => r.json()).catch(() => ({ success:false }))),
                // Untuk surat, beri timeout ekstra panjang karena mengagregasi banyak tabel
                Promise.race([
                    fetch(`/admin/biodata/surat/${nik}`).then(r => r.json()).catch(() => ({ success:false })),
                    new Promise(resolve => setTimeout(() => resolve({ success:false, data: [] }), 20000))
                ])
            ]).then(([warungRes, laporanRes, domisiliRes, beritaRes, tagihanRes, asetRes, lokasiRes, lettersRes]) => {
                renderWarungProduk(warungRes);
                renderLaporan(laporanRes);
                renderDomisili(domisiliRes);
                renderBerita(beritaRes);
                renderTagihan(tagihanRes);
                renderAset(asetRes);
                renderDomisiliMap(lokasiRes);
                renderLetters(lettersRes);
            });
        }

        // ====== Renderers ======
        function renderWarungProduk(resp) {
            const el = document.getElementById('warungProdukSection');
            const info = (resp && resp.success) ? (resp.informasi_usaha || null) : null;
            const items = (resp && resp.success && Array.isArray(resp.data)) ? resp.data : [];

            // Kartu informasi usaha (jika ada)
            const infoHtml = info ? `
                ${info.foto_url ? `
                <div class="mb-3">
                    <div class="text-sm text-gray-500 mb-1">Foto Usaha</div>
                    <img src="${info.foto_url}" alt="Foto Usaha" class="w-32 h-32 object-cover rounded border" />
                </div>` : ''}
                <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-3 text-gray-700">
                    <div><span class="text-gray-500 text-xs">Nama Usaha</span><div class="font-medium">${info.nama_usaha || '-'}</div></div>
                    <div><span class="text-gray-500 text-xs">Nomor KK</span><div class="font-medium">${info.kk || '-'}</div></div>
                    <div class="md:col-span-2"><span class="text-gray-500 text-xs">Alamat</span><div class="font-medium">${info.alamat || '-'}</div></div>
                    <div><span class="text-gray-500 text-xs">Kelompok Usaha</span><div class="font-medium">${info.kelompok_usaha || '-'}</div></div>
                    <div><span class="text-gray-500 text-xs">Tag Lokasi</span><div class="font-medium">${info.tag_lokasi || '-'}</div></div>
                    <div><span class="text-gray-500 text-xs">Pemilik (NIK)</span><div class="font-medium">${(info.pemilik && info.pemilik.nik) ? info.pemilik.nik : '-'}</div></div>
                    <div><span class="text-gray-500 text-xs">Pemilik (Nama)</span><div class="font-medium">${(info.pemilik && info.pemilik.nama) ? info.pemilik.nama : '-'}</div></div>
                </div>
            ` : '';

            if (items.length === 0) {
                el.innerHTML = infoHtml + '<span class="text-gray-500">Tidak ada data warung.</span>';
                return;
            }

            // Format harga ke Rupiah
            const formatRupiah = (harga) => {
                if (!harga || harga === '-') return '-';
                return 'Rp ' + Number(harga).toLocaleString('id-ID');
            };
            
            const rows = items.map((it, i) => `
                <tr class="border-b">
                    <td class="px-3 py-2">${i + 1}</td>
                    <td class="px-3 py-2">${it.foto_url ? `<img src=\"${it.foto_url}\" alt=\"Foto Produk\" class=\"h-12 w-12 object-cover rounded border\" />` : '-'}</td>
                    <td class="px-3 py-2 font-medium">${it.nama_produk || '-'}</td>
                    <td class="px-3 py-2">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${
                            it.klasifikasi === 'barang' ? 'bg-blue-100 text-blue-800' :
                            it.klasifikasi === 'jasa' ? 'bg-green-100 text-green-800' :
                            'bg-gray-100 text-gray-800'
                        }">
                            ${it.klasifikasi === 'barang' ? 'Barang' :
                              it.klasifikasi === 'jasa' ? 'Jasa' :
                              it.klasifikasi || '-'}
                        </span>
                    </td>
                    <td class="px-3 py-2">${it.jenis || '-'}</td>
                    <td class="px-3 py-2 font-medium">${formatRupiah(it.harga)}</td>
                    <td class="px-3 py-2">${it.stok ?? '-'}</td>
                </tr>
            `).join('');

            el.innerHTML = `
                ${infoHtml}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs uppercase bg-[#e6e8ed] text-gray-700">
                            <tr>
                                <th class="px-3 py-2">No</th>
                                <th class="px-3 py-2">Foto</th>
                                <th class="px-3 py-2">Produk</th>
                                <th class="px-3 py-2">Klasifikasi</th>
                                <th class="px-3 py-2">Jenis</th>
                                <th class="px-3 py-2">Harga</th>
                                <th class="px-3 py-2">Stok</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">${rows}</tbody>
                    </table>
                </div>`;
        }

        function renderLaporan(resp) {
            const el = document.getElementById('laporanInputSection');
            const items = (resp && resp.success && Array.isArray(resp.data)) ? resp.data : [];
            if (items.length === 0) { el.innerHTML = '<span class="text-gray-500">Tidak ada laporan.</span>'; return; }
            const rows = items.map((it, i) => `
                <tr class="border-b">
                    <td class="px-3 py-2">${i + 1}</td>
                    <td class="px-3 py-2 font-medium">${it.judul_laporan || '-'}</td>
                    <td class="px-3 py-2">${it.status || '-'}</td>
                    <td class="px-3 py-2">${(it.created_at || '').toString().substring(0,10)}</td>
                </tr>
            `).join('');
            el.innerHTML = `
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs uppercase bg-[#e6e8ed] text-gray-700">
                            <tr>
                                <th class="px-3 py-2">No</th>
                                <th class="px-3 py-2">Judul</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">${rows}</tbody>
                    </table>
                </div>`;
        }

        function renderDomisili(resp) {
            const el = document.getElementById('domisiliSection');
            const dom = (resp && resp.success && Array.isArray(resp.domisili)) ? resp.domisili : [];
            const domUsaha = (resp && resp.success && Array.isArray(resp.domisili_usaha)) ? resp.domisili_usaha : [];
            if (dom.length === 0 && domUsaha.length === 0) { el.innerHTML = ''; return; }
            const rows1 = dom.map((it, i) => `
                <tr class="border-b"><td class="px-3 py-2">${i + 1}</td><td class="px-3 py-2">${it.full_name || '-'}</td><td class="px-3 py-2">Domisili</td><td class="px-3 py-2">${(it.created_at||'').toString().substring(0,10)}</td></tr>
            `).join('');
            const rows2 = domUsaha.map((it, i) => `
                <tr class="border-b"><td class="px-3 py-2">${dom.length + i + 1}</td><td class="px-3 py-2">${it.full_name || '-'}</td><td class="px-3 py-2">Domisili Usaha</td><td class="px-3 py-2">${(it.created_at||'').toString().substring(0,10)}</td></tr>
            `).join('');
            el.innerHTML = `
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs uppercase bg-[#e6e8ed] text-gray-700">
                            <tr>
                                <th class="px-3 py-2">No</th>
                                <th class="px-3 py-2">Nama</th>
                                <th class="px-3 py-2">Jenis</th>
                                <th class="px-3 py-2">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">${rows1}${rows2}</tbody>
                    </table>
                </div>`;
        }

        // Peta domisili readonly dari tabel penduduk (tag_lokasi + alamat)
        function renderDomisiliMap(resp) {
            try {
                const data = resp && resp.success ? resp.data : null;
                const mapEl = document.getElementById('domisiliMap');
                const alamatEl = document.getElementById('domisiliAlamat');
                const coordEl = document.getElementById('domisiliKoordinat');
                if (!mapEl) return;

                // Pastikan Leaflet termuat di produksi: jika belum, coba muat dari CDN alternatif
                if (!window.L) {
                    const loadLeaflet = () => new Promise((resolve, reject) => {
                        const existing = document.querySelector('script[data-leaflet-loader]');
                        if (existing) {
                            existing.addEventListener('load', () => resolve());
                            existing.addEventListener('error', () => reject());
                            return;
                        }
                        const linkCss = document.createElement('link');
                        linkCss.rel = 'stylesheet';
                        linkCss.href = 'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css';
                        document.head.appendChild(linkCss);

                        const script = document.createElement('script');
                        script.src = 'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js';
                        script.async = true;
                        script.setAttribute('data-leaflet-loader', '1');
                        script.onload = () => resolve();
                        script.onerror = () => reject();
                        document.head.appendChild(script);
                    });

                    loadLeaflet()
                        .then(() => initMapDom(data))
                        .catch(() => {
                            // Fallback terakhir: render peta via iframe OSM supaya tetap terlihat di produksi
                            try {
                                let lat = -6.1753924;
                                let lng = 106.8271528;
                                let alamat = '-';
                                if (data && data.tag_lokasi) {
                                    const parts = data.tag_lokasi.split(',').map(function (v) { return parseFloat(v.trim()); });
                                    if (parts.length === 2 && !isNaN(parts[0]) && !isNaN(parts[1])) { lat = parts[0]; lng = parts[1]; }
                                }
                                if (data && data.alamat) alamat = data.alamat;
                                const delta = 0.01;
                                const bbox = [lng - delta, lat - delta, lng + delta, lat + delta].join(',');
                                mapEl.innerHTML = `<iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
                                    src="https://www.openstreetmap.org/export/embed.html?bbox=${bbox}&layer=mapnik&marker=${lat},${lng}"></iframe>`;
                                if (alamatEl) alamatEl.textContent = alamat || '-';
                                if (coordEl) coordEl.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                            } catch (_) {}
                        });
                } else {
                    initMapDom(data);
                }

                function initMapDom(d) {
                    let lat = -6.1753924; // default Monas
                    let lng = 106.8271528;
                    let alamat = '-';
                    if (d && d.tag_lokasi) {
                        const parts = d.tag_lokasi.split(',').map(function (v) { return parseFloat(v.trim()); });
                        if (parts.length === 2 && !isNaN(parts[0]) && !isNaN(parts[1])) {
                            lat = parts[0];
                            lng = parts[1];
                        }
                    }
                    if (d && d.alamat) alamat = d.alamat;

                    // Reset container jika peta sudah ada
                    if (mapEl._leaflet_id) {
                        mapEl.replaceWith(mapEl.cloneNode(true));
                    }
                    const mapTarget = document.getElementById('domisiliMap');
                    const map = L.map(mapTarget, { zoomControl: true, scrollWheelZoom: false, dragging: true }).setView([lat, lng], 15);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(map);
                    L.marker([lat, lng], { draggable: false, keyboard: false }).addTo(map);

                    // Perbaiki layout saat container awalnya tersembunyi (mis. di dalam modal)
                    setTimeout(function () { try { map.invalidateSize(true); } catch (e) {} }, 200);
                    setTimeout(function () { try { map.invalidateSize(true); } catch (e) {} }, 600);
                    setTimeout(function () { try { map.invalidateSize(true); } catch (e) {} }, 1200);

                    if (alamatEl) alamatEl.textContent = alamat || '-';
                    if (coordEl) coordEl.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                }
            } catch (e) {
                // abaikan kesalahan rendering peta
            }
        }

        function renderBerita(resp) {
            const el = document.getElementById('beritaInputSection');
            const items = (resp && resp.success && Array.isArray(resp.data)) ? resp.data : [];
            if (items.length === 0) { el.innerHTML = '<span class="text-gray-500">Tidak ada berita.</span>'; return; }
            
            // Mapping status berita
            const statusMap = {
                'published': 'Dipublikasi',
                'draft': 'Draft',
                'archived': 'Diarsipkan'
            };
            
            const rows = items.map((it, i) => `
                <tr class="border-b">
                    <td class="px-3 py-2">${i + 1}</td>
                    <td class="px-3 py-2 font-medium">${it.judul || '-'}</td>
                    <td class="px-3 py-2">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${
                            it.status === 'published' ? 'bg-green-100 text-green-800' :
                            it.status === 'draft' ? 'bg-yellow-100 text-yellow-800' :
                            it.status === 'archived' ? 'bg-gray-100 text-gray-800' :
                            'bg-gray-100 text-gray-800'
                        }">
                            ${statusMap[it.status] || it.status || '-'}
                        </span>
                    </td>
                    <td class="px-3 py-2">${(it.created_at||'').toString().substring(0,10)}</td>
                </tr>
            `).join('');
            el.innerHTML = `
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs uppercase bg-[#e6e8ed] text-gray-700">
                            <tr>
                                <th class="px-3 py-2">No</th>
                                <th class="px-3 py-2">Judul</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">${rows}</tbody>
                    </table>
                </div>`;
        }

        function renderTagihan(resp) {
            const el = document.getElementById('tagihanSection');
            const items = (resp && resp.success && Array.isArray(resp.data)) ? resp.data : [];
            if (items.length === 0) { el.innerHTML = '<span class="text-gray-500">Tidak ada tagihan.</span>'; return; }
            
            // Format nominal ke Rupiah
            const formatRupiah = (nominal) => {
                if (!nominal || nominal === '-') return '-';
                return 'Rp ' + Number(nominal).toLocaleString('id-ID');
            };
            
            const rows = items.map((it, i) => `
                <tr class="border-b">
                    <td class="px-3 py-2">${i + 1}</td>
                    <td class="px-3 py-2">${it.kategori || '-'}</td>
                    <td class="px-3 py-2">${it.sub_kategori || '-'}</td>
                    <td class="px-3 py-2 font-medium">${formatRupiah(it.nominal)}</td>
                    <td class="px-3 py-2">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${
                            it.status === 'lunas' ? 'bg-green-100 text-green-800' :
                            it.status === 'belum_lunas' ? 'bg-red-100 text-red-800' :
                            it.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                            'bg-gray-100 text-gray-800'
                        }">
                            ${it.status === 'lunas' ? 'Lunas' :
                              it.status === 'belum_lunas' ? 'Belum Lunas' :
                              it.status === 'pending' ? 'Pending' :
                              it.status || '-'}
                        </span>
                    </td>
                    <td class="px-3 py-2">${it.tanggal || '-'}</td>
                </tr>
            `).join('');
            el.innerHTML = `
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs uppercase bg-[#e6e8ed] text-gray-700">
                            <tr>
                                <th class="px-3 py-2">No</th>
                                <th class="px-3 py-2">Kategori</th>
                                <th class="px-3 py-2">Sub Kategori</th>
                                <th class="px-3 py-2">Nominal</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">${rows}</tbody>
                    </table>
                </div>`;
        }

        function renderAset(resp) {
            const el = document.getElementById('asetSection');
            const items = (resp && resp.success && Array.isArray(resp.data)) ? resp.data : [];
            if (items.length === 0) { el.innerHTML = '<span class="text-gray-500">Tidak ada aset.</span>'; return; }
            const rows = items.map((it, i) => `
                <tr class="border-b">
                    <td class="px-3 py-2">${i + 1}</td>
                    <td class="px-3 py-2 font-medium">${it.nama_aset || '-'}</td>
                    <td class="px-3 py-2">${it.nik_pemilik || '-'}</td>
                    <td class="px-3 py-2">${it.nama_pemilik || '-'}</td>
                    <td class="px-3 py-2">${it.alamat || '-'}</td>
                    <td class="px-3 py-2">${it.klasifikasi || '-'}</td>
                    <td class="px-3 py-2">${it.jenis_aset || '-'}</td>
                    <td class="px-3 py-2">
                        <div class="flex space-x-2">
                            ${it.foto_aset_depan ? `<img src="${it.foto_aset_depan}" alt="Foto Depan" class="w-12 h-12 rounded object-cover cursor-pointer" onclick="showImageModal('${it.foto_aset_depan}', 'Foto Depan Aset')">` : '<span class="text-gray-400">-</span>'}
                            ${it.foto_aset_samping ? `<img src="${it.foto_aset_samping}" alt="Foto Samping" class="w-12 h-12 rounded object-cover cursor-pointer" onclick="showImageModal('${it.foto_aset_samping}', 'Foto Samping Aset')">` : '<span class="text-gray-400">-</span>'}
                        </div>
                    </td>
                </tr>
            `).join('');
            el.innerHTML = `
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs uppercase bg-[#e6e8ed] text-gray-700">
                            <tr>
                                <th class="px-3 py-2">No</th>
                                <th class="px-3 py-2">Nama Aset</th>
                                <th class="px-3 py-2">NIK Pemilik</th>
                                <th class="px-3 py-2">Nama Pemilik</th>
                                <th class="px-3 py-2">Alamat</th>
                                <th class="px-3 py-2">Klasifikasi</th>
                                <th class="px-3 py-2">Jenis Aset</th>
                                <th class="px-3 py-2">Foto</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">${rows}</tbody>
                    </table>
                </div>`;
        }

        function renderLetters(resp) {
            const el = document.getElementById('suratInputSection');
            const items = (resp && resp.success && Array.isArray(resp.data)) ? resp.data : [];
            if (!el) return;
            if (items.length === 0) { el.innerHTML = '<span class="text-gray-500">Tidak ada riwayat surat.</span>'; return; }

            const rows = items.map((it, i) => `
                <tr class="border-b">
                    <td class="px-3 py-2">${i + 1}</td>
                    <td class="px-3 py-2">${it.type_label || '-'}</td>
                    <td class="px-3 py-2">${it.full_name || '-'}</td>
                    <td class="px-3 py-2">${it.nik || '-'}</td>
                    <td class="px-3 py-2">${it.purpose || '-'}</td>
                    <td class="px-3 py-2">${it.letter_date || '-'}</td>
                    <td class="px-3 py-2">${it.is_accepted === null || it.is_accepted === undefined ? '-' : (it.is_accepted ? 'Diterima' : 'Menunggu')}</td>
                </tr>
            `).join('');

            el.innerHTML = `
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs uppercase bg-[#e6e8ed] text-gray-700">
                            <tr>
                                <th class="px-3 py-2">No</th>
                                <th class="px-3 py-2">Jenis Surat</th>
                                <th class="px-3 py-2">Nama</th>
                                <th class="px-3 py-2">NIK</th>
                                <th class="px-3 py-2">Keperluan</th>
                                <th class="px-3 py-2">Tanggal</th>
                                <th class="px-3 py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">${rows}</tbody>
                    </table>
                </div>`;
        }

        // Fungsi untuk menampilkan foto dalam modal
        function showImageModal(imageSrc, title) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            modal.innerHTML = `
                <div class="bg-white rounded-lg p-6 max-w-4xl max-h-full overflow-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">${title}</h3>
                        <button onclick="this.closest('.fixed').remove()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                    </div>
                    <img src="${imageSrc}" alt="${title}" class="max-w-full max-h-96 mx-auto rounded">
                </div>
            `;
            document.body.appendChild(modal);
            
            // Tutup modal saat klik di luar gambar
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.remove();
                }
            });
        }
    </script>

    <!-- Script untuk memuat foto di tabel -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Load foto untuk setiap baris di tabel
            @foreach($citizens['data']['citizens'] ?? [] as $citizen)
                @if(isset($citizen['nik']))
                    loadPhoto('{{ $citizen['nik'] }}');
                @endif
            @endforeach
        });

        function loadPhoto(nik) {
            fetch(`/admin/family-member/${nik}/documents`)
                .then(response => response.json())
                .then(data => {
                    const photoDiv = document.getElementById(`photo-${nik}`);
                    if (photoDiv && data.success && data.documents && data.documents.foto_diri && data.documents.foto_diri.preview_url) {
                        photoDiv.innerHTML = `<img src="${data.documents.foto_diri.preview_url}" alt="Foto Diri" class="w-10 h-10 rounded-full object-cover">`;
                    }
                })
                .catch(error => {
                    console.error('Error loading photo for NIK:', nik, error);
                });
        }
    </script>

    <style>
        /* Custom styles for SweetAlert toasts */
        .colored-toast.swal2-icon-success {
            background-color: #f0fdf4 !important;
            border-left: 4px solid #28a745 !important;
        }

        .colored-toast.swal2-icon-error {
            background-color: #fef2f2 !important;
            border-left: 4px solid #dc3545 !important;
        }

        .colored-toast.swal2-icon-warning {
            background-color: #fffbeb !important;
            border-left: 4px solid #ffc107 !important;
        }

        .colored-toast .swal2-title {
            color: #333 !important;
        }

        /* Tambahkan link ke animate.css jika belum ada */
        @import url('https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css');
    </style>
</x-layout>

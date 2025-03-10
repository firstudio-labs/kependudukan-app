<x-layout>
    <div class="p-4 mt-14">
        <!-- Alert Sukses -->
        {{-- @if(session('success'))
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
        @endif --}}

        <!-- Judul H1 -->
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Data KK</h1>

        <!-- Bar untuk Search dan Tambah Pasien -->
        <div class="flex justify-between items-center mb-4">
            <!-- Input Pencarian -->
            <form method="GET" action="" class="relative">
                <input
                    type="text"
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
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
                onclick="window.location.href='{{ route('superadmin.datakk.create') }}'"
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
                        <th scope="col" class="px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">No KK</th>
                        <th scope="col" class="px-6 py-3">Nama Lengkap</th>
                        <th scope="col" class="px-6 py-3">Alamat</th>
                        <th scope="col" class="px-6 py-3">Jumlah Anggota Keluarga</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kk as $k)
                    <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $k->id }}</th>
                        <td class="px-6 py-4">{{ $k->kk }}</td>
                        <td class="px-6 py-4">{{ $k->full_name }}</td>
                        <td class="px-6 py-4">{{ $k->address }}</td>
                        <td class="px-6 py-4">{{ $k->jml_anggota_kk }}</td>
                        <td class="flex items-center px-6 py-4 space-x-2">
                            <button onclick="showDetailModal({{ $k->toJson() }})" class="text-blue-600 hover:text-blue-800" aria-label="Detail">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            <a href="{{ route('superadmin.datakk.update', $k->id) }}" class="text-yellow-600 hover:text-yellow-800" aria-label="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('superadmin.destroy', $k->id) }}" method="POST" class="delete-form" id="delete-form-{{ $k->id }}">
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
                        <td colspan="6" class="text-center py-4">Tidak ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {!! $kk->links() !!}
            </div>
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
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-[#7886C7] bg-gray-50">
                    <h3 class="text-xl font-semibold text-[#2D336B]">Detail Data KK</h3>
                    <button onclick="closeDetailModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center">
                        <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-4 md:p-5 overflow-y-auto max-h-[70vh]">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Informasi KK -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold mb-4 text-[#7886C7]">Informasi KK</h4>
                            <div class="space-y-3">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Nomor KK</span>
                                    <span id="detailKK" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Nama Lengkap</span>
                                    <span id="detailFullName" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Jumlah Anggota KK</span>
                                    <span id="detailJmlAnggota" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Email</span>
                                    <span id="detailEmail" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Telepon</span>
                                    <span id="detailTelepon" class="font-medium"></span>
                                </div>
                            </div>
                        </div>



                        <!-- Informasi Alamat -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold mb-4 text-[#7886C7]">Informasi Alamat</h4>
                            <div class="space-y-3">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Alamat</span>
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
                                    <span class="text-sm text-gray-500">Dusun</span>
                                    <span id="detailDusun" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Provinsi</span>
                                    <span id="detailProvinceId" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Kabupaten/Kota</span>
                                    <span id="detailDistrictId" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Kecamatan</span>
                                    <span id="detailSubDistrictId" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Desa/Kelurahan</span>
                                    <span id="detailVillageId" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Kode Pos</span>
                                    <span id="detailPostalCode" class="font-medium"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Luar Negeri (jika ada) -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold mb-4 text-[#7886C7]">Informasi Luar Negeri</h4>
                            <div class="space-y-3">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Alamat Luar Negeri</span>
                                    <span id="detailAlamatLuarNegeri" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Kota</span>
                                    <span id="detailKota" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Negara Bagian</span>
                                    <span id="detailNegaraBagian" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Negara</span>
                                    <span id="detailNegara" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Kode Pos Luar Negeri</span>
                                    <span id="detailKodePosLuarNegeri" class="font-medium"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-1 md:col-span-2 bg-gray-50 p-4 rounded-lg mt-4">
                            <h4 class="text-lg font-semibold mb-4 text-[#7886C7]">Informasi Anggota Keluarga</h4>
                            <div id="familyMembersLoading" class="text-center py-4">
                                <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-[#7886C7]"></div>
                                <p class="mt-2 text-gray-600">Memuat data anggota keluarga...</p>
                            </div>
                            <div id="familyMembersError" class="hidden text-center py-4 text-red-500">
                                Gagal memuat data anggota keluarga
                            </div>
                            <div id="familyMembersEmpty" class="hidden text-center py-4 text-gray-500">
                                Tidak ada data anggota keluarga
                            </div>
                            <div id="familyMembersContainer" class="hidden">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead>
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Keluarga</th>
                                            </tr>
                                        </thead>
                                        <tbody id="familyMembersTable" class="bg-white divide-y divide-gray-200">
                                            <!-- Family members will be inserted here dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200 rounded-b">
                    <button onclick="closeDetailModal()" type="button" class="text-white bg-gray-500 hover:bg-gray-600 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg text-sm px-5 py-2.5 text-center">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        document.addEventListener('DOMContentLoaded', function() {
        // Attach event listeners to all delete forms
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2D336B',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    });

        function closeAlert() {
            document.getElementById('success-alert')?.classList.add('opacity-0');
            document.getElementById('error-alert')?.classList.add('opacity-0');
            setTimeout(() => {
                document.getElementById('success-alert')?.remove();
                document.getElementById('error-alert')?.remove();
            }, 500);
        }
        setTimeout(closeAlert, 4000); // Auto-close dalam 4 detik

        function showDetailModal(data) {
            // Set values to modal
            document.getElementById('detailKK').innerText = data.kk || '-';
            document.getElementById('detailFullName').innerText = data.full_name || '-';
            document.getElementById('detailJmlAnggota').innerText = data.jml_anggota_kk || '-';
            document.getElementById('detailEmail').innerText = data.email || '-';
            document.getElementById('detailTelepon').innerText = data.telepon || '-';
            document.getElementById('detailAddress').innerText = data.address || '-';
            document.getElementById('detailRT').innerText = data.rt || '-';
            document.getElementById('detailRW').innerText = data.rw || '-';
            document.getElementById('detailDusun').innerText = data.dusun || '-';
            document.getElementById('detailPostalCode').innerText = data.postal_code || '-';

            // Set location names instead of IDs
            document.getElementById('detailProvinceId').innerText = data.province_name || data.province_id || '-';
            document.getElementById('detailDistrictId').innerText = data.district_name || data.district_id || '-';
            document.getElementById('detailSubDistrictId').innerText = data.sub_district_name || data.sub_district_id || '-';
            document.getElementById('detailVillageId').innerText = data.village_name || data.village_id || '-';

            document.getElementById('detailAlamatLuarNegeri').innerText = data.alamat_luar_negeri || '-';
            document.getElementById('detailKota').innerText = data.kota || '-';
            document.getElementById('detailNegaraBagian').innerText = data.negara_bagian || '-';
            document.getElementById('detailNegara').innerText = data.negara || '-';
            document.getElementById('detailKodePosLuarNegeri').innerText = data.kode_pos_luar_negeri || '-';

            document.getElementById('detailModal').classList.remove('hidden');

            // Reset family members section
            document.getElementById('familyMembersLoading').classList.remove('hidden');
            document.getElementById('familyMembersError').classList.add('hidden');
            document.getElementById('familyMembersEmpty').classList.add('hidden');
            document.getElementById('familyMembersContainer').classList.add('hidden');
            document.getElementById('familyMembersTable').innerHTML = '';

            // Fetch family members
            fetchFamilyMembers(data.id);
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        function fetchFamilyMembers(kkId) {
    axios.get(`/superadmin/datakk/${kkId}/family-members`)
        .then(response => {
            document.getElementById('familyMembersLoading').classList.add('hidden');

            if (response.data.status === 'OK' && response.data.data && response.data.data.length > 0) {
                const members = response.data.data;
                const tableBody = document.getElementById('familyMembersTable');
                tableBody.innerHTML = '';

                members.forEach((member, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-4 py-2 whitespace-nowrap">${index + 1}</td>
                        <td class="px-4 py-2 whitespace-nowrap">${member.full_name || '-'}</td>
                        <td class="px-4 py-2 whitespace-nowrap">${member.family_status || '-'}</td>
                    `;
                    tableBody.appendChild(row);
                });

                document.getElementById('familyMembersContainer').classList.remove('hidden');
            } else {
                document.getElementById('familyMembersEmpty').classList.remove('hidden');
            }
        })
        .catch(error => {
            document.getElementById('familyMembersLoading').classList.add('hidden');
            document.getElementById('familyMembersError').classList.remove('hidden');
            console.error('Error fetching family members:', error);
        });
}


    </script>
</x-layout>

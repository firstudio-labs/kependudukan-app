<x-layout>
    <div class="p-4 mt-14">
        <!-- Alert Sukses -->
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
        @endif

        <h1 class="text-2xl font-bold text-gray-800 mb-6">Biodata</h1>

        <div class="flex justify-between items-center mb-4">
            <form method="GET" action="" class="relative w-full max-w-xs">
                <input
                    type="text"
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari data biodata..."
                />
                <button type="submit" class="absolute top-1/2 left-2 w-5 h-5 text-gray-400 transform -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 1110.15-10.15 7.5 7.5 0 01-10.15 10.15z" />
                    </svg>
                </button>
            </form>

            <button
                type="button"
                onclick="window.location.href='{{ route('superadmin.biodata.create') }}'"
                class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-sm px-5 py-2.5 flex items-center space-x-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Data Biodata</span>
            </button>
        </div>

        <div class="relative shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">NIK</th>
                        <th class="px-6 py-3">Nama Lengkap</th>
                        <th class="px-6 py-3">Alamat</th>
                        <th class="px-6 py-3">SHDK</th>
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
                    @forelse($citizens['data']['citizens'] as $index => $citizen)
                    <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $startNumber + $index }}</th>
                        <td class="px-6 py-4">{{ $citizen['nik'] }}</td>
                        <td class="px-6 py-4">{{ $citizen['full_name'] }}</td>
                        <td class="px-6 py-4">{{ $citizen['address'] }}</td>
                        <td class="px-6 py-4">{{ $citizen['family_status'] }}</td>
                        <td class="flex items-center px-6 py-4 space-x-2">
                            <button onclick="showDetailModal({{ json_encode($citizen) }})" class="text-blue-600 hover:text-blue-800" aria-label="Detail">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            <a href="{{ route('superadmin.biodata.edit', $citizen['nik']) }}" class="text-yellow-600 hover:text-yellow-800" aria-label="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('superadmin.biodata.destroy', $citizen['nik']) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data?')">
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
            <div class="mt-4 flex justify-between items-center mb-6">
                <div class="text-sm text-gray-600 ml-2">
                    Showing {{ $startNumber }} to {{ $endNumber }} of {{ $totalItems }} results
                </div>
                @if(isset($citizens['data']['pagination']) && $citizens['data']['pagination']['total_page'] > 1)
                    <nav class="flex justify-between items-center mt-4 mb-4">
                        <ul class="inline-flex items-center -space-x-px">
                            @if($citizens['data']['pagination']['prev_page'])
                                <li>
                                    <a href="?page={{ $citizens['data']['pagination']['prev_page'] }}" class="px-3 py-2 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-700">
                                        Previous
                                    </a>
                                </li>
                            @endif
                            @php
                                $totalPages = $citizens['data']['pagination']['total_page'];
                                $currentPage = $citizens['data']['pagination']['current_page'];
                                $startPage = max(1, $currentPage - 5);
                                $endPage = min($totalPages, $currentPage + 4);
                            @endphp
                            @for($i = $startPage; $i <= $endPage; $i++)
                                <li>
                                    <a href="?page={{ $i }}" class="px-3 py-2 leading-tight {{ $i == $currentPage ? 'text-blue-600 bg-blue-50 border border-blue-300' : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700' }}">
                                        {{ $i }}
                                    </a>
                                </li>
                            @endfor
                            @if($citizens['data']['pagination']['next_page'])
                                <li>
                                    <a href="?page={{ $citizens['data']['pagination']['next_page'] }}" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 hover:text-gray-700 mr-2">
                                        Next
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </nav>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="relative w-full max-w-2xl p-6 bg-white rounded-lg shadow-lg">
                <div class="flex justify-between items-center pb-3">
                    <h3 class="text-xl font-semibold">Detail Biodata</h3>
                    <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                        ✖
                    </button>
                </div>
                <div class="mt-4">
                    <p><strong>NIK:</strong> <span id="detailNIK"></span></p>
                    <p><strong>KK:</strong> <span id="detailKK"></span></p>
                    <p><strong>Nama Lengkap:</strong> <span id="detailFullName"></span></p>
                    <p><strong>Jenis Kelamin:</strong> <span id="detailGender"></span></p>
                    <p><strong>Tanggal Lahir:</strong> <span id="detailBirthDate"></span></p>
                    <p><strong>Umur:</strong> <span id="detailAge"></span></p>
                    <p><strong>Tempat Lahir:</strong> <span id="detailBirthPlace"></span></p>
                    <p><strong>Alamat:</strong> <span id="detailAddress"></span></p>
                    <p><strong>Provinsi:</strong> <span id="detailProvinceId"></span></p>
                    <p><strong>Kabupaten/Kota:</strong> <span id="detailDistrictId"></span></p>
                    <p><strong>Kecamatan:</strong> <span id="detailSubDistrictId"></span></p>
                    <p><strong>Desa/Kelurahan:</strong> <span id="detailVillageId"></span></p>
                    <p><strong>RT:</strong> <span id="detailRT"></span></p>
                    <p><strong>RW:</strong> <span id="detailRW"></span></p>
                    <p><strong>Kode Pos:</strong> <span id="detailPostalCode"></span></p>
                    <p><strong>Status Kewarganegaraan:</strong> <span id="detailCitizenStatus"></span></p>
                    <p><strong>Akta Kelahiran:</strong> <span id="detailBirthCertificate"></span></p>
                    <p><strong>No. Akta Kelahiran:</strong> <span id="detailBirthCertificateNo"></span></p>
                    <p><strong>Golongan Darah:</strong> <span id="detailBloodType"></span></p>
                    <p><strong>Agama:</strong> <span id="detailReligion"></span></p>
                    <p><strong>Status Perkawinan:</strong> <span id="detailMaritalStatus"></span></p>
                    <p><strong>Akta Perkawinan:</strong> <span id="detailMaritalCertificate"></span></p>
                    <p><strong>No. Akta Perkawinan:</strong> <span id="detailMaritalCertificateNo"></span></p>
                    <p><strong>Tanggal Perkawinan:</strong> <span id="detailMarriageDate"></span></p>
                    <p><strong>Akta Perceraian:</strong> <span id="detailDivorceCertificate"></span></p>
                    <p><strong>No. Akta Perceraian:</strong> <span id="detailDivorceCertificateNo"></span></p>
                    <p><strong>Tanggal Perceraian:</strong> <span id="detailDivorceCertificateDate"></span></p>
                    <p><strong>SHDK:</strong> <span id="detailFamilyStatus"></span></p>
                    <p><strong>Gangguan Jiwa:</strong> <span id="detailMentalDisorders"></span></p>
                    <p><strong>Disabilitas:</strong> <span id="detailDisabilities"></span></p>
                    <p><strong>Status Pendidikan:</strong> <span id="detailEducationStatus"></span></p>
                    <p><strong>Jenis Pekerjaan:</strong> <span id="detailJobTypeId"></span></p>
                    <p><strong>NIK Ibu:</strong> <span id="detailNikMother"></span></p>
                    <p><strong>Nama Ibu:</strong> <span id="detailMother"></span></p>
                    <p><strong>NIK Ayah:</strong> <span id="detailNikFather"></span></p>
                    <p><strong>Nama Ayah:</strong> <span id="detailFather"></span></p>
                    <p><strong>Koordinat:</strong> <span id="detailCoordinate"></span></p>
                </div>
                <div class="flex justify-end pt-4">
                    <button onclick="closeDetailModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function closeAlert() {
            document.getElementById('success-alert')?.classList.add('opacity-0');
            document.getElementById('error-alert')?.classList.add('opacity-0');
            setTimeout(() => {
                document.getElementById('success-alert')?.remove();
                document.getElementById('error-alert')?.remove();
            }, 500);
        }
        setTimeout(closeAlert, 4000); // Auto-close dalam 4 detik

        function showDetailModal(biodata) {
            document.getElementById('detailNIK').innerText = biodata.nik;
            document.getElementById('detailKK').innerText = biodata.kk;
            document.getElementById('detailFullName').innerText = biodata.full_name;
            document.getElementById('detailGender').innerText = biodata.gender;
            document.getElementById('detailBirthDate').innerText = biodata.birth_date;
            document.getElementById('detailAge').innerText = biodata.age;
            document.getElementById('detailBirthPlace').innerText = biodata.birth_place;
            document.getElementById('detailAddress').innerText = biodata.address;
            document.getElementById('detailProvinceId').innerText = biodata.province_id;
            document.getElementById('detailDistrictId').innerText = biodata.district_id;
            document.getElementById('detailSubDistrictId').innerText = biodata.sub_district_id;
            document.getElementById('detailVillageId').innerText = biodata.village_id;
            document.getElementById('detailRT').innerText = biodata.rt;
            document.getElementById('detailRW').innerText = biodata.rw;
            document.getElementById('detailPostalCode').innerText = biodata.postal_code;
            document.getElementById('detailCitizenStatus').innerText = biodata.citizen_status;
            document.getElementById('detailBirthCertificate').innerText = biodata.birth_certificate;
            document.getElementById('detailBirthCertificateNo').innerText = biodata.birth_certificate_no;
            document.getElementById('detailBloodType').innerText = biodata.blood_type;
            document.getElementById('detailReligion').innerText = biodata.religion;
            document.getElementById('detailMaritalStatus').innerText = biodata.marital_status;
            document.getElementById('detailMaritalCertificate').innerText = biodata.marital_certificate;
            document.getElementById('detailMaritalCertificateNo').innerText = biodata.marital_certificate_no;
            document.getElementById('detailMarriageDate').innerText = biodata.marriage_date;
            document.getElementById('detailDivorceCertificate').innerText = biodata.divorce_certificate;
            document.getElementById('detailDivorceCertificateNo').innerText = biodata.divorce_certificate_no;
            document.getElementById('detailDivorceCertificateDate').innerText = biodata.divorce_certificate_date;
            document.getElementById('detailFamilyStatus').innerText = biodata.family_status;
            document.getElementById('detailMentalDisorders').innerText = biodata.mental_disorders;
            document.getElementById('detailDisabilities').innerText = biodata.disabilities;
            document.getElementById('detailEducationStatus').innerText = biodata.education_status;
            document.getElementById('detailJobTypeId').innerText = biodata.job_type_id;
            document.getElementById('detailNikMother').innerText = biodata.nik_mother;
            document.getElementById('detailMother').innerText = biodata.mother;
            document.getElementById('detailNikFather').innerText = biodata.nik_father;
            document.getElementById('detailFather').innerText = biodata.father;
            document.getElementById('detailCoordinate').innerText = biodata.coordinate;
            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }
    </script>
</x-layout>

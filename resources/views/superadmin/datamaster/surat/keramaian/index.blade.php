<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Surat Izin Keramaian</h1>

        <div class="flex justify-between items-center mb-4">
            <form method="GET" action="" class="relative w-full max-w-xs">
                <input
                    type="text"
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari surat izin keramaian..."
                />
                <button type="submit" class="absolute top-1/2 left-2 w-5 h-5 text-gray-400 transform -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 1110.15-10.15 7.5 7.5 0 01-10.15 10.15z" />
                    </svg>
                </button>
            </form>

            <button
                type="button"
                onclick="window.location.href='{{ route('superadmin.surat.keramaian.create') }}'"
                class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-sm px-5 py-2.5 flex items-center space-x-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Surat Izin Keramaian</span>
            </button>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">NIK</th>
                        <th class="px-6 py-3">Nama Lengkap</th>
                        <th class="px-6 py-3">Hari, Tanggal</th>
                        <th class="px-6 py-3">Tempat</th>
                        <th class="px-6 py-3">Acara</th>
                        <th class="px-6 py-3">Pejabat Penandatangan</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($keramaianList as $index => $keramaian)
                    <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $index + 1 }}</th>
                        <td class="px-6 py-4">{{ $keramaian->nik ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $keramaian->full_name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            {{ $keramaian->day ?? '' }},
                            {{ \Carbon\Carbon::parse($keramaian->event_date)->format('d-m-Y') }}
                        </td>
                        <td class="px-6 py-4">{{ $keramaian->place ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $keramaian->event ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $keramaian->signer ? $keramaian->signer->judul : $keramaian->signing }}
                        </td>
                        <td class="flex items-center px-6 py-4 space-x-2">
                            <a href="{{ route('superadmin.surat.keramaian.export-pdf', $keramaian->id) }}" class="text-blue-600 hover:text-blue-800" aria-label="Export PDF" target="_blank">
                                <i class="fa-solid fa-file-pdf"></i>
                            </a>
                            <a href="{{ route('superadmin.surat.keramaian.edit', $keramaian->id) }}" class="text-yellow-600 hover:text-yellow-800" aria-label="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('superadmin.surat.keramaian.delete', $keramaian->id) }}" method="POST" onsubmit="return confirmDelete(event)">
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
                        <td colspan="8" class="text-center py-4">Tidak ada data surat izin keramaian.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination Section -->
            @if($keramaianList->hasPages())
            <div class="px-4 py-3 flex flex-col sm:flex-row justify-between items-center">
                <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                    Showing {{ $keramaianList->firstItem() }} to {{ $keramaianList->lastItem() }} of {{ $keramaianList->total() }} results
                </div>
                {{ $keramaianList->links('pagination::tailwind') }}
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
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-[#7886C7] bg-gray-50">
                    <h3 class="text-xl font-semibold text-[#2D336B]">Detail Surat Izin Keramaian</h3>
                    <button onclick="closeDetailModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center">
                        <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-4 md:p-5 overflow-y-auto max-h-[70vh]">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Informasi Pemohon -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold mb-4 text-[#7886C7]">Informasi Pemohon</h4>
                            <div class="space-y-3">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">NIK</span>
                                    <span id="detailNik" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Nama Lengkap</span>
                                    <span id="detailFullName" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Tempat, Tanggal Lahir</span>
                                    <span class="font-medium">
                                        <span id="detailBirthPlace"></span>,
                                        <span id="detailBirthDate"></span>
                                    </span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Jenis Kelamin</span>
                                    <span id="detailGender" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Pekerjaan</span>
                                    <span id="detailJob" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Agama</span>
                                    <span id="detailReligion" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Kewarganegaraan</span>
                                    <span id="detailCitizenStatus" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Alamat</span>
                                    <span id="detailAddress" class="font-medium"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Lokasi -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold mb-4 text-[#7886C7]">Informasi Lokasi</h4>
                            <div class="space-y-3">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Desa/Kelurahan</span>
                                    <span id="detailVillageName" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Kecamatan</span>
                                    <span id="detailSubdistrictName" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Kabupaten/Kota</span>
                                    <span id="detailDistrictName" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Provinsi</span>
                                    <span id="detailProvinceName" class="font-medium"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Keramaian -->
                        <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                            <h4 class="text-lg font-semibold mb-4 text-[#7886C7]">Informasi Acara</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Hari</span>
                                    <span id="detailDay" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Waktu</span>
                                    <span id="detailTime" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Tanggal</span>
                                    <span id="detailEventDate" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Tempat</span>
                                    <span id="detailPlace" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Acara</span>
                                    <span id="detailEvent" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Hiburan</span>
                                    <span id="detailEntertainment" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Undangan</span>
                                    <span id="detailInvitation" class="font-medium"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Surat -->
                        <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                            <h4 class="text-lg font-semibold mb-4 text-[#7886C7]">Informasi Surat</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Nomor Surat</span>
                                    <span id="detailLetterNumber" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Pejabat Penandatangan</span>
                                    <span id="detailSigning" class="font-medium"></span>
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
        document.addEventListener('DOMContentLoaded', function() {
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
        });

        // Delete confirmation function
        function confirmDelete(event) {
            event.preventDefault();
            const form = event.target;

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
                    form.submit();
                }
            });

            return false;
        }

        // Function to show detail modal
        async function showDetailModal(id) {
            try {
                const response = await fetch(`{{ url('/superadmin/surat/keramaian') }}/${id}/detail`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to fetch data');
                }

                const data = await response.json();
                const keramaian = data.keramaian;

                // Mapping for select fields
                const genderMap = { '1': 'Laki-laki', '2': 'Perempuan' };
                const religionMap = {
                    '1': 'Islam', '2': 'Kristen', '3': 'Katholik',
                    '4': 'Hindu', '5': 'Buddha', '6': 'Kong Hu Cu',
                    '7': 'Lainnya'
                };
                const citizenStatusMap = { '1': 'WNA', '2': 'WNI' };

                // Format date function
                const formatDate = (dateStr) => {
                    if (!dateStr) return '-';
                    const date = new Date(dateStr);
                    return date.toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                };

                // Format time function
                const formatTime = (timeStr) => {
                    if (!timeStr) return '-';
                    const time = new Date(`1970-01-01T${timeStr}`);
                    return time.toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                };

                // Set values for applicant information
                document.getElementById('detailNik').innerText = keramaian.nik || '-';
                document.getElementById('detailFullName').innerText = keramaian.full_name || '-';
                document.getElementById('detailBirthPlace').innerText = keramaian.birth_place || '-';
                document.getElementById('detailBirthDate').innerText = formatDate(keramaian.birth_date);
                document.getElementById('detailGender').innerText = genderMap[keramaian.gender] || '-';
                document.getElementById('detailJob').innerText = keramaian.job || '-';
                document.getElementById('detailReligion').innerText = religionMap[keramaian.religion] || '-';
                document.getElementById('detailCitizenStatus').innerText = citizenStatusMap[keramaian.citizen_status] || '-';
                document.getElementById('detailAddress').innerText = keramaian.address || '-';

                // Set values for location
                document.getElementById('detailProvinceName').innerText = keramaian.province_name || '-';
                document.getElementById('detailDistrictName').innerText = keramaian.district_name || '-';
                document.getElementById('detailSubdistrictName').innerText = keramaian.subdistrict_name || '-';
                document.getElementById('detailVillageName').innerText = keramaian.village_name || '-';

                // Set values for event information
                document.getElementById('detailDay').innerText = keramaian.day || '-';
                document.getElementById('detailTime').innerText = formatTime(keramaian.time);
                document.getElementById('detailEventDate').innerText = formatDate(keramaian.event_date);
                document.getElementById('detailPlace').innerText = keramaian.place || '-';
                document.getElementById('detailEvent').innerText = keramaian.event || '-';
                document.getElementById('detailEntertainment').innerText = keramaian.entertainment || '-';
                document.getElementById('detailInvitation').innerText = keramaian.invitation || '-';

                // Set values for letter information
                document.getElementById('detailLetterNumber').innerText = keramaian.letter_number || '-';
                document.getElementById('detailSigning').innerText = keramaian.signing || '-';

                // Show modal
                document.getElementById('detailModal').classList.remove('hidden');

            } catch (error) {
                console.error('Error fetching data:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Tidak dapat memuat data surat izin keramaian.',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        }

        // Function to close detail modal
        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }
    </script>
</x-layout>

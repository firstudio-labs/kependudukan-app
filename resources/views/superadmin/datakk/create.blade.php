<x-layout>
    <div class="p-4 mt-14">
        <!-- Alert Sukses -->
        {{-- @if(session('success'))
            <div id="successAlert"
                class="flex items-center p-4 mb-4 text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-green-800 dark:text-green-300 relative"
                role="alert">
                <svg class="w-5 h-5 mr-2 text-green-800 dark:text-green-300" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="font-medium">Sukses!</span> {{ session('success') }}
                <button type="button"
                    class="absolute top-2 right-2 text-green-800 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-900 rounded-lg p-1 transition-all duration-300"
                    onclick="closeAlert('successAlert')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        @endif

        <!-- Alert Error -->
        @if(session('error'))
            <div id="errorAlert"
                class="flex items-center p-4 mb-4 text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-red-800 dark:text-red-300 relative"
                role="alert">
                <svg class="w-5 h-5 mr-2 text-red-800 dark:text-red-300" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18.364 5.636L5.636 18.364M5.636 5.636l12.728 12.728"></path>
                </svg>
                <span class="font-medium">Gagal!</span> {{ session('error') }}
                <button type="button"
                    class="absolute top-2 right-2 text-red-800 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-900 rounded-lg p-1 transition-all duration-300"
                    onclick="closeAlert('errorAlert')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        @endif --}}

        <!-- Judul H1 -->
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Data KK</h1>

        <!-- Form Tambah Data KK -->
        <form method="POST" action="{{ route('kk.store') }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Kolom 1: Data Utama -->
                <div>
                    <label for="kkSelect" class="block text-sm font-medium text-gray-700">No KK</label>
                    <select id="kkSelect" name="kk" autocomplete="off"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        style="max-height: 200px; overflow-y: auto;">
                        <option value="">Pilih No KK</option>
                        <!-- Opsi lainnya -->
                    </select>
                </div>

                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <select id="full_name" name="full_name" autocomplete="name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        style="max-height: 200px; overflow-y: auto;">
                        <option value="">Pilih Nama Lengkap</option>
                        <!-- Opsi lainnya -->
                    </select>
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea id="address" name="address" autocomplete="street-address"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required></textarea>
                </div>

                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Kode Pos</label>
                    <input type="text" id="postal_code" name="postal_code" autocomplete="postal-code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                </div>

                <div>
                    <label for="rt" class="block text-sm font-medium text-gray-700">RT</label>
                    <input type="text" id="rt" name="rt" autocomplete="address-line1"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                </div>

                <div>
                    <label for="rw" class="block text-sm font-medium text-gray-700">RW</label>
                    <input type="text" id="rw" name="rw" autocomplete="address-line2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                </div>

                <div>
                    <label for="telepon" class="block text-sm font-medium text-gray-700">Telepon</label>
                    <input type="text" id="telepon" name="telepon" autocomplete="tel"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" autocomplete="email"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <div>
                    <label for="jml_anggota_kk" class="block text-sm font-medium text-gray-700">Jumlah Anggota
                        Keluarga</label>
                    <input type="text" id="jml_anggota_kk" name="jml_anggota_kk" autocomplete="off"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        readonly>
                </div>

                <!-- Dynamic Family Members Fields -->
                <div id="familyMembersContainer" class="col-span-2">
                    <!-- Family member fields will be inserted here -->
                </div>
            </div>

            <!-- Kategori: Data Wilayah -->
            <div class="mt-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Data Wilayah</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="province_id" class="block text-sm font-medium text-gray-700">Provinsi</label>
                        <select id="province_id" name="province_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                            required>
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province['code'] }}">{{ $province['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kabupaten stays as select but will be populated via JS -->
                    <div>
                        <label for="district_id" class="block text-sm font-medium text-gray-700">Kabupaten</label>
                        <select id="district_id" name="district_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                            required>
                            <option value="">Pilih Kabupaten</option>
                        </select>
                    </div>

                    <div>
                        <label for="sub_district_id" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                        <select id="sub_district_id" name="sub_district_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                            required>
                            <option value="">Pilih Kecamatan</option>
                        </select>
                    </div>

                    <div>
                        <label for="village_id" class="block text-sm font-medium text-gray-700">Desa/Kelurahan</label>
                        <select id="village_id" name="village_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                            required>
                            <option value="">Pilih Desa/Kelurahan</option>
                        </select>
                    </div>

                    <div>
                        <label for="dusun" class="block text-sm font-medium text-gray-700">Dusun/Dukuh/Kampung</label>
                        <input type="text" name="dusun" id="dusun" autocomplete="address-level5"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>
                </div>
            </div>

            <!-- Kategori: Alamat di Luar Negeri -->
            <div class="mt-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Alamat di Luar Negeri <span
                        class="text-red-500">*</span></h2>
                <p class="text-sm text-gray-500 mb-4">Hanya diisi oleh WNI di luar wilayah NKRI.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="alamat_luar_negeri" class="block text-sm font-medium text-gray-700">Alamat Luar
                            Negeri</label>
                        <textarea name="alamat_luar_negeri" id="alamat_luar_negeri" autocomplete="off"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"></textarea>
                    </div>

                    <div>
                        <label for="kota" class="block text-sm font-medium text-gray-700">Kota</label>
                        <input type="text" name="kota" id="kota" autocomplete="off"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <div>
                        <label for="negara_bagian" class="block text-sm font-medium text-gray-700">Provinsi/Negara
                            Bagian</label>
                        <input type="text" name="negara_bagian" id="negara_bagian" autocomplete="off"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <div>
                        <label for="negara" class="block text-sm font-medium text-gray-700">Negara</label>
                        <input type="text" name="negara" id="negara" autocomplete="country"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <div>
                        <label for="kode_pos_luar_negeri" class="block text-sm font-medium text-gray-700">Kode Pos Luar
                            Negeri</label>
                        <input type="text" name="kode_pos_luar_negeri" id="kode_pos_luar_negeri"
                            autocomplete="postal-code"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan dan Batal -->
            <div class="mt-6 flex justify-end space-x-4">
                <button type="button" onclick="window.history.back()"
                    class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Batal
                </button>
                <button type="submit"
                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">Simpan</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
        // Fungsi untuk menutup alert
        function closeAlert(alertId) {
            document.getElementById(alertId).classList.add('hidden');
        }

        // Menutup alert secara otomatis setelah 5 detik
        setTimeout(function () {
            const successAlert = document.getElementById('successAlert');
            const errorAlert = document.getElementById('errorAlert');

            if (successAlert) {
                successAlert.classList.add('opacity-0', 'transition-opacity', 'duration-1000');
                setTimeout(() => successAlert.classList.add('hidden'), 1000);
            }

            if (errorAlert) {
                errorAlert.classList.add('opacity-0', 'transition-opacity', 'duration-1000');
                setTimeout(() => errorAlert.classList.add('hidden'), 1000);
            }
        }, 5000);

        // Fungsi untuk mengambil data dari API
        async function fetchCitizens() {
            try {
                const response = await axios.get('{{ config("services.kependudukan.url") }}/api/all-citizens', {
                    headers: {
                        'X-API-Key': '{{ config("services.kependudukan.key") }}'
                    }
                });

                const data = response.data;

                if (data.status === 'OK' && data.data && Array.isArray(data.data)) {
                    const kkSelect = document.getElementById('kkSelect');
                    const fullNameSelect = document.getElementById('full_name');

                    if (!kkSelect || !fullNameSelect) {
                        console.error('Elemen select untuk No KK atau Nama Lengkap tidak ditemukan.');
                        return;
                    }

                    // Kosongkan opsi yang ada sebelum menambahkan yang baru
                    kkSelect.innerHTML = '<option value="">Pilih No KK</option>';
                    fullNameSelect.innerHTML = '<option value="">Pilih Nama Lengkap</option>';

                    // Tambahkan opsi baru ke select
                    data.data.forEach(citizen => {
                        if (citizen.family_status === 'KEPALA KELUARGA') {
                            const kkOption = document.createElement('option');
                            kkOption.value = citizen.kk;
                            kkOption.textContent = citizen.kk;
                            kkOption.setAttribute('data-full-name', citizen.full_name);
                            kkOption.setAttribute('data-address', citizen.address);
                            kkOption.setAttribute('data-postal-code', citizen.postal_code);
                            kkOption.setAttribute('data-rt', citizen.rt);
                            kkOption.setAttribute('data-rw', citizen.rw);
                            kkOption.setAttribute('data-telepon', citizen.telepon || '');
                            kkOption.setAttribute('data-email', citizen.email || '');
                            kkOption.setAttribute('data-province_id', citizen.province_id);
                            kkOption.setAttribute('data-district_id', citizen.district_id);
                            kkOption.setAttribute('data-sub_district_id', citizen.sub_district_id);
                            kkOption.setAttribute('data-village_id', citizen.village_id);
                            kkOption.setAttribute('data-dusun', citizen.dusun || '');
                            kkSelect.appendChild(kkOption);

                            const fullNameOption = document.createElement('option');
                            fullNameOption.value = citizen.full_name;
                            fullNameOption.textContent = citizen.full_name;
                            fullNameOption.setAttribute('data-kk', citizen.kk);
                            fullNameOption.setAttribute('data-address', citizen.address);
                            fullNameOption.setAttribute('data-postal-code', citizen.postal_code);
                            fullNameOption.setAttribute('data-rt', citizen.rt);
                            fullNameOption.setAttribute('data-rw', citizen.rw);
                            fullNameOption.setAttribute('data-telepon', citizen.telepon || '');
                            fullNameOption.setAttribute('data-email', citizen.email || '');
                            fullNameOption.setAttribute('data-province_id', citizen.province_id);
                            fullNameOption.setAttribute('data-district_id', citizen.district_id);
                            fullNameOption.setAttribute('data-sub_district_id', citizen.sub_district_id); // Fix: This had a dash instead of underscore
                            fullNameOption.setAttribute('data-village_id', citizen.village_id);
                            fullNameOption.setAttribute('data-dusun', citizen.dusun || '');
                            fullNameSelect.appendChild(fullNameOption);
                        }
                    });
                } else {
                    console.error('Struktur respons API tidak valid:', data);
                }
            } catch (error) {
                if (error.response) {
                    console.error('Gagal mengambil data warga:', error.response.data);
                } else {
                    console.error('Gagal mengambil data warga:', error.message);
                }
            }
        }

        // Event listener saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function () {
            let isUpdating = false; // Flag untuk menghindari rekursi

            // Inisialisasi Select2 untuk elemen select No KK
            $('#kkSelect').select2({
                placeholder: 'Pilih No KK',
                width: '100%'
            });

            // Inisialisasi Select2 untuk elemen select Nama Lengkap
            $('#full_name').select2({
                placeholder: 'Pilih Nama Lengkap',
                width: '100%'
            });

            // Event listener untuk perubahan pada Select2 No KK
            $('#kkSelect').on('change', function () {
                if (isUpdating) return; // Hindari rekursi
                isUpdating = true;

                const selectedKK = $(this).val(); // Ambil nilai yang dipilih
                const selectedOption = $(this).find('option:selected'); // Ambil opsi yang dipilih

                if (selectedKK) {
                    // Ambil data dari atribut data-*
                    const fullName = selectedOption.data('full-name');
                    const address = selectedOption.data('address');
                    const postalCode = selectedOption.data('postal-code');
                    const rt = selectedOption.data('rt');
                    const rw = selectedOption.data('rw');
                    const telepon = selectedOption.data('telepon') || ''; // Tambahkan default empty string
                    const email = selectedOption.data('email') || ''; // Tambahkan default empty string
                    const province_id = selectedOption.data('province_id');
                    const district_id = selectedOption.data('district_id');
                    const subDistrict_id = selectedOption.data('sub_district_id');
                    const village_id = selectedOption.data('village_id');
                    const dusun = selectedOption.data('dusun') || ''; // Tambahkan default empty string

                    // Isi field yang sesuai
                    $('#full_name').val(fullName || '').trigger('change.select2');
                    $('#address').val(address || '');
                    $('#postal_code').val(postalCode || '');
                    $('#rt').val(rt || '');
                    $('#rw').val(rw || '');
                    $('#telepon').val(telepon);
                    $('#email').val(email);
                    $('#dusun').val(dusun);

                    // Load location data sequentially
                    loadLocationData(province_id, district_id, subDistrict_id, village_id);

                    // Hitung jumlah anggota keluarga berdasarkan No KK
                    let jumlahAnggota = $('#kkSelect option').filter(function () {
                        return $(this).val() === selectedKK;
                    }).length;

                    $('#jml_anggota_kk').val(jumlahAnggota);

                    $.ajax({
                        url: "{{ config('services.kependudukan.url') }}/api/citizens-family/" + selectedKK,
                        type: "GET",
                        headers: {
                            'X-API-Key': "{{ config('services.kependudukan.key') }}"
                        },
                        success: function (response) {
                            if (response.status === "OK") {
                                $('#jml_anggota_kk').val(response.count);

                                // Clear previous fields
                                $('#familyMembersContainer').empty();

                                // Create fields for each family member
                                if (response.data && Array.isArray(response.data)) {
                                    response.data.forEach((member, index) => {
                                        const fieldHtml = `
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Anggota ${index + 1}</label>
                                    <input type="text"
                                        value="${member.full_name} - ${member.family_status}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                                        readonly>
                                    <input type="hidden" name="family_members[${index}][full_name]" value="${member.full_name}">
                                    <input type="hidden" name="family_members[${index}][family_status]" value="${member.family_status}">
                                </div>
                            `;
                                        $('#familyMembersContainer').append(fieldHtml);
                                    });
                                }
                            } else {
                                $('#jml_anggota_kk').val(0);
                                $('#familyMembersContainer').empty();
                            }
                        },
                        error: function () {
                            $('#jml_anggota_kk').val(0);
                            $('#familyMembersContainer').empty();
                        }
                    });

                    // Fetch family members count
                    $.ajax({
                        url: "{{ route('getFamilyMembers') }}",
                        type: "GET",
                        data: { kk: selectedKK },
                        headers: {
                            'X-API-Key': "{{ config('services.kependudukan.key') }}"
                        },
                        success: function (response) {
                            if (response.status === "OK") {
                                $('#jml_anggota_kk').val(response.count);

                                // Clear previous fields
                                $('#familyMembersContainer').empty();

                                // Create fields for each family member
                                if (response.data && Array.isArray(response.data)) {
                                    response.data.forEach((member, index) => {
                                        const fieldHtml = `
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Anggota ${index + 1}</label>
                                    <input type="text"
                                        value="${member.full_name} - ${member.family_status}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                                        readonly>
                                    <input type="hidden" name="family_members[${index}][full_name]" value="${member.full_name}">
                                    <input type="hidden" name="family_members[${index}][family_status]" value="${member.family_status}">
                                </div>
                            `;
                                        $('#familyMembersContainer').append(fieldHtml);
                                    });
                                }
                            } else {
                                $('#jml_anggota_kk').val(0);
                                $('#familyMembersContainer').empty();
                            }
                        },
                        error: function () {
                            $('#jml_anggota_kk').val(0);
                            $('#familyMembersContainer').empty();
                        }
                    });

                } else {
                    // Kosongkan field jika tidak ada pilihan
                    $('#full_name').val('').trigger('change.select2');
                    $('#address').val('');
                    $('#postal_code').val('');
                    $('#rt').val('');
                    $('#rw').val('');
                    $('#telepon').val('');
                    $('#email').val('');
                    $('#provinc_id').val('');
                    $('#district_id').val('');
                    $('#sub_district_id').val('');
                    $('#village_id').val('');
                    $('#dusun').val('');
                }

                isUpdating = false; // Reset flag
            });

            // Event listener untuk perubahan pada Select2 Nama Lengkap
            $('#full_name').on('change', function () {
                if (isUpdating) return; // Hindari rekursi
                isUpdating = true;

                const selectedFullName = $(this).val(); // Ambil nilai yang dipilih
                const selectedOption = $(this).find('option:selected'); // Ambil opsi yang dipilih

                if (selectedFullName) {
                    // Ambil data dari atribut data-*
                    const kk = selectedOption.data('kk');
                    const address = selectedOption.data('address');
                    const postalCode = selectedOption.data('postal-code');
                    const rt = selectedOption.data('rt');
                    const rw = selectedOption.data('rw');
                    const telepon = selectedOption.data('telepon') || ''; // Tambahkan default empty string
                    const email = selectedOption.data('email') || '';
                    const province_id = selectedOption.data('province_id');
                    const district_id = selectedOption.data('district_id');
                    const subDistrict_id = selectedOption.data('sub-district_id');
                    const village_id = selectedOption.data('village_id');
                    const dusun = selectedOption.data('dusun') || '';

                    // Isi field yang sesuai
                    $('#kkSelect').val(kk || '').trigger('change.select2');
                    $('#address').val(address || '');
                    $('#postal_code').val(postalCode || '');
                    $('#rt').val(rt || '');
                    $('#rw').val(rw || '');
                    $('#telepon').val(telepon);
                    $('#email').val(email);
                    $('#dusun').val(dusun);

                    // Load location data sequentially
                    loadLocationData(province_id, district_id, subDistrict_id, village_id);

                    if (kk) {
                        $.ajax({
                            url: "{{ route('getFamilyMembers') }}",
                            type: "GET",
                            data: { kk: kk },
                            headers: {
                                'X-API-Key': "{{ config('services.kependudukan.key') }}"
                            },
                            success: function (response) {
                                if (response.status === "OK") {
                                    $('#jml_anggota_kk').val(response.count);

                                    // Clear previous fields
                                    $('#familyMembersContainer').empty();

                                    // Create fields for each family member
                                    if (response.data && Array.isArray(response.data)) {
                                        response.data.forEach((member, index) => {
                                            const fieldHtml = `
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700">Anggota ${index + 1}</label>
                                        <input type="text"
                                            value="${member.full_name} - ${member.family_status}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                                            readonly>
                                        <input type="hidden" name="family_members[${index}][full_name]" value="${member.full_name}">
                                        <input type="hidden" name="family_members[${index}][family_status]" value="${member.family_status}">
                                    </div>
                                `;
                                            $('#familyMembersContainer').append(fieldHtml);
                                        });
                                    }
                                } else {
                                    $('#jml_anggota_kk').val(0);
                                    $('#familyMembersContainer').empty();
                                }
                            },
                            error: function () {
                                $('#jml_anggota_kk').val(0);
                                $('#familyMembersContainer').empty();
                            }
                        });
                    }
                } else {
                    // Kosongkan field jika tidak ada pilihan
                    $('#kkSelect').val('').trigger('change.select2');
                    $('#address').val('');
                    $('#postal_code').val('');
                    $('#rt').val('');
                    $('#rw').val('');
                    $('#telepon').val('');
                    $('#email').val('');
                    $('#province_id').val('');
                    $('#district_id').val('');
                    $('#sub_district_id').val('');
                    $('#village_id').val('');
                    $('#dusun').val('');
                    $('#jml_anggota_kk').val('');
                    $('#familyMembersContainer').empty();
                }

                isUpdating = false; // Reset flag
            });

            fetchCitizens(); // Memanggil fungsi untuk mengambil data warga saat halaman dimuat
        });

        // Function to load location data sequentially
        function loadLocationData(provinceId, districtId, subDistrictId, villageId) {
            console.log('Loading location data:', { provinceId, districtId, subDistrictId, villageId });

            // Set province first
            if (provinceId) {
                $('#province_id').val(provinceId).trigger('change');

                // Wait for districts to load, then set district
                setTimeout(() => {
                    if (districtId) {
                        $('#district_id').val(districtId).trigger('change');

                        // Wait for sub-districts to load, then set sub-district
                        setTimeout(() => {
                            if (subDistrictId) {
                                $('#sub_district_id').val(subDistrictId).trigger('change');

                                // Wait for villages to load, then set village
                                setTimeout(() => {
                                    if (villageId) {
                                        $('#village_id').val(villageId);
                                    }
                                }, 1000);
                            }
                        }, 1000);
                    }
                }, 1000);
            }
        }

        // Event handler untuk dropdown provinsi
        $('#province_id').on('change', function () {
            const provinceCode = $(this).val();
            console.log('Kode provinsi yang dipilih:', provinceCode);

            // Reset dropdown kabupaten, kecamatan, dan desa
            $('#district_id').empty().append('<option value="">Pilih Kabupaten</option>').prop('disabled', true);
            $('#sub_district_id').empty().append('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
            $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>').prop('disabled', true);

            if (provinceCode) {
                // Tampilkan loading state
                $('#district_id').prop('disabled', true).empty().append('<option value="">Loading...</option>');

                // Tambahkan debugging URL
                const apiUrl = `/api/wilayah/provinsi/${provinceCode}/kota`;
                console.log('Request URL:', apiUrl);

                // Ambil data kabupaten dari API
                $.ajax({
                    url: apiUrl,
                    type: 'GET',
                    success: function (data) {
                        console.log('Data kabupaten yang diterima:', data);
                        $('#district_id').empty().append('<option value="">Pilih Kabupaten</option>');

                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach(function (item) {
                                $('#district_id').append(`<option value="${item.code}">${item.name}</option>`);
                            });
                        }

                        $('#district_id').prop('disabled', false);
                    },
                    error: function (error) {
                        console.error('Error loading kabupaten:', error);
                        $('#district_id').empty().append('<option value="">Error loading data</option>');
                    }
                });
            }
        });

        // Event handler untuk dropdown kabupaten
        $('#district_id').on('change', function () {
            const kotaCode = $(this).val();

            // Reset dropdown kecamatan dan desa
            $('#sub_district_id').empty().append('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
            $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>').prop('disabled', true);

            if (kotaCode) {
                // Tampilkan loading state
                $('#sub_district_id').prop('disabled', true).empty().append('<option value="">Loading...</option>');

                // Ambil data kecamatan dari API
                $.ajax({
                    url: `/api/wilayah/kota/${kotaCode}/kecamatan`,
                    type: 'GET',
                    success: function (data) {
                        $('#sub_district_id').empty().append('<option value="">Pilih Kecamatan</option>');

                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach(function (item) {
                                $('#sub_district_id').append(`<option value="${item.code}">${item.name}</option>`);
                            });
                        }

                        $('#sub_district_id').prop('disabled', false);
                    },
                    error: function (error) {
                        console.error('Error loading kecamatan:', error);
                        $('#sub_district_id').empty().append('<option value="">Error loading data</option>');
                    }
                });
            }
        });

        // Event handler untuk dropdown kecamatan
        $('#sub_district_id').on('change', function () {
            const kecamatanCode = $(this).val();

            // Reset dropdown desa
            $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>').prop('disabled', true);

            if (kecamatanCode) {
                // Tampilkan loading state
                $('#village_id').prop('disabled', true).empty().append('<option value="">Loading...</option>');

                // Ambil data desa/kelurahan dari API
                $.ajax({
                    url: `/api/wilayah/kecamatan/${kecamatanCode}/kelurahan`,
                    type: 'GET',
                    success: function (data) {
                        $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>');

                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach(function (item) {
                                $('#village_id').append(`<option value="${item.code}">${item.name}</option>`);
                            });
                        }

                        $('#village_id').prop('disabled', false);
                    },
                    error: function (error) {
                        console.error('Error loading desa/kelurahan:', error);
                        $('#village_id').empty().append('<option value="">Error loading data</option>');
                    }
                });
            }
        });

    </script>
</x-layout>

<x-guest.surat-layout title="Surat Keterangan Pengantar KTP">
    <!-- Title Heading -->
    <h1 class="text-2xl font-extrabold text-gray-800 text-shadow mb-4">Portal Layanan Desa</h1>

    <!-- Wrapper Flex -->
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Card Nomor Antrian -->
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl shadow-md border border-white/20 p-6 text-center w-full lg:w-1/3 self-start">
            <button class="text-black font-semibold px-4 py-2 rounded-xl mb-4 bg-white/10 backdrop-blur-lg border border-white/20 shadow-sm">
                Antrian Layanan Desa
            </button>

            <div class="border border-white/20 rounded-2xl p-6 bg-white/5 backdrop-blur-lg shadow-inner">
                <div class="text-sm text-black mb-1">No Antrian Saat Ini</div>
                <div class="text-5xl font-bold text-black drop-shadow-md">{{ $queueNumber }}</div>
                @if($villageName)
                    <div class="mt-2 text-[#a7a7ee] text-sm">Nomor antrian anda</div>
                    <div class="mt-1 text-sm text-gray-600">Desa: {{ $villageName }}</div>
                @endif
            </div>

            <p class="mt-4 text-sm italic text-black">Quod Enchiridion Epictetus stoici scripsit. Rodrigo Abela</p>
        </div>
        <div class="w-full lg:w-2/3" id="inheritance-form-container"
             data-citizen-route="{{ route('citizens.administrasi') }}"
             data-provinces="{{ json_encode($provinces) }}"
             data-success="{{ session('success') }}"
             data-error="{{ session('error') }}">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Buat Surat Keterangan Ahli Waris</h1>

            <form method="POST" action="{{ route('guest.surat.ahli-waris.store') }}">
                @csrf

                <!-- Daftar Ahli Waris Section -->
                <div class="mb-2 mt-6">
                    <h2 class="text-xl font-bold text-gray-800">Daftar Ahli Waris</h2>
                </div>

                <div id="heirs-container">
                    <!-- Template for heir row, will be cloned by JavaScript -->
                    <div class="heir-row mb-4 template" style="display:none">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">NIK <span class="text-red-500">*</span></label>
                                <select name="nik[]" class="nik-select mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    <option value="">Pilih NIK</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                                <select name="full_name[]" class="fullname-select mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    <option value="">Pilih Nama</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tempat Lahir <span class="text-red-500">*</span></label>
                                <input type="text" name="birth_place[]" class="birth-place mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                                <input type="date" name="birth_date[]" class="birth-date mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                                <select name="gender[]" class="gender mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="1">Laki-Laki</option>
                                    <option value="2">Perempuan</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Agama <span class="text-red-500">*</span></label>
                                <select name="religion[]" class="religion mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    <option value="">Pilih Agama</option>
                                    <option value="1">Islam</option>
                                    <option value="2">Kristen</option>
                                    <option value="3">Katholik</option>
                                    <option value="4">Hindu</option>
                                    <option value="5">Buddha</option>
                                    <option value="6">Kong Hu Cu</option>
                                    <option value="7">Lainnya</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Hubungan Keluarga <span class="text-red-500">*</span></label>
                                <select name="family_status[]" class="family-status mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    <option value="">Pilih Hubungan</option>
                                    <option value="1">ANAK</option>
                                    <option value="2">KEPALA KELUARGA</option>
                                    <option value="3">ISTRI</option>
                                    <option value="4">ORANG TUA</option>
                                    <option value="5">MERTUA</option>
                                    <option value="6">CUCU</option>
                                    <option value="7">FAMILI LAIN</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-2">
                            <label class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                            <textarea name="address[]" class="address mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
                        </div>

                        <div class="flex justify-end mt-2">
                            <button type="button" class="remove-heir bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Hapus</button>
                        </div>
                    </div>
                </div>

                <div class="mt-4 mb-6">
                    <button type="button" id="add-heir" class="bg-[#2D336B] text-white px-4 py-2 rounded hover:bg-[#7886C7]">
                        Tambah Ahli Waris
                    </button>
                </div>

                <!-- Data Wilayah Section -->
                {{-- <div class="mb-2 mt-6"> --}}
                    {{-- <h2 class="text-xl font-bold text-gray-800">Data Wilayah</h2> --}}
                {{-- </div> --}}

                <!-- Hidden Location Fields (instead of visible dropdowns) -->
                <input type="hidden" id="province_id" name="province_id" value="{{ request('province_id') }}">
                <input type="hidden" id="district_id" name="district_id" value="{{ request('district_id') }}">
                <input type="hidden" id="subdistrict_id" name="subdistrict_id" value="{{ request('sub_district_id') }}">
                <input type="hidden" id="village_id" name="village_id" value="{{ request('village_id') }}">

                <!-- Informasi Surat Section -->
                <div class="mb-2 mt-6">
                    <h2 class="text-xl font-bold text-gray-800">Informasi Surat</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="heir_name" class="block text-sm font-medium text-gray-700">Nama Ahli Waris <span class="text-red-500">*</span></label>
                        <input type="text" id="heir_name" name="heir_name" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <div>
                        <label for="deceased_name" class="block text-sm font-medium text-gray-700">Nama Almarhum <span class="text-red-500">*</span></label>
                        <input type="text" id="deceased_name" name="deceased_name" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <div>
                        <label for="death_place" class="block text-sm font-medium text-gray-700">Tempat Meninggal <span class="text-red-500">*</span></label>
                        <input type="text" id="death_place" name="death_place" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <div>
                        <label for="death_date" class="block text-sm font-medium text-gray-700">Tanggal Meninggal <span class="text-red-500">*</span></label>
                        <input type="date" id="death_date" name="death_date" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <div>
                        <label for="death_certificate_number" class="block text-sm font-medium text-gray-700">Nomor Akte Kematian</label>
                        <input type="text" id="death_certificate_number" name="death_certificate_number" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <div>
                        <label for="death_certificate_date" class="block text-sm font-medium text-gray-700">Tanggal Akte Kematian</label>
                        <input type="date" id="death_certificate_date" name="death_certificate_date" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <div>
                        <label for="inheritance_letter_date" class="block text-sm font-medium text-gray-700">Tanggal Surat Waris</label>
                        <input type="date" id="inheritance_letter_date" name="inheritance_letter_date" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <div>
                        <label for="inheritance_type" class="block text-sm font-medium text-gray-700">Jenis Warisan <span class="text-red-500">*</span></label>
                        <input type="text" id="inheritance_type" name="inheritance_type" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-4">
                    <button type="button" onclick="window.history.back()" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                        Batal
                    </button>
                    <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#7886C7] text-base font-medium text-white hover:bg-[#2D336B] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="{{ asset('js/location-dropdowns.js') }}"></script>
    <script src="{{ asset('js/sweet-alert-utils.js') }}"></script>
    <script src="{{ asset('js/citizen-only-form.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Setup inheritance form
            setupInheritanceForm();

            // Initialize SweetAlert messages
            if ("{{ session('success') }}") {
                showSuccessAlert("{{ session('success') }}");
            }

            if ("{{ session('error') }}") {
                showErrorAlert("{{ session('error') }}");
            }

            function setupInheritanceForm() {
                // Get heirs container
                const heirsContainer = document.getElementById('heirs-container');
                const template = heirsContainer.querySelector('.heir-row.template');

                // Add first heir row
                addHeirRow();

                // Add button handler
                document.getElementById('add-heir').addEventListener('click', addHeirRow);

                function addHeirRow() {
                    // Clone template
                    const row = template.cloneNode(true);
                    row.classList.remove('template');
                    row.style.display = '';
                    heirsContainer.appendChild(row);

                    // Initialize selects in this row
                    const nikSelect = row.querySelector('.nik-select');
                    const nameSelect = row.querySelector('.fullname-select');

                    $(nikSelect).select2({
                        placeholder: 'Ketik untuk mencari NIK',
                        minimumInputLength: 1,
                        ajax: {
                            url: '{{ route("citizens.administrasi") }}',
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return {
                                    search: params.term
                                };
                            },
                            processResults: function(data) {
                                let results = [];
                                if (data.data) {
                                    results = data.data.map(citizen => ({
                                        id: citizen.nik,
                                        text: citizen.nik,
                                        citizen: citizen
                                    }));
                                }
                                return { results };
                            }
                        }
                    });

                    $(nameSelect).select2({
                        placeholder: 'Ketik untuk mencari nama',
                        minimumInputLength: 1,
                        ajax: {
                            url: '{{ route("citizens.administrasi") }}',
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return {
                                    search: params.term
                                };
                            },
                            processResults: function(data) {
                                let results = [];
                                if (data.data) {
                                    results = data.data.map(citizen => ({
                                        id: citizen.full_name,
                                        text: citizen.full_name,
                                        citizen: citizen
                                    }));
                                }
                                return { results };
                            }
                        }
                    });

                    // Remove button handler
                    row.querySelector('.remove-heir').addEventListener('click', function() {
                        if (heirsContainer.querySelectorAll('.heir-row:not(.template)').length > 1) {
                            row.remove();
                        } else {
                            alert('Minimal harus ada satu ahli waris');
                        }
                    });

                    // Set up interconnection between NIK and name
                    setupHeirFieldsConnection(nikSelect, nameSelect, row);
                }

                function setupHeirFieldsConnection(nikSelect, nameSelect, row) {
                    // Rest of connection logic
                }
            }
        });
    </script>
</x-guest.surat-layout>

/**
 * Inheritance Certificate Form Functionality
 */

// Function to handle success and error messages using SweetAlert
function showSweetAlert(type, title, text) {
    Swal.fire({
        icon: type,
        title: title,
        text: text,
        timer: 3000,
        showConfirmButton: false,
        position: 'top-end',
        toast: true
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Store the loaded citizens for reuse
    let allCitizens = [];
    let nameOptions = []; // Cache untuk options Select2

    // Get variables from data attributes
    const formContainer = document.getElementById('inheritance-form-container');
    if (!formContainer) {
        console.error('Form container not found');
        return;
    }

    const citizenApiRoute = formContainer.dataset.citizenRoute;
    const success = formContainer.dataset.success;
    const error = formContainer.dataset.error;

    // Show notifications if needed
    if (success) {
        showSweetAlert('success', 'Sukses!', success);
    }

    if (error) {
        showSweetAlert('error', 'Gagal!', error);
    }

    // Load citizens data
    $.ajax({
        url: citizenApiRoute,
        type: 'GET',
        dataType: 'json',
        data: {
            limit: 10000
        },
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(data) {
            // Transform the response to match what we expect
            let processedData = data;
            if (data && data.data && Array.isArray(data.data)) {
                processedData = data.data;
            } else if (data && Array.isArray(data)) {
                processedData = data;
            }

            // Make sure we have valid data
            if (!Array.isArray(processedData)) {
                console.error('Invalid citizen data format');
                return;
            }

            allCitizens = processedData;

            // Pre-process name options untuk Select2
            nameOptions = allCitizens.map(citizen => ({
                id: citizen.full_name,
                text: citizen.full_name,
                citizen: citizen
            })).filter(option => option.id); // Filter out empty names

            // Setup the heirs interface
            setupHeirsInterface();
        },
        error: function(error) {
            console.error('Failed to load citizen data:', error);
            // Setup the heirs interface anyway with empty data
            setupHeirsInterface();
        }
    });

    function setupHeirsInterface() {
        // Handle adding more heirs
        const heirsContainer = document.getElementById('heirs-container');
        const addHeirButton = document.getElementById('add-heir');
        const firstHeirRow = document.querySelector('.heir-row').cloneNode(true);

        // Remove the first heir row template
        heirsContainer.innerHTML = '';

        // Function to initialize a new heir row (optimized)
        function initializeHeirRow(heirRow) {
            // Setup NIK input
            const nikInput = heirRow.querySelector('.nik-select');
            if (nikInput) {
                nikInput.addEventListener('input', function() {
                    const nikValue = this.value.trim();

                    // Only process if NIK is exactly 16 digits
                    if (nikValue.length === 16 && /^\d+$/.test(nikValue)) {
                        // Find citizen with matching NIK
                        const matchedCitizen = allCitizens.find(citizen => {
                            const citizenNik = citizen.nik ? citizen.nik.toString() : '';
                            return citizenNik === nikValue;
                        });

                        if (matchedCitizen) {
                            // Fill form fields
                            populateHeirFieldsFromCitizen(heirRow, matchedCitizen);

                            // Update full name input (now as text input, not dropdown)
                            if ($(heirRow).find('.fullname-select').length) {
                                $(heirRow).find('.fullname-select').val(matchedCitizen.full_name);
                            }

                            // Visual feedback for success
                            $(nikInput).addClass('border-green-500').removeClass('border-red-500 border-gray-300');
                            setTimeout(() => {
                                $(nikInput).removeClass('border-green-500').addClass('border-gray-300');
                            }, 2000);
                        } else {
                            // Show error alert for NIK not found
                            Swal.fire({
                                title: 'Data Tidak Ditemukan',
                                text: 'NIK tidak terdaftar dalam sistem',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });

                            // Visual feedback for error
                            $(nikInput).addClass('border-red-500').removeClass('border-green-500 border-gray-300');
                            setTimeout(() => {
                                $(nikInput).removeClass('border-red-500').addClass('border-gray-300');
                            }, 2000);
                        }
                    }
                });
            }

            // Setup RF ID Tag listener
            setupHeirRfIdListener(heirRow);

            // Setup name select (optimized) - KODE DROPDOWN DIKOMENTARI
            const nameSelect = heirRow.querySelector('.fullname-select');
            if (nameSelect) {
                // --- KODE DROPDOWN NAMA LENGKAP DIKOMENTARI ---
                // Jika sewaktu-waktu dibutuhkan, bisa diaktifkan kembali

                /*
                // Initialize Select2 dengan data yang sudah di-cache
                $(nameSelect).select2({
                    placeholder: 'Ketik nama untuk mencari...',
                    width: '100%',
                    data: nameOptions, // Gunakan data yang sudah di-cache
                    minimumInputLength: 3,
                    language: {
                        noResults: function() {
                            return 'Tidak ada data yang ditemukan';
                        },
                        searching: function() {
                            return 'Mencari...';
                        },
                        inputTooShort: function() {
                            return 'Ketik minimal 3 karakter untuk mencari';
                        }
                    },
                    delay: 300,
                    matcher: function(params, data) {
                        if (!params.term) {
                            return null;
                        }

                        if (params.term.length < 3) {
                            return null;
                        }

                        const term = params.term.toLowerCase();
                        const text = data.text.toLowerCase();

                        if (text.indexOf(term) > -1) {
                            return data;
                        }

                        return null;
                    }
                }).on("select2:open", function() {
                    $('.select2-results__options').css('max-height', '400px');
                });

                // When name is selected, fill in other fields
                $(nameSelect).on('select2:select', function (e) {
                    const citizen = e.params.data.citizen;
                    if (citizen) {
                        // Set NIK in input
                        const nikValue = citizen.nik ? citizen.nik.toString() : '';
                        $(heirRow).find('.nik-select').val(nikValue);

                        // Fill other form fields
                        populateHeirFieldsFromCitizen(heirRow, citizen);
                    }
                });
                */
            }

            // Setup remove button
            const removeButton = heirRow.querySelector('.remove-heir');
            if (removeButton) {
                removeButton.addEventListener('click', function() {
                    if (document.querySelectorAll('#heirs-container .heir-row').length > 1) {
                        heirRow.remove();
                    } else {
                        Swal.fire({
                            title: 'Peringatan',
                            text: 'Minimal harus ada satu ahli waris',
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        }

        // Function to setup RF ID Tag listener for heir row
        function setupHeirRfIdListener(heirRow) {
            const rfIdInput = heirRow.querySelector('.rf-id-tag');
            if (!rfIdInput) return;

            // Tambahkan event untuk input dan paste
            rfIdInput.addEventListener('input', function() {
                const rfIdValue = this.value.trim();
                if (rfIdValue.length > 0) {
                    // Cari data warga dengan RF ID Tag yang sama
                    const matchedCitizen = allCitizens.find(citizen => {
                        // Jika citizen tidak memiliki rf_id_tag, lewati
                        if (citizen.rf_id_tag === undefined || citizen.rf_id_tag === null) {
                            return false;
                        }

                        // Konversi ke string dan normalisasi
                        const normalizedInput = rfIdValue.toString().replace(/^0+/, '').trim();
                        const normalizedStored = citizen.rf_id_tag.toString().replace(/^0+/, '').trim();

                        // Cek kecocokan persis
                        const exactMatch = normalizedInput === normalizedStored;

                        // Cek kecocokan sebagian (jika input adalah bagian dari rf_id_tag)
                        const partialMatch = normalizedStored.includes(normalizedInput) && normalizedInput.length >= 5;

                        // Kembalikan true jika ada kecocokan persis atau sebagian
                        return exactMatch || partialMatch;
                    });

                    // Jika ditemukan, isi form
                    if (matchedCitizen) {
                        populateHeirFieldsFromCitizen(heirRow, matchedCitizen);

                        // --- KODE UPDATE DROPDOWN DIKOMENTARI ---
                        // Jika sewaktu-waktu dibutuhkan, bisa diaktifkan kembali
                        /*
                        // Update dropdown NIK dan Nama dengan trigger yang benar
                        if ($(heirRow).find('.nik-select').length) {
                            $(heirRow).find('.nik-select').val(matchedCitizen.nik).trigger('change');
                        }
                        if ($(heirRow).find('.fullname-select').length) {
                            $(heirRow).find('.fullname-select').val(matchedCitizen.full_name).trigger('change');
                        }
                        */

                        // Set NIK dan nama lengkap secara manual (karena sekarang input text)
                        if ($(heirRow).find('.nik-select').length) {
                            $(heirRow).find('.nik-select').val(matchedCitizen.nik);
                        }
                        if ($(heirRow).find('.fullname-select').length) {
                            $(heirRow).find('.fullname-select').val(matchedCitizen.full_name);
                        }

                        // Feedback visual berhasil
                        $(rfIdInput).addClass('border-green-500').removeClass('border-red-500 border-gray-300');
                        setTimeout(() => {
                            $(rfIdInput).removeClass('border-green-500').addClass('border-gray-300');
                        }, 2000);
                    } else if (rfIdValue.length >= 5) {
                        // Feedback visual tidak ditemukan (hanya untuk input yang cukup panjang)
                        $(rfIdInput).addClass('border-red-500').removeClass('border-green-500 border-gray-300');
                        setTimeout(() => {
                            $(rfIdInput).removeClass('border-red-500').addClass('border-gray-300');
                        }, 2000);
                    }
                }
            });

            // Tambahkan event untuk paste
            rfIdInput.addEventListener('paste', function() {
                // Trigger input event after paste
                setTimeout(() => {
                    this.dispatchEvent(new Event('input'));
                }, 10);
            });
        }

        // Function to add a new heir row (optimized)
        function addHeirRow() {
            const heirRowClone = firstHeirRow.cloneNode(true);
            heirsContainer.appendChild(heirRowClone);

            // Gunakan setTimeout untuk memastikan DOM sudah siap
            setTimeout(() => {
                initializeHeirRow(heirRowClone);
            }, 10);
        }

        // Add first heir row
        addHeirRow();

        // Add heir button click handler
        addHeirButton.addEventListener('click', addHeirRow);
    }

    // Function to populate heir fields from citizen data
    function populateHeirFieldsFromCitizen(heirRow, citizen) {
        // Set NIK field
        if (citizen.nik) {
            const nikValue = citizen.nik.toString();
            $(heirRow).find('.nik-select').val(nikValue);
        }

        // Set full name field (now as input text, not dropdown)
        if (citizen.full_name) {
            $(heirRow).find('.fullname-select').val(citizen.full_name);
        }

        // Set birth place
        $(heirRow).find('.birth-place').val(citizen.birth_place || '');

        // Set birth date - handle different date formats
        if (citizen.birth_date) {
            let formattedDate = '';
            try {
                // Handle date in format "DD/MM/YYYY"
                if (citizen.birth_date.includes('/')) {
                    const [day, month, year] = citizen.birth_date.split('/');
                    formattedDate = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
                }
                // Handle date in format "YYYY-MM-DD"
                else if (citizen.birth_date.includes('-')) {
                    formattedDate = citizen.birth_date;
                }
                // Handle date object or timestamp
                else {
                    const date = new Date(citizen.birth_date);
                    if (!isNaN(date.getTime())) {
                        formattedDate = date.toISOString().split('T')[0];
                    }
                }
            } catch (error) {
                console.warn('Error formatting birth date:', error);
            }
            $(heirRow).find('.birth-date').val(formattedDate);
        }

        // Set gender - handle conversion
        let gender = citizen.gender;
        if (typeof gender === 'string') {
            if (gender.toLowerCase() === 'laki-laki') {
                gender = 1;
            } else if (gender.toLowerCase() === 'perempuan') {
                gender = 2;
            }
        }
        $(heirRow).find('select[name="gender[]"]').val(gender).trigger('change');

        // Set religion - handle conversion
        let religion = citizen.religion;
        if (typeof religion === 'string') {
            const religionMap = {
                'islam': 1,
                'kristen': 2,
                'katholik': 3,
                'hindu': 4,
                'buddha': 5,
                'kong hu cu': 6,
                'lainnya': 7
            };
            religion = religionMap[religion.toLowerCase()] || '';
        }
        $(heirRow).find('select[name="religion[]"]').val(religion).trigger('change');

        // Set family status - handle conversion
        let familyStatus = citizen.family_status;
        if (typeof familyStatus === 'string') {
            const statusMap = {
                'anak': 1,
                'kepala keluarga': 2,
                'istri': 3,
                'orang tua': 4,
                'mertua': 5,
                'cucu': 6,
                'famili lain': 7
            };
            familyStatus = statusMap[familyStatus.toLowerCase()] || '';
        }
        $(heirRow).find('select[name="family_status[]"]').val(familyStatus).trigger('change');

        // Set address
        $(heirRow).find('textarea[name="address[]"]').val(citizen.address || '');

        // Set RT field
        $(heirRow).find('input[name="rt[]"]').val(citizen.rt || '');

        // Set RW field
        $(heirRow).find('input[name="rw[]"]').val(citizen.rw || '');
    }
});

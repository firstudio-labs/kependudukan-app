/**
 * Rental House Form Functionality
 */

// Function to handle success and error messages using SweetAlert
function showSweetAlert(type, title, text) {
    Swal.fire({
        icon: type,
        title: title,
        text: text,
        timer: 3000,
        showConfirmButton: false
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Store the loaded citizens for reuse
    let allCitizens = [];

    // Get variables from data attributes
    const formContainer = document.getElementById('rental-house-form-container');
    if (!formContainer) {
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

    // Get location IDs from URL query parameters and set hidden inputs
    const urlParams = new URLSearchParams(window.location.search);
    const provinceId = urlParams.get('province_id');
    const districtId = urlParams.get('district_id');
    const subDistrictId = urlParams.get('sub_district_id');
    const villageId = urlParams.get('village_id');

    // Set hidden input values
    if (provinceId) document.getElementById('province_id').value = provinceId;
    if (districtId) document.getElementById('district_id').value = districtId;
    if (subDistrictId) document.getElementById('subdistrict_id').value = subDistrictId;
    if (villageId) document.getElementById('village_id').value = villageId;

    // Load citizens data with village filter
    $.ajax({
        url: citizenApiRoute,
        type: 'GET',
        dataType: 'json',
        data: {
            limit: 10000,
            village_id: villageId // Tambahkan filter village_id
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
                return;
            }

            allCitizens = processedData;

            // Setup NIK input and name select
            setupNikInput(processedData);
            setupNameSelect(processedData);
            setupResponsibleNameSelect(processedData);
            setupRfIdTagListener(processedData);
        },
        error: function(error) {
            // Error handling removed for cleaner code
        }
    });
});

// Function to setup RF ID Tag listener
function setupRfIdTagListener(citizens) {
    const rfIdInput = document.getElementById('rf_id_tag');
    if (!rfIdInput) return;

    // Remove any existing event listeners by cloning the element
    const newRfIdInput = rfIdInput.cloneNode(true);
    rfIdInput.parentNode.replaceChild(newRfIdInput, rfIdInput);

    // Prevent form submission when Enter is pressed on RF ID input
    newRfIdInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    });

    // Add input event listener with debouncing
    let inputTimeout;
    newRfIdInput.addEventListener('input', function() {
        const rfIdValue = this.value.trim();

        // Clear previous timeout
        clearTimeout(inputTimeout);

        if (rfIdValue.length > 0) {
            // Set a timeout to process the RF ID after a short delay
            inputTimeout = setTimeout(() => {
                processRfIdValue(rfIdValue, citizens, newRfIdInput);
            }, 300); // 300ms delay to prevent immediate processing
        }
    });

    // Handle paste events
    newRfIdInput.addEventListener('paste', function(e) {
        e.preventDefault();
        const pastedText = (e.clipboardData || window.clipboardData).getData('text');
        this.value = pastedText;

        // Process after paste
        setTimeout(() => {
            const rfIdValue = this.value.trim();
            if (rfIdValue.length > 0) {
                processRfIdValue(rfIdValue, citizens, newRfIdInput);
            }
        }, 100);
    });

    // Handle keyup events for RF ID scanner
    newRfIdInput.addEventListener('keyup', function(e) {
        // Prevent form submission on Enter
        if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }

        const rfIdValue = this.value.trim();
        if (rfIdValue.length > 0) {
            // Clear previous timeout
            clearTimeout(inputTimeout);

            // Set a timeout to process the RF ID
            inputTimeout = setTimeout(() => {
                processRfIdValue(rfIdValue, citizens, newRfIdInput);
            }, 200); // 200ms delay for keyup events
        }
    });

    // Function to process RF ID value
    function processRfIdValue(rfIdValue, citizens, inputElement) {
        // Cari data warga dengan RF ID Tag yang sama
        const matchedCitizen = citizens.find(citizen => {
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
            populateCitizenData(matchedCitizen);

            // Set NIK dan nama lengkap secara manual (karena sekarang input text)
            if ($('#nikSelect').length) {
                $('#nikSelect').val(matchedCitizen.nik);
            }
            if ($('#fullNameSelect').length) {
                $('#fullNameSelect').val(matchedCitizen.full_name);
            }

            // Set address jika ada
            if (matchedCitizen.address && $('#address').length) {
                $('#address').val(matchedCitizen.address);
            }

            // Feedback visual berhasil
            $(inputElement).addClass('border-green-500').removeClass('border-red-500 border-gray-300');
            setTimeout(() => {
                $(inputElement).removeClass('border-green-500').addClass('border-gray-300');
            }, 2000);
        } else if (rfIdValue.length >= 5) {
            // Feedback visual tidak ditemukan (hanya untuk input yang cukup panjang)
            $(inputElement).addClass('border-red-500').removeClass('border-green-500 border-gray-300');
            setTimeout(() => {
                $(inputElement).removeClass('border-red-500').addClass('border-gray-300');
            }, 2000);
        }
    }
}

// Function to setup NIK input - FIXED VERSION
function setupNikInput(citizens) {
    const nikInput = document.getElementById('nikSelect'); // FIXED: Use correct ID
    if (!nikInput) {
        return;
    }

    // Add input event listener
    nikInput.addEventListener('input', function() {
        const nikValue = this.value.trim();

        // Only process if NIK is exactly 16 digits
        if (nikValue.length === 16 && /^\d+$/.test(nikValue)) {
            // Find citizen with matching NIK
            const matchedCitizen = citizens.find(citizen => {
                const citizenNik = citizen.nik ? citizen.nik.toString() : '';
                return citizenNik === nikValue;
            });

            if (matchedCitizen) {
                // Fill form fields
                populateCitizenData(matchedCitizen);

                // Update full name input (now as text input, not dropdown)
                if ($('#fullNameSelect').length) {
                    $('#fullNameSelect').val(matchedCitizen.full_name);
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

// Function to setup name select - KODE DROPDOWN DIKOMENTARI
function setupNameSelect(citizens) {
    // --- KODE DROPDOWN NAMA LENGKAP DIKOMENTARI ---
    // Jika sewaktu-waktu dibutuhkan, bisa diaktifkan kembali

    /*
    const nameSelect = document.getElementById('fullNameSelect'); // FIXED: Use correct ID
    if (!nameSelect) {
        return;
    }

    // Create name options array
    const nameOptions = [];

    // Process citizen data for Select2
    citizens.forEach(citizen => {
        if (citizen.full_name) {
            nameOptions.push({
                id: citizen.full_name,
                text: citizen.full_name,
                citizen: citizen
            });
        }
    });

    // Initialize Full Name Select2 dengan minimum input length
    $('#fullNameSelect').select2({
        placeholder: 'Ketik nama untuk mencari...',
        width: '100%',
        data: nameOptions,
        minimumInputLength: 3, // Minimal 3 karakter sebelum dropdown muncul
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
        // Tambahkan delay untuk mengurangi request berlebihan
        delay: 300,
        // Fungsi untuk filter data berdasarkan input
        matcher: function(params, data) {
            // Jika tidak ada input, jangan tampilkan hasil
            if (!params.term) {
                return null;
            }

            // Jika input kurang dari 3 karakter, jangan tampilkan hasil
            if (params.term.length < 3) {
                return null;
            }

            // Cari berdasarkan nama yang mengandung input
            const term = params.term.toLowerCase();
            const text = data.text.toLowerCase();

            if (text.indexOf(term) > -1) {
                return data;
            }

            return null;
        }
    }).on("select2:open", function() {
        // This ensures all options are visible when dropdown opens
        $('.select2-results__options').css('max-height', '400px');
    });

    // When Full Name is selected, fill in other fields
    $('#fullNameSelect').on('select2:select', function (e) {
        const citizen = e.params.data.citizen;
        if (citizen) {
            // Set NIK in input - FIXED: Use correct ID
            const nikValue = citizen.nik ? citizen.nik.toString() : '';
            $('#nikSelect').val(nikValue);

            // Fill other form fields
            populateCitizenData(citizen);
        }
    });
    */
}

// Function to setup responsible name select - LEAVE AS IS
function setupResponsibleNameSelect(citizens) {
    const responsibleNameSelect = document.getElementById('responsibleNameSelect');
    if (!responsibleNameSelect) {
        return;
    }

    // Create name options array
    const nameOptions = [];

    // Process citizen data for Select2
    citizens.forEach(citizen => {
        if (citizen.full_name) {
            nameOptions.push({
                id: citizen.full_name,
                text: citizen.full_name,
                citizen: citizen
            });
        }
    });

    // Initialize Responsible Name Select2 dengan minimum input length
    $('#responsibleNameSelect').select2({
        placeholder: 'Ketik nama penanggung jawab untuk mencari...',
        width: '100%',
        data: nameOptions,
        minimumInputLength: 3, // Minimal 3 karakter sebelum dropdown muncul
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
        // Tambahkan delay untuk mengurangi request berlebihan
        delay: 300,
        // Fungsi untuk filter data berdasarkan input
        matcher: function(params, data) {
            // Jika tidak ada input, jangan tampilkan hasil
            if (!params.term) {
                return null;
            }

            // Jika input kurang dari 3 karakter, jangan tampilkan hasil
            if (params.term.length < 3) {
                return null;
            }

            // Cari berdasarkan nama yang mengandung input
            const term = params.term.toLowerCase();
            const text = data.text.toLowerCase();

            if (text.indexOf(term) > -1) {
                return data;
            }

            return null;
        }
    }).on("select2:open", function() {
        // This ensures all options are visible when dropdown opens
        $('.select2-results__options').css('max-height', '400px');
    });

    // When Responsible Name is selected, fill in other fields (if needed)
    $('#responsibleNameSelect').on('select2:select', function (e) {
        const citizen = e.params.data.citizen;
        if (citizen) {
            // Jika perlu mengisi field lain untuk penanggung jawab, bisa ditambahkan di sini
            // Contoh: $('#responsible_address').val(citizen.address || '');
        }
    });
}

// Function to populate citizen data
function populateCitizenData(citizen) {
    // Set NIK field
    if (citizen.nik) {
        const nikValue = citizen.nik.toString();
        $('#nikSelect').val(nikValue);
    }

    // Set full name field (now as input text, not dropdown)
    if (citizen.full_name) {
        $('#fullNameSelect').val(citizen.full_name);
    }

    // Set address field
    $('#address').val(citizen.address || '');

    // Set RT field
    $('#rt').val(citizen.rt || '');

    // Set RFID field - Auto-fill RFID when NIK is entered
    if (citizen.rf_id_tag && document.querySelector('#rf_id_tag')) {
        document.querySelector('#rf_id_tag').value = citizen.rf_id_tag.toString();
    }

    // Set location IDs if they exist
    if (citizen.province_id) $('#province_id').val(citizen.province_id);
    if (citizen.district_id) $('#district_id').val(citizen.district_id);
    if (citizen.subdistrict_id) $('#subdistrict_id').val(citizen.subdistrict_id);
    if (citizen.village_id) $('#village_id').val(citizen.village_id);
}

// Form validation - only run if form exists
const form = document.querySelector('form');
if (form) {
    form.addEventListener('submit', function(e) {
        // Make sure URL location params are set before submission
        setupLocationFromUrl();

        // Continue with any other validation
        const nikSelect = document.getElementById('nikSelect');
        const nameSelect = document.getElementById('fullNameSelect');

        if (!nikSelect.value || !nameSelect.value) {
            e.preventDefault();
            showSweetAlert('error', 'Form tidak lengkap', 'Pastikan NIK dan nama telah dipilih');
            return false;
        }
    });
}

// Inisialisasi pencarian NIK
if (typeof window.citizenSearchUrl !== 'undefined') {
    fetchCitizens(window.citizenSearchUrl);
}

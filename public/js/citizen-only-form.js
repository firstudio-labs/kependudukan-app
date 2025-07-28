/**
 * Citizen-Only Form Helper
 * Contains functions for handling citizen data without re-fetching location data
 * This is used when location data is already provided from previous steps
 */

// Initialize citizen data (NIK and Name) select fields with Select2
function initializeCitizenSelect(routeUrl, onDataLoaded = null) {
    let isUpdating = false;
    let allCitizens = [];

    // Get village_id from URL parameters or hidden input
    const urlParams = new URLSearchParams(window.location.search);
    const villageId = urlParams.get('village_id') || document.getElementById('village_id')?.value;

    // Kurangi console.log untuk production
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        console.log('Initializing citizen select with village_id:', villageId);
    }

    // Load all citizens first before initializing Select2
    $.ajax({
        url: routeUrl,
        type: 'GET',
        dataType: 'json',
        data: {
            limit: 10000,
            village_id: villageId
        },
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(data) {
            // Kurangi console.log untuk production
            if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                console.log('Citizen data received:', data);
            }

            // Transform the response to match what we expect
            let processedData = data;
            if (data && data.data && Array.isArray(data.data)) {
                processedData = data.data;
            } else if (data && Array.isArray(data)) {
                processedData = data;
            } else if (data && data.data && typeof data.data === 'object' && !Array.isArray(data.data)) {
                // Handle case where data.data is an object with numeric keys
                processedData = Object.values(data.data);
            }

            // Make sure we have valid data
            if (!Array.isArray(processedData)) {
                console.error('Invalid citizen data format:', processedData);
                // Fallback: try to get data without village_id filter
                if (villageId) {
                    console.log('Retrying without village_id filter...');
                    initializeCitizenSelect(routeUrl, onDataLoaded);
                    return;
                }
                return;
            }

            // Kurangi console.log untuk production
            if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                console.log('Processed citizen data count:', processedData.length);
                if (processedData.length > 0) {
                    console.log('Sample citizen data:', processedData[0]);
                }
            }

            allCitizens = processedData;

            // Initialize NIK input listener
            setupNikInputListener(allCitizens);

            // Initialize name select with Select2
            setupNameSelect(allCitizens);

            // Initialize RF ID Tag event listener
            setupRfIdTagListener(allCitizens);

            // If callback provided, call it with the loaded citizen data
            if (typeof onDataLoaded === 'function') {
                onDataLoaded(allCitizens);
            }
        },
        error: function(error) {
            console.error('Error loading citizens:', error);

            // Fallback: try without village_id filter
            if (villageId) {
                console.log('Error occurred, retrying without village_id filter...');
                // Remove village_id from the request
                $.ajax({
                    url: routeUrl,
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
                    success: function(fallbackData) {
                        // Kurangi console.log untuk production
                        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                            console.log('Fallback data received:', fallbackData);
                        }

                        let processedData = fallbackData;
                        if (fallbackData && fallbackData.data && Array.isArray(fallbackData.data)) {
                            processedData = fallbackData.data;
                        } else if (fallbackData && Array.isArray(fallbackData)) {
                            processedData = fallbackData;
                        } else if (fallbackData && fallbackData.data && typeof fallbackData.data === 'object' && !Array.isArray(fallbackData.data)) {
                            processedData = Object.values(fallbackData.data);
                        }

                        if (Array.isArray(processedData)) {
                            allCitizens = processedData;
                            setupNikInputListener(allCitizens);
                            setupNameSelect(allCitizens);
                            setupRfIdTagListener(allCitizens);

                            if (typeof onDataLoaded === 'function') {
                                onDataLoaded(allCitizens);
                            }
                        }
                    },
                    error: function(fallbackError) {
                        console.error('Fallback also failed:', fallbackError);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Error',
                                text: 'Gagal memuat data warga. Silakan coba lagi.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                });
            } else {
                // Show user-friendly error message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Error',
                        text: 'Gagal memuat data warga. Silakan coba lagi.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            }
        }
    });



    // Add RF ID Tag event listener
    function setupRfIdTagListener(citizens) {
        const rfIdInput = document.getElementById('rf_id_tag');
        if (!rfIdInput) return;

        // Tambahkan event untuk input dan paste
        rfIdInput.addEventListener('input', function() {
            const rfIdValue = this.value.trim();
            if (rfIdValue.length > 0) {
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

                    // --- KODE UPDATE DROPDOWN DIKOMENTARI ---
                    // Jika sewaktu-waktu dibutuhkan, bisa diaktifkan kembali
                    /*
                    // Update dropdown NIK dan Nama dengan trigger yang benar
                    if ($('#nikSelect').length) {
                        $('#nikSelect').val(matchedCitizen.nik).trigger('change.select2');
                    }
                    if ($('#fullNameSelect').length) {
                        $('#fullNameSelect').val(matchedCitizen.full_name).trigger('change.select2');
                    }
                    */

                    // Set NIK dan nama lengkap secara manual (karena sekarang input text)
                    if ($('#nikSelect').length) {
                        $('#nikSelect').val(matchedCitizen.nik);
                    }
                    if ($('#fullNameSelect').length) {
                        $('#fullNameSelect').val(matchedCitizen.full_name);
                    }

                    // Set domicile_address jika ada
                    if (matchedCitizen.address && $('#domicile_address').length) {
                        $('#domicile_address').val(matchedCitizen.address);
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

    function setupNikInputListener(citizens) {
        // Setup NIK input listener (for regular input field)
        const nikInput = document.getElementById('nikSelect');
        if (nikInput) {
            // Remove any existing event listeners
            const newNikInput = nikInput.cloneNode(true);
            nikInput.parentNode.replaceChild(newNikInput, nikInput);

            // Add input event listener
            newNikInput.addEventListener('input', function() {
                const nikValue = this.value.trim();

                // Only process if NIK is exactly 16 digits
                if (nikValue.length === 16 && /^\d+$/.test(nikValue)) {
                    // Find citizen with matching NIK
                    const matchedCitizen = citizens.find(citizen => {
                        const citizenNik = citizen.nik ? citizen.nik.toString() : '';
                        return citizenNik === nikValue;
                    });

                    if (matchedCitizen) {
                        // Fill form with citizen data
                        populateCitizenData(matchedCitizen);

                        // Update full name input (now as text input, not dropdown)
                        if ($('#fullNameSelect').length) {
                            $('#fullNameSelect').val(matchedCitizen.full_name);
                        }

                        // Visual feedback for success
                        $(newNikInput).addClass('border-green-500').removeClass('border-red-500 border-gray-300');
                        setTimeout(() => {
                            $(newNikInput).removeClass('border-green-500').addClass('border-gray-300');
                        }, 2000);
                    } else {
                        // Show error alert for NIK not found
                        Swal.fire({
                            title: 'Data Tidak Ditemukan',
                            text: 'NIK yang dimasukkan tidak terdaftar dalam sistem',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });

                        // Visual feedback for error
                        $(newNikInput).addClass('border-red-500').removeClass('border-green-500 border-gray-300');
                        setTimeout(() => {
                            $(newNikInput).removeClass('border-red-500').addClass('border-gray-300');
                        }, 2000);
                    }
                }
            });
        }
    }

    function setupNameSelect(citizens) {
        // --- KODE DROPDOWN NAMA LENGKAP DIKOMENTARI ---
        // Jika sewaktu-waktu dibutuhkan, bisa diaktifkan kembali

        /*
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

        // --- Nonaktifkan fitur search, hanya dropdown saja ---
        $('#fullNameSelect').select2({
            placeholder: 'Pilih Nama Lengkap',
            width: '100%',
            data: nameOptions,
            minimumResultsForSearch: Infinity // Nonaktifkan search box
        }).on("select2:open", function() {
            $('.select2-results__options').css('max-height', '400px');
        });

        // When Full Name is selected, fill in other fields
        $('#fullNameSelect').on('select2:select', function (e) {
            if (isUpdating) return; // Prevent recursion
            isUpdating = true;

            const citizen = e.params.data.citizen;

            if (citizen) {
                // Set NIK in input
                const nikValue = citizen.nik ? citizen.nik.toString() : '';
                $('#nikSelect').val(nikValue);

                // Fill other form fields
                populateCitizenData(citizen);
            }

            isUpdating = false;
        });
        */

        // --- KODE SEARCH SELECT2 DIKOMENTARI, BISA DIAKTIFKAN KEMBALI JIKA PERLU ---
        /*
        $('#fullNameSelect').select2({
            placeholder: 'Ketik nama untuk mencari...',
            width: '100%',
            data: nameOptions,
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
        });
        */
    }
}

// Fill citizen data into form fields
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

    // Fill other form fields - PERSONAL INFO
    $('#birth_place').val(citizen.birth_place || '');

    // Handle birth_date - reformatting if needed
    if (citizen.birth_date) {
        // Check if birth_date is in DD/MM/YYYY format and convert it
        if (citizen.birth_date.includes('/')) {
            const [day, month, year] = citizen.birth_date.split('/');
            $('#birth_date').val(`${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`);
        } else {
            $('#birth_date').val(citizen.birth_date);
        }
    } else {
        $('#birth_date').val('');
    }

    // Set address field
    $('#address').val(citizen.address || '');

    // Handle gender selection - convert string to numeric value
    let gender = citizen.gender;
    if (typeof gender === 'string') {
        // Convert string gender values to numeric
        if (gender.toLowerCase() === 'laki-laki') {
            gender = 1;
        } else if (gender.toLowerCase() === 'perempuan') {
            gender = 2;
        }
    }
    $('#gender').val(gender).trigger('change');

    // Handle religion selection - convert string to numeric value
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
    $('#religion').val(religion).trigger('change');

    // Ensure job_type_id is numeric
    const jobTypeId = parseInt(citizen.job_type_id) || '';
    $('#job_type_id').val(jobTypeId).trigger('change');

    // Handle citizen status conversion properly
    let citizenStatus = citizen.citizen_status;
    if (typeof citizenStatus === 'string') {
        if (citizenStatus.toLowerCase() === 'wna') {
            citizenStatus = 1;
        } else if (citizenStatus.toLowerCase() === 'wni') {
            citizenStatus = 2;
        }
    }
    $('#citizen_status').val(citizenStatus).trigger('change');

    // Convert rt to text if it's a number
    const rt = citizen.rt ? citizen.rt.toString() : '';
    $('#rt').val(rt);

    // Set domicile_address if it exists
    if (citizen.address && document.querySelector('#domicile_address')) {
        document.querySelector('#domicile_address').value = citizen.address;
    }

    // --- KODE UPDATE DROPDOWN NAMA LENGKAP DIKOMENTARI ---
    // Jika sewaktu-waktu dibutuhkan, bisa diaktifkan kembali
    /*
    // Update dropdown NIK dan Nama dengan trigger yang benar
    if ($('#nikSelect').length) {
        $('#nikSelect').val(matchedCitizen.nik).trigger('change.select2');
    }
    if ($('#fullNameSelect').length) {
        $('#fullNameSelect').val(matchedCitizen.full_name).trigger('change.select2');
    }
    */
}

// Form validation without checking location fields
function setupFormValidation() {
    document.querySelector('form').addEventListener('submit', function(e) {
        // No need to validate location fields as they're pre-populated
        // Just add any other validation you need here
    });
}

// Initialize parent (father/mother) select fields for birth certificate forms
function initializeParentSelect(routeUrl, parentType = 'father') {
    let isUpdating = false;
    let allCitizens = [];

    // Load all citizens first before initializing Select2
    $.ajax({
        url: routeUrl,
        type: 'GET',
        dataType: 'json',
        data: {
            limit: 10000 // Increase limit to load more citizens at once
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

            // Now initialize the parent interface
            setupParentInterface(allCitizens, parentType);
        },
        error: function(error) {
            console.error('Error loading citizens data:', error);
        }
    });

    function setupParentInterface(citizens, parentType) {
        // Setup NIK input
        setupParentNikInput(citizens, parentType);

        // Setup name select
        setupParentNameSelect(citizens, parentType);
    }

    function setupParentNikInput(citizens, parentType) {
        const nikInput = document.getElementById(`${parentType}_nik`);
        if (!nikInput) return;

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
                    // Fill parent fields
                    populateParentData(matchedCitizen, parentType);

                    // Update full name select
                    $(`#${parentType}_full_name`).val(matchedCitizen.full_name).trigger('change');

                    // Visual feedback for success
                    $(nikInput).addClass('border-green-500').removeClass('border-red-500 border-gray-300');
                    setTimeout(() => {
                        $(nikInput).removeClass('border-green-500').addClass('border-gray-300');
                    }, 2000);
                } else {
                    // Show error alert for NIK not found
                    Swal.fire({
                        title: 'Data Tidak Ditemukan',
                        text: `NIK ${parentType === 'father' ? 'Ayah' : 'Ibu'} tidak terdaftar dalam sistem`,
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

    function setupParentNameSelect(citizens, parentType) {
        const nameSelect = document.getElementById(`${parentType}_full_name`);
        if (!nameSelect) return;

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

        // Initialize Full Name Select2
        $(`#${parentType}_full_name`).select2({
            placeholder: `Pilih Nama ${parentType === 'father' ? 'Ayah' : 'Ibu'}`,
            width: '100%',
            data: nameOptions,
            language: {
                noResults: function() {
                    return 'Tidak ada data yang ditemukan';
                },
                searching: function() {
                    return 'Mencari...';
                }
            }
        }).on("select2:open", function() {
            // This ensures all options are visible when dropdown opens
            $('.select2-results__options').css('max-height', '400px');
        });

        // When Full Name is selected, fill in other fields
        $(`#${parentType}_full_name`).on('select2:select', function (e) {
            if (isUpdating) return; // Prevent recursion
            isUpdating = true;

            const citizen = e.params.data.citizen;

            if (citizen) {
                // Set NIK in input
                const nikValue = citizen.nik ? citizen.nik.toString() : '';
                $(`#${parentType}_nik`).val(nikValue);

                // Fill other form fields
                populateParentData(citizen, parentType);
            }

            isUpdating = false;
        });
    }
}

// Fill parent data for birth certificate forms
function populateParentData(citizen, parentType) {
    // Fill fields based on parent type (father or mother)
    $(`#${parentType}_birth_place`).val(citizen.birth_place || '');

    // Handle birth_date - reformatting if needed
    if (citizen.birth_date) {
        // Check if birth_date is in DD/MM/YYYY format and convert it
        if (citizen.birth_date.includes('/')) {
            const [day, month, year] = citizen.birth_date.split('/');
            $(`#${parentType}_birth_date`).val(`${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`);
        } else {
            $(`#${parentType}_birth_date`).val(citizen.birth_date);
        }
    } else {
        $(`#${parentType}_birth_date`).val('');
    }

    // Set address field
    $(`#${parentType}_address`).val(citizen.address || '');

    // Handle religion selection - convert string to numeric value
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
    $(`#${parentType}_religion`).val(religion).trigger('change');

    // Ensure job_type_id is numeric
    const jobTypeId = parseInt(citizen.job_type_id) || '';
    $(`#${parentType}_job`).val(jobTypeId).trigger('change');
}

/**
 * Initialize the signing officer dropdown with data from the penandatangan table
 * @param {string} endpointUrl - URL for fetching signing officers data (optional)
 */
function initializeSigningDropdown(endpointUrl) {
    const signingSelect = document.getElementById('signing');

    if (!signingSelect) return;

    // Check if we have the signer data in the global scope
    if (typeof signerOptions !== 'undefined' && signerOptions && signerOptions.length > 0) {
        // Clear existing options except the first one
        signingSelect.innerHTML = '<option value="">Pilih Pejabat Penandatangan</option>';

        // Add options from the signerOptions global variable
        signerOptions.forEach(signer => {
            const option = document.createElement('option');
            // Use judul as the value instead of ID
            option.value = signer.judul;

            // Different databases might use different field names
            const title = signer.judul || signer.name || '';
            const desc = signer.keterangan || signer.position || '';

            option.textContent = title + (desc ? ' - ' + desc : '');
            signingSelect.appendChild(option);
        });
    } else if (endpointUrl) {
        // If we don't have the data in the global scope, try to fetch it
        fetch(endpointUrl)
            .then(response => response.json())
            .then(data => {
                // Clear existing options
                signingSelect.innerHTML = '<option value="">Pilih Pejabat Penandatangan</option>';

                if (Array.isArray(data) && data.length > 0) {
                    // Add fetched options
                    data.forEach(signer => {
                        const option = document.createElement('option');
                        // Use judul as the value instead of ID
                        option.value = signer.judul;
                        const title = signer.judul || signer.name || '';
                        const desc = signer.keterangan || signer.position || '';
                        option.textContent = title + (desc ? ' - ' + desc : '');
                        signingSelect.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching signing officers:', error);
            });
    }
}

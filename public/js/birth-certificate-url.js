/**
 * Birth Certificate Form Handler
 * Uses hidden location fields populated from URL parameters
 */

document.addEventListener('DOMContentLoaded', function() {
    // Flag to prevent recursive updates
    let isFatherUpdating = false;
    let isMotherUpdating = false;
    let allCitizens = [];

    // Get form container and API route
    const formContainer = document.getElementById('birth-form-container');
    const citizenApiRoute = formContainer.dataset.citizenRoute;
    const success = formContainer.dataset.success;
    const error = formContainer.dataset.error;

    // Show notifications if needed
    if (success) showAlert('success', success);
    if (error) showAlert('error', error);

    // Get location IDs from URL query parameters
    const urlParams = new URLSearchParams(window.location.search);
    const provinceId = urlParams.get('province_id');
    const districtId = urlParams.get('district_id');
    const subDistrictId = urlParams.get('sub_district_id');
    const villageId = urlParams.get('village_id');

    // Set form hidden input values
    if (provinceId) document.getElementById('province_id').value = provinceId;
    if (districtId) document.getElementById('district_id').value = districtId;
    if (subDistrictId) document.getElementById('subdistrict_id').value = subDistrictId;
    if (villageId) document.getElementById('village_id').value = villageId;

    // Load citizens data
    $.ajax({
        url: citizenApiRoute,
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
            let citizens = [];
            if (data && data.data && Array.isArray(data.data)) {
                citizens = data.data;
            } else if (Array.isArray(data)) {
                citizens = data;
            }

            allCitizens = citizens;

            // Setup NIK inputs for both parents
            setupParentNikInput('father', citizens);
            setupParentNikInput('mother', citizens);

            // Setup name selects with data
            setupParentNameSelect('father', citizens);
            setupParentNameSelect('mother', citizens);

            // Add RF ID Tag event listeners for both parents
            setupRfIdTagListener('father', citizens);
            setupRfIdTagListener('mother', citizens);
        },
        error: function(error) {
            console.error('Error loading citizens data:', error);
        }
    });

    function setupParentNameSelect(parentType, citizens) {
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
        $(`#${parentType}_full_name`).select2({
            placeholder: `Ketik nama ${parentType === 'father' ? 'Ayah' : 'Ibu'} untuk mencari...`,
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
        $(`#${parentType}_full_name`).on('select2:select', function (e) {
            if (isFatherUpdating && parentType === 'father') return;
            if (isMotherUpdating && parentType === 'mother') return;

            if (parentType === 'father') isFatherUpdating = true;
            if (parentType === 'mother') isMotherUpdating = true;

            const citizen = e.params.data.citizen;

            if (citizen) {
                // Set NIK in input
                const nikValue = citizen.nik ? citizen.nik.toString() : '';
                $(`#${parentType}_nik`).val(nikValue);

                // Fill other form fields
                populateParentFields(citizen, parentType);

                // Also set child address if it's empty
                if (!$('#child_address').val() && citizen.address) {
                    $('#child_address').val(citizen.address);
                }
            }

            if (parentType === 'father') isFatherUpdating = false;
            if (parentType === 'mother') isMotherUpdating = false;
        });
    }

    // Add RF ID Tag event listener for specific parent
    function setupRfIdTagListener(parentType, citizens) {
        const rfIdInput = document.getElementById(`${parentType}_rf_id_tag`);
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
                    // Fill parent fields
                    populateParentFields(matchedCitizen, parentType);

                    // Update dropdown NIK dan Nama dengan trigger yang benar
                    if ($(`#${parentType}_nik`).length) {
                        $(`#${parentType}_nik`).val(matchedCitizen.nik).trigger('change.select2');
                    }
                    if ($(`#${parentType}_full_name`).length) {
                        $(`#${parentType}_full_name`).val(matchedCitizen.full_name).trigger('change.select2');
                    }

                    // Set child address if it's empty
                    if (!$('#child_address').val() && matchedCitizen.address) {
                        $('#child_address').val(matchedCitizen.address);
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

    function populateParentFields(citizen, parentType) {
        // Birth place
        $(`#${parentType}_birth_place`).val(citizen.birth_place || '');

        // Birth date - handle formatting
        if (citizen.birth_date) {
            let birthDate = citizen.birth_date;
            if (birthDate.includes('/')) {
                const [day, month, year] = birthDate.split('/');
                birthDate = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
            }
            $(`#${parentType}_birth_date`).val(birthDate);
        }

        // Address
        $(`#${parentType}_address`).val(citizen.address || '');

        // Religion - handle conversion
        let religion = citizen.religion;
        if (typeof religion === 'string') {
            const religionMap = {
                'islam': 1, 'kristen': 2, 'katholik': 3, 'hindu': 4,
                'buddha': 5, 'kong hu cu': 6, 'lainnya': 7
            };
            religion = religionMap[religion.toLowerCase()] || '';
        }
        $(`#${parentType}_religion`).val(religion).trigger('change');

        // Job
        $(`#${parentType}_job`).val(citizen.job_type_id || '').trigger('change');
    }
});

// Function to handle success and error messages using SweetAlert
function showAlert(type, text) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: type,
            title: type === 'success' ? 'Sukses!' : 'Gagal!',
            text: text,
            timer: 3000,
            showConfirmButton: false
        });
    } else {
        alert(text);
    }
}

// Add this function to handle NIK input for both parents
function setupParentNikInput(parentType, citizens) {
    const nikInput = document.getElementById(`${parentType}_nik`);
    if (!nikInput) return;

    // Remove Select2 if it exists
    if ($(nikInput).hasClass('select2-hidden-accessible')) {
        $(nikInput).select2('destroy');
    }

    // Convert to regular input
    nikInput.type = 'text';
    nikInput.placeholder = `Masukkan NIK ${parentType === 'father' ? 'Ayah' : 'Ibu'} (16 digit)`;

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
                populateParentFields(matchedCitizen, parentType);

                // Update full name select
                $(`#${parentType}_full_name`).val(matchedCitizen.full_name).trigger('change.select2');

                // Also set child address if it's empty
                if (!$('#child_address').val() && matchedCitizen.address) {
                    $('#child_address').val(matchedCitizen.address);
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

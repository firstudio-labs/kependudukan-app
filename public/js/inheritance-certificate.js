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
        showConfirmButton: false
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Store the loaded citizens for reuse
    let allCitizens = [];

    // Get variables passed from PHP
    const citizenApiRoute = document.getElementById('inheritance-form-container').dataset.citizenRoute;
    const provinces = JSON.parse(document.getElementById('inheritance-form-container').dataset.provinces);

    // Check for flash messages
    const successMessage = document.getElementById('inheritance-form-container').dataset.success;
    const errorMessage = document.getElementById('inheritance-form-container').dataset.error;

    if(successMessage) {
        showSweetAlert('success', 'Sukses!', successMessage);
    }

    if(errorMessage) {
        showSweetAlert('error', 'Gagal!', errorMessage);
    }

    // Determine admin village id (admin desa) to filter citizens
    const adminVillageIdEl = document.getElementById('admin_village_id');
    const adminVillageId = adminVillageIdEl ? adminVillageIdEl.value : null;

    // Load all citizens first before initializing heir rows
    $.ajax({
        url: citizenApiRoute,
        type: 'GET',
        dataType: 'json',
        data: (function(){
            const payload = { limit: 10000 };
            if (adminVillageId) { payload.village_id = adminVillageId; }
            return payload;
        })(),
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

            // Now setup the heirs interface
            setupHeirsInterface();
        },
        error: function(error) {
            console.error('Failed to load citizen data:', error);
            // Setup the heirs interface anyway with empty data
            setupHeirsInterface();
        }
    });

    function setupHeirsInterface() {
        // Setup location dropdowns using the imported function
        setupLocationDropdowns(provinces);

        // Handle adding more heirs
        const heirsContainer = document.getElementById('heirs-container');
        const addHeirButton = document.getElementById('add-heir');
        const firstHeirRow = document.querySelector('.heir-row').cloneNode(true);

        // Remove the first heir row template
        heirsContainer.innerHTML = '';

        // Initialize one heir row with input listeners (NIK and RF ID)
        function initializeHeirRowInputs(heirRow) {
            const nikInput = heirRow.querySelector('.nik-select');
            const nameInput = heirRow.querySelector('.fullname-select');
            const rfIdInput = heirRow.querySelector('.rf-id-tag');

            if (nikInput) {
                // Replace to remove stale listeners
                const newNik = nikInput.cloneNode(true);
                nikInput.parentNode.replaceChild(newNik, nikInput);

                newNik.addEventListener('input', function() {
                    const val = this.value.trim();
                    if (val.length === 16 && /^\d+$/.test(val)) {
                        const citizen = allCitizens.find(c => (c.nik ? c.nik.toString() : '') === val);
                        if (citizen) {
                            populateHeirFieldsFromCitizen($(heirRow), citizen);
                            if (nameInput) nameInput.value = citizen.full_name || '';
                            if (rfIdInput) {
                                const rfVal = (citizen.rf_id_tag ?? citizen.rfid ?? citizen.rf_id) || '';
                                rfIdInput.value = rfVal ? rfVal.toString() : '';
                                if (rfVal) {
                                    $(rfIdInput).addClass('border-green-500').removeClass('border-red-500 border-gray-300');
                                    setTimeout(() => { $(rfIdInput).removeClass('border-green-500').addClass('border-gray-300'); }, 2000);
                                }
                            }
                            if (!$('#province_id').val() || !$('#district_id').val() || !$('#subdistrict_id').val() || !$('#village_id').val()) {
                                populateLocationDropdowns(
                                    citizen.province_id,
                                    citizen.district_id,
                                    citizen.subdistrict_id || citizen.sub_district_id,
                                    citizen.village_id
                                );
                            }
                            $(this).addClass('border-green-500').removeClass('border-red-500 border-gray-300');
                            setTimeout(() => { $(this).removeClass('border-green-500').addClass('border-gray-300'); }, 2000);
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({ title: 'Data Tidak Ditemukan', text: 'NIK tidak terdaftar atau bukan warga desa ini', icon: 'error', confirmButtonText: 'OK' });
                            }
                            // Clear current row fields when not found
                            clearHeirRowFields(heirRow, { keepNik: true });
                            $(this).addClass('border-red-500').removeClass('border-green-500 border-gray-300');
                            setTimeout(() => { $(this).removeClass('border-red-500').addClass('border-gray-300'); }, 2000);
                        }
                    } else if (val.length < 16) {
                        // While editing/incomplete, avoid leftover data
                        clearHeirRowFields(heirRow, { keepNik: true });
                    }
                });
            }

            if (rfIdInput) {
                const newRf = rfIdInput.cloneNode(true);
                rfIdInput.parentNode.replaceChild(newRf, rfIdInput);

                let inputTimeout;
                newRf.addEventListener('keydown', function(e){ if (e.key === 'Enter') { e.preventDefault(); e.stopPropagation(); }});
                newRf.addEventListener('input', function(){
                    const rf = this.value.trim();
                    clearTimeout(inputTimeout);
                    if (rf.length > 0) {
                        inputTimeout = setTimeout(() => { processRfId(rf, heirRow, newRf); }, 300);
                    }
                });
                newRf.addEventListener('paste', function(e){
                    e.preventDefault();
                    const t = (e.clipboardData || window.clipboardData).getData('text');
                    this.value = t;
                    setTimeout(() => { const rf = this.value.trim(); if (rf.length > 0) { processRfId(rf, heirRow, newRf); } }, 100);
                });
                newRf.addEventListener('keyup', function(e){
                    if (e.key === 'Enter') { e.preventDefault(); e.stopPropagation(); return; }
                    const rf = this.value.trim();
                    if (rf.length > 0) {
                        clearTimeout(inputTimeout);
                        inputTimeout = setTimeout(() => { processRfId(rf, heirRow, newRf); }, 200);
                    }
                });
            }
        }

        function normalizeRfId(rfId) {
            if (!rfId) return '';
            let v = rfId.toString();
            v = v.replace(/^0+/, '');
            v = v.replace(/[^0-9]/g, '');
            return v.trim();
        }

        function processRfId(rfIdValue, heirRow, inputEl) {
            const matched = allCitizens.find(citizen => {
                if (!citizen.rf_id_tag) return false;
                const a = normalizeRfId(rfIdValue);
                const b = normalizeRfId(citizen.rf_id_tag);
                const exact = a === b;
                const partial = b.includes(a) && a.length >= 5;
                const reverse = a.includes(b) && b.length >= 5;
                return exact || partial || reverse;
            });

            if (matched) {
                populateHeirFieldsFromCitizen($(heirRow), matched);
                const nikInput = heirRow.querySelector('.nik-select');
                const nameInput = heirRow.querySelector('.fullname-select');
                if (nikInput) nikInput.value = matched.nik ? matched.nik.toString() : '';
                if (nameInput) nameInput.value = matched.full_name || '';
                $(inputEl).addClass('border-green-500').removeClass('border-red-500 border-gray-300');
                setTimeout(() => { $(inputEl).removeClass('border-green-500').addClass('border-gray-300'); }, 2000);
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ title: 'Data Tidak Ditemukan', text: 'RF ID tidak terdaftar', icon: 'error', confirmButtonText: 'OK' });
                }
                // Clear current row fields on RF not found (keep RFID typed value)
                clearHeirRowFields(heirRow, { keepNik: false, keepRfId: true });
                $(inputEl).addClass('border-red-500').removeClass('border-green-500 border-gray-300');
                setTimeout(() => { $(inputEl).removeClass('border-red-500').addClass('border-gray-300'); }, 2000);
            }
        }

        // Function to populate heir fields from citizen data
        function populateHeirFieldsFromCitizen(row, citizen) {
            // Birth place
            $(row).find('.birth-place').val(citizen.birth_place || '');

            // Birth date - handle formatting
            if (citizen.birth_date) {
                let birthDate = citizen.birth_date;
                if (birthDate.includes('/')) {
                    const [day, month, year] = birthDate.split('/');
                    birthDate = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
                }
                $(row).find('.birth-date').val(birthDate);
            }

            // Gender - handle conversion
            let gender = citizen.gender;
            if (typeof gender === 'string') {
                if (gender.toLowerCase() === 'laki-laki') {
                    gender = 1;
                } else if (gender.toLowerCase() === 'perempuan') {
                    gender = 2;
                }
            }
            $(row).find('.gender').val(gender).trigger('change');

            // Religion - handle conversion
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
            $(row).find('.religion').val(religion).trigger('change');

            // Address
            $(row).find('.address').val(citizen.address || '');

            // Family Status - handle numeric values from API
            if (citizen.family_status !== undefined && citizen.family_status !== null) {
                let familyStatusValue = citizen.family_status;

                // If it's a string that contains a number, convert it to number
                if (typeof familyStatusValue === 'string' && !isNaN(parseInt(familyStatusValue))) {
                    familyStatusValue = parseInt(familyStatusValue);
                }
                // If it's a string with text, try to map it to corresponding number
                else if (typeof familyStatusValue === 'string') {
                    const statusMap = {
                        'anak': 1,
                        'kepala keluarga': 2,
                        'istri': 3,
                        'orang tua': 4,
                        'mertua': 5,
                        'cucu': 6,
                        'famili lain': 7
                    };

                    const normalizedStatus = familyStatusValue.toLowerCase().trim();
                    if (statusMap[normalizedStatus] !== undefined) {
                        familyStatusValue = statusMap[normalizedStatus];
                    }
                }

                // Set the numeric value in the dropdown
                if (!isNaN(familyStatusValue) && familyStatusValue > 0) {
                    $(row).find('select[name="family_status[]"]').val(familyStatusValue).trigger('change');
                }
            }
        }

        // Function to add a new heir row
        function addHeirRow() {
            const heirRowClone = firstHeirRow.cloneNode(true);
            heirsContainer.appendChild(heirRowClone);

            // Initialize input listeners on the new row
            initializeHeirRowInputs(heirRowClone);

            // Remove heir button functionality
            heirRowClone.querySelector('.remove-heir').addEventListener('click', function() {
                if (document.querySelectorAll('#heirs-container .heir-row').length > 1) {
                    this.closest('.heir-row').remove();
                } else {
                    alert('Minimal harus ada satu ahli waris');
                }
            });
        }

        // Add first heir row by default
        addHeirRow();

        // Helper to clear fields in a row
        function clearHeirRowFields(heirRow, options = {}) {
            const keepNik = options.keepNik || false;
            const keepRfId = options.keepRfId || false;
            const $row = $(heirRow);
            if (!keepNik) { $row.find('.nik-select').val(''); }
            if (!keepRfId) { $row.find('.rf-id-tag').val(''); }
            $row.find('.fullname-select').val('');
            $row.find('.birth-place').val('');
            $row.find('.birth-date').val('');
            $row.find('.gender').val('').trigger('change');
            $row.find('.religion').val('').trigger('change');
            $row.find('.address').val('');
            $row.find('select[name="family_status[]"]').val('').trigger('change');
        }

        // Add heir button click handler
        addHeirButton.addEventListener('click', addHeirRow);

        // Form validation for required fields and location data
        if (typeof setupFormValidation === 'function') {
            setupFormValidation();
        }

        // Make the signing dropdown easier to use with basic styling
        $('#signing').css('width', '100%');
    }
});

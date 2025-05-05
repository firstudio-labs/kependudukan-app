/**
 * Inheritance Certificate Form Handler
 * Uses hidden location fields populated from URL parameters
 */

document.addEventListener('DOMContentLoaded', function() {
    // Store the loaded citizens for reuse
    let allCitizens = [];

    // Get form container and API route
    const formContainer = document.getElementById('inheritance-form-container');
    const citizenApiRoute = formContainer.dataset.citizenRoute;
    const success = formContainer.dataset.success;
    const error = formContainer.dataset.error;

    console.log("API Route:", citizenApiRoute);

    // Show notifications if needed
    if (success) {
        showSweetAlert('success', 'Sukses!', success);
    }

    if (error) {
        showSweetAlert('error', 'Gagal!', error);
    }

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

    // Setup the heirs interface
    setupHeirsInterface();

    // Setup the heirs interface with the citizen data
    function setupHeirsInterface() {
        // Get heirs container and template
        const heirsContainer = document.getElementById('heirs-container');
        const heirTemplate = document.querySelector('.heir-row');

        // Clear the container
        heirsContainer.innerHTML = '';

        // Add first heir row
        addHeirRow();

        // Add button click handler
        document.getElementById('add-heir').addEventListener('click', addHeirRow);

        // Function to add a new heir row
        function addHeirRow() {
            // Clone template
            const newRow = heirTemplate.cloneNode(true);
            heirsContainer.appendChild(newRow);

            // Initialize Select2 for this row
            initializeHeirSelect2(newRow);

            // Add remove button handler
            newRow.querySelector('.remove-heir').addEventListener('click', function() {
                const allRows = document.querySelectorAll('#heirs-container .heir-row');
                if (allRows.length > 1) {
                    this.closest('.heir-row').remove();
                } else {
                    alert('Minimal harus ada satu ahli waris');
                }
            });
        }
    }

    // Initialize Select2 dropdowns for an heir row
    function initializeHeirSelect2(heirRow) {
        const nikSelect = heirRow.querySelector('.nik-select');
        const nameSelect = heirRow.querySelector('.fullname-select');
        let isUpdating = false;

        // Initialize NIK select with AJAX
        $(nikSelect).select2({
            placeholder: 'Ketik untuk mencari NIK',
            width: '100%',
            allowClear: true,
            minimumInputLength: 1,
            ajax: {
                url: citizenApiRoute,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term || '',
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;

                    let processedData = [];
                    if (data && data.data && Array.isArray(data.data)) {
                        processedData = data.data;
                    } else if (Array.isArray(data)) {
                        processedData = data;
                    }

                    return {
                        results: processedData.map(citizen => ({
                            id: citizen.nik?.toString() || '',
                            text: citizen.nik?.toString() || '',
                            citizen: citizen
                        })).filter(item => item.id !== ''),
                        pagination: {
                            more: (params.page * 10) < (data.total || 1000)
                        }
                    };
                },
                cache: true
            },
            language: {
                inputTooShort: function() {
                    return 'Ketik minimal 1 karakter untuk mencari NIK...';
                },
                noResults: function() {
                    return 'Tidak ada data yang ditemukan';
                },
                searching: function() {
                    return 'Mencari...';
                }
            }
        });

        // Initialize Name select with AJAX
        $(nameSelect).select2({
            placeholder: 'Ketik untuk mencari nama',
            width: '100%',
            allowClear: true,
            minimumInputLength: 1,
            ajax: {
                url: citizenApiRoute,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term || '',
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;

                    let processedData = [];
                    if (data && data.data && Array.isArray(data.data)) {
                        processedData = data.data;
                    } else if (Array.isArray(data)) {
                        processedData = data;
                    }

                    return {
                        results: processedData.map(citizen => ({
                            id: citizen.full_name || '',
                            text: citizen.full_name || '',
                            citizen: citizen
                        })).filter(item => item.id !== ''),
                        pagination: {
                            more: (params.page * 10) < (data.total || 1000)
                        }
                    };
                },
                cache: true
            },
            language: {
                inputTooShort: function() {
                    return 'Ketik minimal 1 karakter untuk mencari nama...';
                },
                noResults: function() {
                    return 'Tidak ada data yang ditemukan';
                },
                searching: function() {
                    return 'Mencari...';
                }
            }
        });

        // NIK select change handler
        $(nikSelect).on('select2:select', function(e) {
            if (isUpdating) return;
            isUpdating = true;

            const citizen = e.params.data.citizen;
            if (citizen) {
                // Update name select
                $(nameSelect).val(citizen.full_name).trigger('change.select2');

                // Fill heir fields
                populateHeirFields(heirRow, citizen);
            }

            isUpdating = false;
        });

        // Name select change handler
        $(nameSelect).on('select2:select', function(e) {
            if (isUpdating) return;
            isUpdating = true;

            const citizen = e.params.data.citizen;
            if (citizen) {
                // Update NIK select
                $(nikSelect).val(citizen.nik ? citizen.nik.toString() : '').trigger('change.select2');

                // Fill heir fields
                populateHeirFields(heirRow, citizen);
            }

            isUpdating = false;
        });
    }

    // Populate heir fields from citizen data
    function populateHeirFields(rowElement, citizen) {
        // Birth place
        $(rowElement).find('.birth-place').val(citizen.birth_place || '');

        // Birth date - handle formatting
        if (citizen.birth_date) {
            let birthDate = citizen.birth_date;
            if (birthDate.includes('/')) {
                const [day, month, year] = birthDate.split('/');
                birthDate = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
            }
            $(rowElement).find('.birth-date').val(birthDate);
        }

        // Gender - handle conversion
        let gender = citizen.gender;
        if (typeof gender === 'string') {
            if (gender.toLowerCase() === 'laki-laki') gender = 1;
            else if (gender.toLowerCase() === 'perempuan') gender = 2;
        }
        $(rowElement).find('.gender').val(gender);

        // Religion - handle conversion
        let religion = citizen.religion;
        if (typeof religion === 'string') {
            const religionMap = {
                'islam': 1, 'kristen': 2, 'katholik': 3, 'hindu': 4,
                'buddha': 5, 'kong hu cu': 6, 'lainnya': 7
            };
            religion = religionMap[religion.toLowerCase()] || '';
        }
        $(rowElement).find('.religion').val(religion);

        // Address
        $(rowElement).find('.address').val(citizen.address || '');

        // Family status - handle conversion
        let familyStatus = citizen.family_status;
        if (typeof familyStatus === 'string') {
            const statusMap = {
                'anak': 1, 'kepala keluarga': 2, 'istri': 3, 'orang tua': 4,
                'mertua': 5, 'cucu': 6, 'famili lain': 7
            };
            const normalizedStatus = familyStatus.toLowerCase().trim();
            if (statusMap[normalizedStatus]) {
                familyStatus = statusMap[normalizedStatus];
            }
        }
        $(rowElement).find('.family-status').val(familyStatus || '');
    }

    // Form validation
    $('form').on('submit', function(e) {
        // Check required fields for each heir
        const heirRows = document.querySelectorAll('#heirs-container .heir-row');
        let isValid = true;

        heirRows.forEach((row, index) => {
            const requiredFields = [
                { name: 'nik[]', label: `NIK ahli waris #${index + 1}` },
                { name: 'full_name[]', label: `Nama ahli waris #${index + 1}` },
                { name: 'birth_place[]', label: `Tempat lahir ahli waris #${index + 1}` },
                { name: 'birth_date[]', label: `Tanggal lahir ahli waris #${index + 1}` },
                { name: 'gender[]', label: `Jenis kelamin ahli waris #${index + 1}` },
                { name: 'religion[]', label: `Agama ahli waris #${index + 1}` },
                { name: 'family_status[]', label: `Hubungan keluarga ahli waris #${index + 1}` },
                { name: 'address[]', label: `Alamat ahli waris #${index + 1}` }
            ];

            requiredFields.forEach(field => {
                const element = row.querySelector(`[name="${field.name}"]`);
                if (!element || !element.value) {
                    isValid = false;
                    alert(`Field ${field.label} harus diisi`);
                }
            });
        });

        // Check other required fields
        const otherRequiredFields = [
            'heir_name', 'deceased_name', 'death_place',
            'death_date', 'inheritance_type'
        ];

        otherRequiredFields.forEach(field => {
            const value = $(this).find(`[name="${field}"]`).val();
            if (!value || value === '') {
                isValid = false;
                alert(`Field ${field.replace('_', ' ')} harus diisi`);
            }
        });

        // Check location fields
        const provinceId = $('#province_id').val();
        const districtId = $('#district_id').val();
        const subDistrictId = $('#subdistrict_id').val();
        const villageId = $('#village_id').val();

        if (!provinceId || !districtId || !subDistrictId || !villageId) {
            isValid = false;
            alert('Data lokasi tidak lengkap');
        }

        if (!isValid) {
            e.preventDefault();
            return false;
        }
    });
});

// Function to handle success and error messages using SweetAlert
function showSweetAlert(type, title, text) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: type,
            title: title,
            text: text,
            timer: 3000,
            showConfirmButton: false
        });
    } else {
        alert(text);
    }
}

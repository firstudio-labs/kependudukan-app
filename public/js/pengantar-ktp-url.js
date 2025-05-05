/**
 * Pengantar KTP Form Handler - URL Parameter Version
 * Uses hidden location fields populated from URL parameters
 */

document.addEventListener('DOMContentLoaded', function() {
    // Store the loaded citizens for reuse
    let allCitizens = [];

    // Flag to prevent recursive updates
    let isUpdating = false;

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

    // Initialize Select2 for NIK and Full Name selects
    $('#nikSelect').select2({
        placeholder: 'Pilih NIK',
        width: '100%',
        language: {
            noResults: function() { return 'Tidak ada data yang ditemukan'; },
            searching: function() { return 'Mencari...'; }
        }
    });

    $('#fullNameSelect').select2({
        placeholder: 'Pilih Nama Lengkap',
        width: '100%',
        language: {
            noResults: function() { return 'Tidak ada data yang ditemukan'; },
            searching: function() { return 'Mencari...'; }
        }
    });

    // Load all citizens data
    $.ajax({
        url: CITIZENS_URL,
        type: 'GET',
        dataType: 'json',
        data: { limit: 10000 },
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(data) {
            // Process the data
            let processedData = Array.isArray(data) ? data :
                               (data.data && Array.isArray(data.data) ? data.data : []);

            allCitizens = processedData;

            // Initialize dropdowns with data
            populateCitizenSelects(processedData);
        },
        error: function(error) {
            console.error('Error fetching citizens:', error);
            populateCitizenSelects([]);
        }
    });

    function populateCitizenSelects(citizens) {
        // Process citizens for select options
        const nikOptions = [];
        const nameOptions = [];

        citizens.forEach(citizen => {
            if (citizen.nik) {
                nikOptions.push({
                    id: citizen.nik.toString(),
                    text: citizen.nik.toString(),
                    citizen: citizen
                });
            }

            if (citizen.full_name) {
                nameOptions.push({
                    id: citizen.full_name,
                    text: citizen.full_name,
                    citizen: citizen
                });
            }
        });

        // Set options for NIK select
        $('#nikSelect').empty().select2({
            data: nikOptions,
            placeholder: 'Pilih NIK',
            width: '100%'
        });

        // Set options for Full Name select
        $('#fullNameSelect').empty().select2({
            data: nameOptions,
            placeholder: 'Pilih Nama Lengkap',
            width: '100%'
        });

        // Set up change handlers
        setupChangeHandlers();
    }

    function setupChangeHandlers() {
        // NIK select change handler
        $('#nikSelect').on('select2:select', function(e) {
            if (isUpdating) return;
            isUpdating = true;

            const citizen = e.params.data.citizen;
            if (citizen) {
                // Update fullName select
                $('#fullNameSelect').val(citizen.full_name).trigger('change.select2');

                // Fill other form fields
                $('#kk').val(citizen.kk || '');
                $('#rt').val(citizen.rt || '');
                $('#rw').val(citizen.rw || '');
                $('#hamlet').val(citizen.hamlet || citizen.dusun || '');
                $('#address').val(citizen.address || '');
            }

            isUpdating = false;
        });

        // Full name select change handler
        $('#fullNameSelect').on('select2:select', function(e) {
            if (isUpdating) return;
            isUpdating = true;

            const citizen = e.params.data.citizen;
            if (citizen) {
                // Update NIK select
                $('#nikSelect').val(citizen.nik ? citizen.nik.toString() : '').trigger('change.select2');

                // Fill other form fields
                $('#kk').val(citizen.kk || '');
                $('#rt').val(citizen.rt || '');
                $('#rw').val(citizen.rw || '');
                $('#hamlet').val(citizen.hamlet || citizen.dusun || '');
                $('#address').val(citizen.address || '');
            }

            isUpdating = false;
        });
    }

    // Form validation
    $('form').on('submit', function(e) {
        // Check required fields
        const requiredFields = ['nik', 'full_name', 'kk', 'rt', 'rw', 'hamlet', 'address', 'application_type'];
        const missingFields = [];

        requiredFields.forEach(field => {
            const value = $(this).find(`[name="${field}"]`).val();
            if (!value || value === '') {
                missingFields.push(field);
            }
        });

        // Check location fields
        const provinceId = $('#province_id').val();
        const districtId = $('#district_id').val();
        const subDistrictId = $('#subdistrict_id').val();
        const villageId = $('#village_id').val();

        if (!provinceId || !districtId || !subDistrictId || !villageId) {
            missingFields.push('location data');
        }

        if (missingFields.length > 0) {
            e.preventDefault();
            alert('Mohon lengkapi semua data yang diperlukan: ' + missingFields.join(', '));
            return false;
        }
    });

    // Show success/error messages if available
    if (SUCCESS_MESSAGE) {
        showAlert('success', SUCCESS_MESSAGE);
    }
    if (ERROR_MESSAGE) {
        showAlert('error', ERROR_MESSAGE);
    }
});

// Alert function (SweetAlert if available, or regular alert)
function showAlert(type, message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: type,
            title: type === 'success' ? 'Berhasil!' : 'Error!',
            text: message,
            timer: 3000,
            showConfirmButton: false
        });
    } else {
        alert(message);
    }
}

/**
 * Rental House Form Handler
 * Uses hidden location fields populated from URL parameters
 */

document.addEventListener('DOMContentLoaded', function() {
    // Store the loaded citizens for reuse
    let allCitizens = [];

    // Flag to prevent recursive updates
    let isUpdating = false;

    // Get form container and API route
    const formContainer = document.getElementById('rental-house-form-container');
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

    // Initialize NIK Select2 with AJAX-only approach
    $('#nikSelect').select2({
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

    // Initialize Full Name Select2 with AJAX-only approach
    $('#fullNameSelect').select2({
        placeholder: 'Ketik untuk mencari nama penyelenggara',
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

    // Initialize Responsible Name Select2 with AJAX-only approach
    $('#responsibleNameSelect').select2({
        placeholder: 'Ketik untuk mencari nama penanggung jawab',
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
                        citizen: citizen  // Include the full citizen data
                    })).filter(item => item.id !== ''),
                    pagination: {
                        more: (params.page * 10) < (data.total || 1000)
                    }
                };
            },
            cache: true
        },
        templateResult: function(data) {
            if (!data.citizen) return data.text;
            return $(`<div>
                <div>${data.text}</div>
                <small class="text-muted">${data.citizen.nik || ''} - ${data.citizen.address || ''}</small>
            </div>`);
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

    // Form validation
    $('form').on('submit', function(e) {
        // Check required fields
        const requiredFields = [
            'nik', 'full_name', 'responsible_name', 'address',
            'street', 'alley_number', 'rt', 'building_area',
            'room_count', 'rental_type', 'rental_address'
        ];
        const missingFields = [];

        requiredFields.forEach(field => {
            const value = $(this).find(`[name="${field}"]`).val();
            if (!value || value === '') {
                missingFields.push(field.replace('_', ' '));
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

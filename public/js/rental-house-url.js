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

            // Setup NIK input and name select
            setupNikInput(processedData);
            setupNameSelect(processedData);
        },
        error: function(error) {
            console.error('Failed to load citizen data:', error);
        }
    });
});

// Function to setup NIK input
function setupNikInput(citizens) {
    const nikInput = document.getElementById('nik');
    if (!nikInput) {
        console.warn('NIK input element not found');
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

                // Update full name select
                $('#full_name').val(matchedCitizen.full_name).trigger('change');

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

// Function to setup name select
function setupNameSelect(citizens) {
    const nameSelect = document.getElementById('full_name');
    if (!nameSelect) {
        console.warn('Name select element not found');
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

    // Initialize Full Name Select2
    $('#full_name').select2({
            placeholder: 'Pilih Nama',
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
    $('#full_name').on('select2:select', function (e) {
            const citizen = e.params.data.citizen;
            if (citizen) {
            // Set NIK in input
            const nikValue = citizen.nik ? citizen.nik.toString() : '';
            $('#nik').val(nikValue);

            // Fill other form fields
            populateCitizenData(citizen);
        }
    });
}

// Function to populate citizen data
function populateCitizenData(citizen) {
    // Set address field
    $('#address').val(citizen.address || '');

    // Set RT field
    $('#rt').val(citizen.rt || '');

    // Set RW field
    $('#rw').val(citizen.rw || '');

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
        const nikSelect = document.getElementById('nik');
        const nameSelect = document.getElementById('full_name');

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

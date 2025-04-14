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

    // Load all citizens first before initializing heir rows
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

        // Process citizens for Select2
        function prepareCitizenOptions() {
            const nikOptions = [];
            const nameOptions = [];

            allCitizens.forEach(citizen => {
                // Handle cases where NIK might be coming from various fields
                let nikValue = null;

                if (typeof citizen.nik !== 'undefined' && citizen.nik !== null) {
                    nikValue = citizen.nik;
                } else if (typeof citizen.id !== 'undefined' && citizen.id !== null && !isNaN(citizen.id)) {
                    nikValue = citizen.id;
                }

                if (nikValue !== null) {
                    const nikString = nikValue.toString();
                    nikOptions.push({
                        id: nikString,
                        text: nikString,
                        citizen: citizen
                    });
                }

                // Only add if full_name is available
                if (citizen.full_name) {
                    nameOptions.push({
                        id: citizen.full_name,
                        text: citizen.full_name,
                        citizen: citizen
                    });
                }
            });

            return { nikOptions, nameOptions };
        }

        // Function to initialize Select2 on an heir row
        function initializeHeirSelect2(heirRow) {
            const { nikOptions, nameOptions } = prepareCitizenOptions();
            const nikSelect = heirRow.querySelector('.nik-select');
            const nameSelect = heirRow.querySelector('.fullname-select');

            // Track if we're in the middle of an update to prevent recursion
            let isUpdating = false;

            // Initialize NIK Select2 with pre-loaded data
            $(nikSelect).select2({
                placeholder: 'Pilih NIK',
                width: '100%',
                data: nikOptions,
                language: {
                    noResults: function() {
                        return 'Tidak ada data yang ditemukan';
                    },
                    searching: function() {
                        return 'Mencari...';
                    }
                },
                escapeMarkup: function(markup) {
                    return markup;
                }
            }).on("select2:open", function() {
                $('.select2-results__options').css('max-height', '400px');
            });

            // Initialize Name Select2 with pre-loaded data
            $(nameSelect).select2({
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
                },
                escapeMarkup: function(markup) {
                    return markup;
                }
            }).on("select2:open", function() {
                $('.select2-results__options').css('max-height', '400px');
            });

            // NIK select change handler
            $(nikSelect).on('select2:select', function(e) {
                if (isUpdating) return;
                isUpdating = true;

                const citizen = e.params.data.citizen;
                if (citizen) {
                    const row = $(this).closest('.heir-row');

                    // Update full name
                    $(row).find('.fullname-select').val(citizen.full_name).trigger('change.select2');

                    // Fill personal data fields - only personal info
                    populateHeirFieldsFromCitizen(row, citizen);

                    // Auto-fill location data if it's not already set
                    if (!$('#province_id').val() || !$('#district_id').val() ||
                        !$('#subdistrict_id').val() || !$('#village_id').val()) {
                        // Use the function from location-dropdowns.js
                        populateLocationDropdowns(
                            citizen.province_id,
                            citizen.district_id,
                            citizen.subdistrict_id || citizen.sub_district_id,
                            citizen.village_id
                        );
                    }
                }

                isUpdating = false;
            });

            // Full name select change handler
            $(nameSelect).on('select2:select', function(e) {
                if (isUpdating) return;
                isUpdating = true;

                const citizen = e.params.data.citizen;
                if (citizen) {
                    const row = $(this).closest('.heir-row');

                    // Update NIK
                    if (citizen.nik) {
                        $(row).find('.nik-select').val(citizen.nik.toString()).trigger('change.select2');
                    }

                    // Fill personal data fields - only personal info
                    populateHeirFieldsFromCitizen(row, citizen);

                    // Auto-fill location data if it's not already set
                    if (!$('#province_id').val() || !$('#district_id').val() ||
                        !$('#subdistrict_id').val() || !$('#village_id').val()) {
                        // Use the function from location-dropdowns.js
                        populateLocationDropdowns(
                            citizen.province_id,
                            citizen.district_id,
                            citizen.subdistrict_id || citizen.sub_district_id,
                            citizen.village_id
                        );
                    }
                }

                isUpdating = false;
            });
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

            // Initialize Select2 on the new row
            initializeHeirSelect2(heirRowClone);

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

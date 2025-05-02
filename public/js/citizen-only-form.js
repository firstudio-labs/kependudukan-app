/**
 * Citizen-Only Form Helper
 * Contains functions for handling citizen data without re-fetching location data
 * This is used when location data is already provided from previous steps
 */

// Initialize citizen data (NIK and Name) select fields with Select2
function initializeCitizenSelect(routeUrl, onDataLoaded = null) {
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

            // Now initialize Select2 with the pre-loaded data
            setupSelect2WithData(allCitizens);

            // If callback provided, call it with the loaded citizen data
            if (typeof onDataLoaded === 'function') {
                onDataLoaded(allCitizens);
            }
        },
        error: function(error) {
            // Initialize Select2 anyway with empty data
            setupSelect2WithData([]);
        }
    });

    function setupSelect2WithData(citizens) {
        // Create NIK options array
        const nikOptions = [];
        const nameOptions = [];

        // Process citizen data for Select2
        for (let i = 0; i < citizens.length; i++) {
            const citizen = citizens[i];

            // Handle cases where NIK might be coming from various fields
            let nikValue = null;

            if (typeof citizen.nik !== 'undefined' && citizen.nik !== null) {
                nikValue = citizen.nik;
            } else if (typeof citizen.id !== 'undefined' && citizen.id !== null && !isNaN(citizen.id)) {
                // If id is numeric (not a name), it might be the NIK
                nikValue = citizen.id;
            }

            if (nikValue !== null) {
                const nikString = nikValue.toString();
                nikOptions.push({
                    id: nikString,
                    text: nikString,
                    citizen: citizen,
                    address: citizen.address // Explicitly include address for data-address attribute
                });
            }

            // Only add if full_name is available
            if (citizen.full_name) {
                nameOptions.push({
                    id: citizen.full_name,
                    text: citizen.full_name,
                    citizen: citizen,
                    address: citizen.address // Explicitly include address for data-address attribute
                });
            }
        }

        // Initialize NIK Select2
        $('#nikSelect').select2({
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
            },
            templateResult: function(data) {
                if (data.loading) return data.text;
                return '<div>' + data.text + '</div>';
            },
            templateSelection: function(data) {
                // This sets data-address on the rendered option
                if (data.address) {
                    setTimeout(() => {
                        $('.select2-selection').attr('data-address', data.address);
                    }, 0);
                }
                return data.text;
            }
        }).on("select2:open", function() {
            // This ensures all options are visible when dropdown opens
            $('.select2-results__options').css('max-height', '400px');
        });

        // Initialize Full Name Select2
        $('#fullNameSelect').select2({
            placeholder: 'Pilih Nama Lengkap',
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
            templateSelection: function(data) {
                // This sets data-address on the rendered option
                if (data.address) {
                    setTimeout(() => {
                        $('.select2-selection').attr('data-address', data.address);
                    }, 0);
                }
                return data.text;
            }
        }).on("select2:open", function() {
            // This ensures all options are visible when dropdown opens
            $('.select2-results__options').css('max-height', '400px');
        });

        // When NIK is selected, fill in other fields
        $('#nikSelect').on('select2:select', function (e) {
            if (isUpdating) return; // Prevent recursion
            isUpdating = true;

            // Get the selected citizen data
            const citizen = e.params.data.citizen;

            if (citizen) {
                // Set Full Name in dropdown
                $('#fullNameSelect').val(citizen.full_name).trigger('change.select2'); // Just update UI

                // Fill other form fields
                populateCitizenData(citizen);

                // Set domicile_address immediately from citizen data
                if (citizen.address) {
                    $('#domicile_address').val(citizen.address);
                }

                // Note: We don't populate location dropdowns here since they should already be set
            }

            isUpdating = false;
        });

        // When Full Name is selected, fill in other fields
        $('#fullNameSelect').on('select2:select', function (e) {
            if (isUpdating) return; // Prevent recursion
            isUpdating = true;

            const citizen = e.params.data.citizen;

            if (citizen) {
                // Set NIK in dropdown without triggering the full change event
                const nikValue = citizen.nik ? citizen.nik.toString() : '';
                $('#nikSelect').val(nikValue).trigger('change.select2');  // Just update the UI

                // Fill other form fields
                populateCitizenData(citizen);

                // Set domicile_address immediately from citizen data
                if (citizen.address) {
                    $('#domicile_address').val(citizen.address);
                }

                // Note: We don't populate location dropdowns here since they should already be set
            }

            isUpdating = false;
        });

        // Manually trigger domicile address update if a citizen is already selected
        setTimeout(() => {
            const nikSelect = document.querySelector('#nikSelect');
            if (nikSelect && nikSelect.value) {
                const selectedCitizen = allCitizens.find(c => c.nik == nikSelect.value || c.id == nikSelect.value);
                if (selectedCitizen && selectedCitizen.address) {
                    document.querySelector('#domicile_address').value = selectedCitizen.address;
                }
            }
        }, 500);
    }
}

// Fill citizen data into form fields
function populateCitizenData(citizen) {
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

            // Now initialize Select2 with the pre-loaded data
            setupParentSelect2WithData(allCitizens, parentType);
        },
        error: function(error) {
            // Initialize Select2 anyway with empty data
            setupParentSelect2WithData([], parentType);
        }
    });

    function setupParentSelect2WithData(citizens, parentType) {
        // Create NIK options array
        const nikOptions = [];
        const nameOptions = [];

        // Process citizen data for Select2
        for (let i = 0; i < citizens.length; i++) {
            const citizen = citizens[i];

            // Handle cases where NIK might be coming from various fields
            let nikValue = null;

            if (typeof citizen.nik !== 'undefined' && citizen.nik !== null) {
                nikValue = citizen.nik;
            } else if (typeof citizen.id !== 'undefined' && citizen.id !== null && !isNaN(citizen.id)) {
                // If id is numeric (not a name), it might be the NIK
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
        }

        // Initialize NIK Select2
        $(`#${parentType}_nik`).select2({
            placeholder: `Pilih NIK ${parentType === 'father' ? 'Ayah' : 'Ibu'}`,
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
            },
            templateResult: function(data) {
                if (data.loading) return data.text;
                return '<div>' + data.text + '</div>';
            }
        }).on("select2:open", function() {
            // This ensures all options are visible when dropdown opens
            $('.select2-results__options').css('max-height', '400px');
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

        // When NIK is selected, fill in other fields
        $(`#${parentType}_nik`).on('select2:select', function (e) {
            if (isUpdating) return; // Prevent recursion
            isUpdating = true;

            // Get the selected citizen data
            const citizen = e.params.data.citizen;

            if (citizen) {
                // Set Full Name in dropdown
                $(`#${parentType}_full_name`).val(citizen.full_name).trigger('change.select2'); // Just update UI

                // Fill other form fields
                populateParentData(citizen, parentType);
            }

            isUpdating = false;
        });

        // When Full Name is selected, fill in other fields
        $(`#${parentType}_full_name`).on('select2:select', function (e) {
            if (isUpdating) return; // Prevent recursion
            isUpdating = true;

            const citizen = e.params.data.citizen;

            if (citizen) {
                // Set NIK in dropdown without triggering the full change event
                const nikValue = citizen.nik ? citizen.nik.toString() : '';
                $(`#${parentType}_nik`).val(nikValue).trigger('change.select2');  // Just update the UI

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

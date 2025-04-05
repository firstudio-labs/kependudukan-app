/**
 * Kelahiran (Birth Certificate) Utilities
 * Contains specialized functions for handling birth certificate forms
 */

// Process citizens data for parent Select2 dropdowns
function prepareParentOptions(citizens, genderFilter) {
    const nikOptions = [];
    const nameOptions = [];

    citizens.forEach(citizen => {
        // Skip if not matching gender filter (1 = male, 2 = female)
        if (genderFilter && citizen.gender && citizen.gender.toString() !== genderFilter.toString()) {
            return;
        }

        // Handle NIK value
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

// Populate parent fields from citizen data
function populateParentFields(prefix, citizen) {
    if (!citizen) return;

    // Set basic fields
    $(`#${prefix}_birth_place`).val(citizen.birth_place || '');
    $(`#${prefix}_address`).val(citizen.address || '');

    // Format and set birth date
    if (citizen.birth_date) {
        let birthDate = citizen.birth_date;
        if (birthDate.includes('/')) {
            const [day, month, year] = birthDate.split('/');
            birthDate = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
        }
        $(`#${prefix}_birth_date`).val(birthDate);
    }

    // Set gender (though this should be fixed by the filter)
    if (citizen.gender) {
        $(`#${prefix}_gender`).val(citizen.gender);
    }

    // Set job/occupation
    if (citizen.job_id || citizen.job_type_id) {
        $(`#${prefix}_job`).val(citizen.job_id || citizen.job_type_id);
    }

    // Set religion
    if (citizen.religion) {
        $(`#${prefix}_religion`).val(citizen.religion);
    }
}

// Initialize parent select dropdowns
function initializeParentSelect(parentType, citizens) {
    // Determine gender filter based on parent type
    const genderFilter = parentType === 'father' ? '1' : '2'; // 1 = male, 2 = female
    const { nikOptions, nameOptions } = prepareParentOptions(citizens, genderFilter);

    // Get the select elements
    const nikSelect = $(`#${parentType}_nik`);
    const nameSelect = $(`#${parentType}_full_name`);

    // Initialize NIK Select2
    nikSelect.select2({
        placeholder: `Pilih NIK ${parentType === 'father' ? 'Ayah' : 'Ibu'}`,
        width: '100%',
        data: nikOptions,
        language: {
            noResults: () => 'Tidak ada data yang ditemukan',
            searching: () => 'Mencari...'
        },
        escapeMarkup: markup => markup
    }).on('select2:open', function() {
        $('.select2-results__options').css('max-height', '400px');
    });

    // Initialize Name Select2
    nameSelect.select2({
        placeholder: `Pilih Nama ${parentType === 'father' ? 'Ayah' : 'Ibu'}`,
        width: '100%',
        data: nameOptions,
        language: {
            noResults: () => 'Tidak ada data yang ditemukan',
            searching: () => 'Mencari...'
        },
        escapeMarkup: markup => markup
    }).on('select2:open', function() {
        $('.select2-results__options').css('max-height', '400px');
    });

    // Set up change handlers with synchronization
    let isUpdating = false;

    // NIK change handler
    nikSelect.on('select2:select', function(e) {
        if (isUpdating) return;
        isUpdating = true;

        const citizen = e.params.data.citizen;
        if (citizen) {
            // Update name select
            nameSelect.val(citizen.full_name).trigger('change.select2');
            // Populate other fields
            populateParentFields(parentType, citizen);
        }

        isUpdating = false;
    });

    // Name change handler
    nameSelect.on('select2:select', function(e) {
        if (isUpdating) return;
        isUpdating = true;

        const citizen = e.params.data.citizen;
        if (citizen) {
            // Update NIK select
            if (citizen.nik) {
                nikSelect.val(citizen.nik.toString()).trigger('change.select2');
            }
            // Populate other fields
            populateParentFields(parentType, citizen);
        }

        isUpdating = false;
    });
}

// Initialize both parent selects for birth certificate form
function initializeBirthCertificateSelects(apiUrl) {
    $.ajax({
        url: apiUrl,
        type: 'GET',
        dataType: 'json',
        data: { limit: 10000 },
        success: function(data) {
            // Transform the response to match what we expect
            let processedData = data;
            if (data && data.data && Array.isArray(data.data)) {
                processedData = data.data;
            } else if (data && Array.isArray(data)) {
                processedData = data;
            }

            // Init father and mother selects
            initializeParentSelect('father', processedData);
            initializeParentSelect('mother', processedData);

            // Initialize child gender and religion dropdowns
            initializeChildDropdowns();
        },
        error: function(error) {
            console.error('Failed to load citizen data:', error);
            // Initialize with empty data
            initializeParentSelect('father', []);
            initializeParentSelect('mother', []);
            initializeChildDropdowns();
        }
    });
}

// Initialize child form dropdowns
function initializeChildDropdowns() {
    // Gender dropdown
    if ($('#child_gender').length && $('#child_gender option').length < 2) {
        const genderOptions = [
            { value: "", text: "Pilih Jenis Kelamin" },
            { value: "1", text: "Laki-Laki" },
            { value: "2", text: "Perempuan" }
        ];

        $('#child_gender').empty().append(genderOptions.map(option =>
            `<option value="${option.value}">${option.text}</option>`
        ).join(''));
    }

    // Religion dropdown
    if ($('#child_religion').length && $('#child_religion option').length < 2) {
        const religionOptions = [
            { value: "", text: "Pilih Agama" },
            { value: "1", text: "Islam" },
            { value: "2", text: "Kristen" },
            { value: "3", text: "Katholik" },
            { value: "4", text: "Hindu" },
            { value: "5", text: "Buddha" },
            { value: "6", text: "Kong Hu Cu" },
            { value: "7", text: "Lainnya" }
        ];

        $('#child_religion').empty().append(religionOptions.map(option =>
            `<option value="${option.value}">${option.text}</option>`
        ).join(''));
    }
}

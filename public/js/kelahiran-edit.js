/**
 * Kelahiran (Birth Certificate) Edit Page
 * Specialized setup for the birth certificate edit form
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize citizen data for both parents
    initializeBirthCertificateSelects(citizenApiUrl);

    // Load location data with pre-selected values from location dropdowns utility
    // We'll use a setTimeout to ensure selects have been initialized
    setTimeout(function() {
        // Load existing location data if available
        if (provinceId && districtId && subdistrictId && villageId) {
            populateLocationDropdowns(provinceId, districtId, subdistrictId, villageId);
        }

        // Pre-select parent values
        preSelectParents();
    }, 500);

    // Initialize child address syncing with parent addresses
    setupChildAddressSyncing();
});

// Pre-select parent values from existing data
function preSelectParents() {
    // Father data
    if (fatherNik) {
        $('#father_nik').val(fatherNik).trigger('change');
    }
    if (fatherName) {
        $('#father_full_name').val(fatherName).trigger('change');
    }

    // Mother data
    if (motherNik) {
        $('#mother_nik').val(motherNik).trigger('change');
    }
    if (motherName) {
        $('#mother_full_name').val(motherName).trigger('change');
    }
}

// Set up syncing child address with parent addresses
function setupChildAddressSyncing() {
    // Track original addresses for comparison
    const originalFatherAddress = $('#father_address').val();
    const originalMotherAddress = $('#mother_address').val();
    const originalChildAddress = $('#child_address').val();

    // When parent data is selected, update child's address to match
    $('#father_nik, #father_full_name').on('select2:select', function(e) {
        if (e.params && e.params.data && e.params.data.citizen) {
            const address = e.params.data.citizen.address || '';
            // Only update child address if it matches original father's address or is empty
            if (address && ($('#child_address').val() === originalFatherAddress || !$('#child_address').val())) {
                $('#child_address').val(address);
            }
        }
    });

    $('#mother_nik, #mother_full_name').on('select2:select', function(e) {
        if (e.params && e.params.data && e.params.data.citizen) {
            const address = e.params.data.citizen.address || '';
            // Only update child address if it matches original mother's address or is empty
            if (address && ($('#child_address').val() === originalMotherAddress || !$('#child_address').val())) {
                $('#child_address').val(address);
            }
        }
    });
}

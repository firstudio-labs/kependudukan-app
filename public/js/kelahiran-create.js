/**
 * Kelahiran (Birth Certificate) Create Page
 * Specialized setup for the birth certificate create form
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize citizen data for both parents
    initializeBirthCertificateSelects(citizenApiUrl);

    // Setup location dropdown events
    setupLocationDropdowns();

    // Setup form validation
    setupFormValidation();

    // Initialize child address syncing with parent addresses
    setupChildAddressSyncing();
});

// Set up syncing child address with parent addresses
function setupChildAddressSyncing() {
    // When parent data is selected, update child's address to match
    $('#father_nik, #father_full_name').on('select2:select', function(e) {
        if (e.params && e.params.data && e.params.data.citizen) {
            const address = e.params.data.citizen.address || '';
            if (address && $('#child_address').val() === '') {
                $('#child_address').val(address);
            }
        }
    });

    $('#mother_nik, #mother_full_name').on('select2:select', function(e) {
        if (e.params && e.params.data && e.params.data.citizen) {
            const address = e.params.data.citizen.address || '';
            if (address && $('#child_address').val() === '') {
                $('#child_address').val(address);
            }
        }
    });
}

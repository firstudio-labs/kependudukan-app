/**
 * Family Card Manager Script
 * Handles family card (KK) management, including:
 * - Location dropdown cascading
 * - Data persistence
 */

// Global variables to store location code mappings
let provinceCodeMap = {};
let districtCodeMap = {};
let subDistrictCodeMap = {};
let villageCodeMap = {};

// Reverse maps to get ID from code
let provinceIdMap = {};
let districtIdMap = {};
let subDistrictIdMap = {};
let villageIdMap = {};

// Define isUpdating in the global scope so all handlers can access it
let isUpdating = false;

// Function to load province codes and store the ID-to-code mapping
async function loadProvinceCodeMap() {
    try {
        const response = await $.ajax({
            url: `${getBaseUrl()}/location/provinces`,
            type: 'GET'
        });

        if (response.data && Array.isArray(response.data)) {
            // Create mappings in both directions
            response.data.forEach(province => {
                provinceCodeMap[province.id] = province.code;
                provinceIdMap[province.code] = province.id;
            });
        }
    } catch (error) {
        // Silent error handling
    }
}

// Function to get district code map for a specific province
async function loadDistrictCodeMap(provinceCode) {
    try {
        const response = await $.ajax({
            url: `${getBaseUrl()}/location/districts/${provinceCode}`,
            type: 'GET'
        });

        if (response && Array.isArray(response)) {
            // Create mappings in both directions
            response.forEach(district => {
                districtCodeMap[district.id] = district.code;
                districtIdMap[district.code] = district.id;
            });
        }
    } catch (error) {
        // Silent error handling
    }
}

// Function to get subdistrict code map for a specific district
async function loadSubDistrictCodeMap(districtCode) {
    try {
        const response = await $.ajax({
            url: `${getBaseUrl()}/location/sub-districts/${districtCode}`,
            type: 'GET'
        });

        if (response && Array.isArray(response)) {
            // Create mappings in both directions
            response.forEach(subDistrict => {
                subDistrictCodeMap[subDistrict.id] = subDistrict.code;
                subDistrictIdMap[subDistrict.code] = subDistrict.id;
            });
        }
    } catch (error) {
        // Silent error handling
    }
}

// Function to get village code map for a specific subdistrict
async function loadVillageCodeMap(subDistrictCode) {
    try {
        const response = await $.ajax({
            url: `${getBaseUrl()}/location/villages/${subDistrictCode}`,
            type: 'GET'
        });

        if (response && Array.isArray(response)) {
            // Create mappings in both directions
            response.forEach(village => {
                villageCodeMap[village.id] = village.code;
                villageIdMap[village.code] = village.id;
            });
        }
    } catch (error) {
        // Silent error handling
    }
}

// Get base URL from a meta tag or directly from window.location
const getBaseUrl = () => {
    const metaUrl = document.querySelector('meta[name="base-url"]');
    return metaUrl ? metaUrl.getAttribute('content') : window.location.origin;
};

// Function to save KK data to localStorage
function saveKKDataToLocalStorage() {
    const kkData = {
        kk: document.getElementById('kk')?.value || '',
        address: document.getElementById('address')?.value || '',
        postal_code: document.getElementById('postal_code')?.value || '',
        rt: document.getElementById('rt')?.value || '',
        rw: document.getElementById('rw')?.value || '',
        province_id: document.getElementById('province_id')?.value || '',
        district_id: document.getElementById('district_id')?.value || '',
        sub_district_id: document.getElementById('sub_district_id')?.value || '',
        village_id: document.getElementById('village_id')?.value || '',
        dusun: document.getElementById('dusun')?.value || '',

        // Data alamat luar negeri
        foreign_address: document.getElementById('foreign_address')?.value || '',
        city: document.getElementById('city')?.value || '',
        state: document.getElementById('state')?.value || '',
        country: document.getElementById('country')?.value || '',
        foreign_postal_code: document.getElementById('foreign_postal_code')?.value || ''
    };

    localStorage.setItem('kkDetailData', JSON.stringify(kkData));
    console.log('Data KK berhasil disimpan ke localStorage:', kkData);

    return kkData;
}

// Function to load KK data from localStorage
function loadKKDataFromLocalStorage() {
    try {
        const savedData = localStorage.getItem('kkDetailData');
        if (!savedData) return null;

        const kkData = JSON.parse(savedData);
        return kkData;
    } catch (error) {
        return null;
    }
}

// Function to copy KK data to family member form
function copyDataToFamilyMemberForm() {
    // Get current values from the KK form
    const kkValue = document.getElementById('kk')?.value || '';
    const addressValue = document.getElementById('address')?.value || '';
    const postalCodeValue = document.getElementById('postal_code')?.value || '';
    const rtValue = document.getElementById('rt')?.value || '';
    const rwValue = document.getElementById('rw')?.value || '';
    const provinceIdValue = document.getElementById('province_id_hidden')?.value || '';
    const districtIdValue = document.getElementById('district_id_hidden')?.value || '';
    const subDistrictIdValue = document.getElementById('sub_district_id_hidden')?.value || '';
    const villageIdValue = document.getElementById('village_id_hidden')?.value || '';
    const hamletValue = document.getElementById('dusun')?.value || '';

    // Foreign address values
    const foreignAddressValue = document.getElementById('foreign_address')?.value || '';
    const cityValue = document.getElementById('city')?.value || '';
    const stateValue = document.getElementById('state')?.value || '';
    const countryValue = document.getElementById('country')?.value || '';
    const foreignPostalCodeValue = document.getElementById('foreign_postal_code')?.value || '';

    // Set values to hidden fields in the family member form
    const setElementValue = (id, value) => {
        const element = document.getElementById(id);
        if (element) element.value = value || '';
    };

    setElementValue('form_kk', kkValue);
    setElementValue('form_address', addressValue);
    setElementValue('form_postal_code', postalCodeValue);
    setElementValue('form_rt', rtValue);
    setElementValue('form_rw', rwValue);
    setElementValue('form_province_id', provinceIdValue);
    setElementValue('form_district_id', districtIdValue);
    setElementValue('form_sub_district_id', subDistrictIdValue);
    setElementValue('form_village_id', villageIdValue);
    setElementValue('form_hamlet', hamletValue);

    // Set foreign address values
    setElementValue('form_foreign_address', foreignAddressValue);
    setElementValue('form_city', cityValue);
    setElementValue('form_state', stateValue);
    setElementValue('form_country', countryValue);
    setElementValue('form_foreign_postal_code', foreignPostalCodeValue);
}

/**
 * Initialize the Family Card Manager
 */
function initializeFamilyCardManager() {
    // Monitor KK input field for changes
    const kkInput = document.getElementById('kk');
    if (kkInput) {
        kkInput.addEventListener('input', function() {
            const selectedValue = this.value;
            if (selectedValue) {
                // Save to hidden field in family member form
                document.getElementById('kk').value = selectedValue;

                // Copy to family member form hidden field
                if (document.getElementById('form_kk')) {
                    document.getElementById('form_kk').value = selectedValue;
                }
            }
        });
    }

    // Province change handler
    $('#province_id').on('change', function () {
        const provinceCode = $(this).val();

        if (provinceCode && provinceIdMap[provinceCode]) {
            $('#province_id_hidden').val(provinceIdMap[provinceCode]);
        } else {
            $('#province_id_hidden').val('');
        }

        // Reset dropdowns
        $('#district_id').empty().append('<option value="">Pilih Kabupaten</option>');
        $('#sub_district_id').empty().append('<option value="">Pilih Kecamatan</option>');
        $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>');

        // Reset hidden fields
        $('#district_id_hidden').val('');
        $('#sub_district_id_hidden').val('');
        $('#village_id_hidden').val('');

        if (provinceCode) {
            // Show loading state
            $('#district_id').prop('disabled', true).empty().append('<option value="">Loading...</option>');

            // Fetch districts from API
            $.ajax({
                url: `${getBaseUrl()}/location/districts/${provinceCode}`,
                type: 'GET',
                success: function (response) {
                    $('#district_id').empty().append('<option value="">Pilih Kabupaten</option>');

                    // Create fresh mappings for districts
                    districtIdMap = {};
                    districtCodeMap = {};

                    if (response.data && Array.isArray(response.data) && response.data.length > 0) {
                        response.data.forEach(function (item) {
                            // Store mappings in both directions
                            districtIdMap[item.code] = item.id;
                            districtCodeMap[item.id] = item.code;

                            $('#district_id').append(`<option value="${item.code}">${item.name}</option>`);
                        });
                    }

                    $('#district_id').prop('disabled', false);
                },
                error: function (error) {
                    $('#district_id').empty().append('<option value="">Error loading data</option>');
                    $('#district_id').prop('disabled', false);
                }
            });
        }
    });

    // District change handler
    $('#district_id').on('change', function () {
        const districtCode = $(this).val();

        if (districtCode && districtIdMap[districtCode]) {
            $('#district_id_hidden').val(districtIdMap[districtCode]);
        } else {
            $('#district_id_hidden').val('');
        }

        // Reset dropdowns
        $('#sub_district_id').empty().append('<option value="">Pilih Kecamatan</option>');
        $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>');

        // Reset hidden fields
        $('#sub_district_id_hidden').val('');
        $('#village_id_hidden').val('');

        if (districtCode) {
            // Show loading state
            $('#sub_district_id').prop('disabled', true).empty().append('<option value="">Loading...</option>');

            // Fetch sub-districts from API
            $.ajax({
                url: `${getBaseUrl()}/location/sub-districts/${districtCode}`,
                type: 'GET',
                success: function (response) {
                    $('#sub_district_id').empty().append('<option value="">Pilih Kecamatan</option>');

                    // Create fresh mappings for subdistricts
                    subDistrictIdMap = {};
                    subDistrictCodeMap = {};

                    if (response.data && Array.isArray(response.data) && response.data.length > 0) {
                        response.data.forEach(function (item) {
                            // Store mappings in both directions
                            subDistrictIdMap[item.code] = item.id;
                            subDistrictCodeMap[item.id] = item.code;

                            $('#sub_district_id').append(`<option value="${item.code}">${item.name}</option>`);
                        });
                    }

                    $('#sub_district_id').prop('disabled', false);
                },
                error: function (error) {
                    $('#sub_district_id').empty().append('<option value="">Error loading data</option>');
                    $('#sub_district_id').prop('disabled', false);
                }
            });
        }
    });

    // Sub-district change handler
    $('#sub_district_id').on('change', function () {
        const subDistrictCode = $(this).val();

        if (subDistrictCode && subDistrictIdMap[subDistrictCode]) {
            $('#sub_district_id_hidden').val(subDistrictIdMap[subDistrictCode]);
        } else {
            $('#sub_district_id_hidden').val('');
        }

        // Reset dropdowns
        $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>');

        // Reset hidden field
        $('#village_id_hidden').val('');

        if (subDistrictCode) {
            // Show loading state
            $('#village_id').prop('disabled', true).empty().append('<option value="">Loading...</option>');

            // Fetch villages from API
            $.ajax({
                url: `${getBaseUrl()}/location/villages/${subDistrictCode}`,
                type: 'GET',
                success: function (response) {
                    $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>');

                    // Create fresh mappings for villages
                    villageIdMap = {};
                    villageCodeMap = {};

                    if (response.data && Array.isArray(response.data) && response.data.length > 0) {
                        response.data.forEach(function (item) {
                            // Store mappings in both directions
                            villageIdMap[item.code] = item.id;
                            villageCodeMap[item.id] = item.code;

                            $('#village_id').append(`<option value="${item.code}">${item.name}</option>`);
                        });
                    }

                    $('#village_id').prop('disabled', false);
                },
                error: function (error) {
                    $('#village_id').empty().append('<option value="">Error loading data</option>');
                    $('#village_id').prop('disabled', false);
                }
            });
        }
    });

    // Village change handler
    $('#village_id').on('change', function() {
        const villageCode = $(this).val();

        if (villageCode && villageIdMap[villageCode]) {
            $('#village_id_hidden').val(villageIdMap[villageCode]);
        } else {
            $('#village_id_hidden').val('');
        }
    });

    // Add form submit handler to save all form data before submission
    const addFamilyMemberForm = document.getElementById('addFamilyMemberForm');
    if (addFamilyMemberForm) {
        addFamilyMemberForm.addEventListener('submit', function(e) {
            // Save current KK form data to localStorage
            saveKKDataToLocalStorage();

            // Ensure KK data is copied to hidden fields
            copyDataToFamilyMemberForm();

            // Check if KK exists
            const currentKK = document.getElementById('kk')?.value || '';
            if (!currentKK) {
                e.preventDefault();
                alert("Silakan isi Nomor KK terlebih dahulu");
                return false;
            }

            // Additional check for family_members_json
            const familyMembersJson = document.getElementById('family_members_json');
            if (familyMembersJson && (!familyMembersJson.value || familyMembersJson.value === '[]')) {
                // If no family members have been added, the current form data should be included
                const addToListBtn = document.getElementById('addToListBtn');
                if (addToListBtn) {
                    // Trigger the add to list functionality first
                    addToListBtn.click();

                    // If after clicking, we still don't have family members, prevent submission
                    if (!familyMembersJson.value || familyMembersJson.value === '[]') {
                        e.preventDefault();
                        return false;
                    }
                }
            }

            return true;
        });
    }

    // Check for previously saved data
    const savedKKData = loadKKDataFromLocalStorage();
    if (savedKKData && savedKKData.kk) {
        // Safely set form values with element existence checks
        const setElementValue = (id, value) => {
            const element = document.getElementById(id);
            if (element) element.value = value || '';
        };

        // Populate form with saved data
        setElementValue('kk', savedKKData.kk);
        setElementValue('address', savedKKData.address);
        setElementValue('postal_code', savedKKData.postal_code);
        setElementValue('rt', savedKKData.rt);
        setElementValue('rw', savedKKData.rw);
        setElementValue('dusun', savedKKData.dusun);

        // Foreign address fields
        setElementValue('foreign_address', savedKKData.foreign_address);
        setElementValue('city', savedKKData.city);
        setElementValue('state', savedKKData.state);
        setElementValue('country', savedKKData.country);
        setElementValue('foreign_postal_code', savedKKData.foreign_postal_code);

        // Handle location data if available
        if (savedKKData.province_id) {
            setElementValue('province_id', savedKKData.province_id);

            // Further location cascading will be handled by the change event handlers
            const provinceCode = provinceCodeMap[savedKKData.province_id];
            if (provinceCode) {
                const provinceSelect = document.getElementById('province_code');
                if (provinceSelect) {
                    $('#province_code').val(provinceCode).trigger('change');
                }
            }
        }
    }
}

// Initialize the Family Card Manager when the document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Load province code mappings first
    loadProvinceCodeMap().then(() => {
        initializeFamilyCardManager();
    });

    // Set flash messages for SweetAlert if present
    const successMessage = document.body.getAttribute('data-success-message');
    const errorMessage = document.body.getAttribute('data-error-message');

    if (successMessage) {
        document.body.setAttribute('data-success-message', successMessage);
    }

    if (errorMessage) {
        document.body.setAttribute('data-error-message', errorMessage);
    }
});

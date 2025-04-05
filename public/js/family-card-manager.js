/**
 * Family Card Manager Script
 * Handles family card (KK) management, including:
 * - Select2 integration for KK and family member selection
 * - Location dropdown cascading
 * - Family member listing
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

// Fungsi untuk mengambil data dari API through controllers
async function fetchCitizens() {
    try {
        // Use the all citizens endpoint
        const response = await axios.get(`${getBaseUrl()}/citizens/all`);
        const data = response.data;

        // Get the citizens array regardless of structure
        let citizensList = [];

        if (data.status === 'OK') {
            if (Array.isArray(data.data)) {
                citizensList = data.data;
            } else if (data.data && typeof data.data === 'object') {
                // In case data.data is a single object
                if (data.data.citizens && Array.isArray(data.data.citizens)) {
                    // If structure is { data: { citizens: [...] } }
                    citizensList = data.data.citizens;
                } else {
                    // If it's a single citizen, make it an array
                    citizensList = [data.data];
                }
            }

            const kkSelect = document.getElementById('kkSelect');
            const fullNameSelect = document.getElementById('full_name');

            if (!kkSelect || !fullNameSelect) {
                return;
            }

            // Clear existing options
            kkSelect.innerHTML = '<option value="">Pilih No KK</option>';
            fullNameSelect.innerHTML = '<option value="">Pilih Nama Lengkap</option>';

            // Filter only heads of family from all citizens
            const headsOfFamily = citizensList.filter(citizen =>
                citizen.family_status === 'KEPALA KELUARGA');

            // Add options for heads of family
            if (headsOfFamily.length > 0) {
                for (const citizen of headsOfFamily) {
                    const kkOption = document.createElement('option');
                    kkOption.value = citizen.kk;
                    kkOption.textContent = citizen.kk;

                    // Set all data attributes
                    kkOption.setAttribute('data-full-name', citizen.full_name);
                    kkOption.setAttribute('data-address', citizen.address || '');
                    kkOption.setAttribute('data-postal-code', citizen.postal_code || '');
                    kkOption.setAttribute('data-rt', citizen.rt || '');
                    kkOption.setAttribute('data-rw', citizen.rw || '');
                    kkOption.setAttribute('data-telepon', citizen.telepon || '');
                    kkOption.setAttribute('data-email', citizen.email || '');
                    kkOption.setAttribute('data-province-id', citizen.province_id || '');
                    kkOption.setAttribute('data-district-id', citizen.district_id || '');
                    kkOption.setAttribute('data-sub-district-id', citizen.sub_district_id || '');
                    kkOption.setAttribute('data-village-id', citizen.village_id || '');
                    kkOption.setAttribute('data-dusun', citizen.dusun || '');

                    kkSelect.appendChild(kkOption);

                    const fullNameOption = document.createElement('option');
                    fullNameOption.value = citizen.full_name;
                    fullNameOption.textContent = citizen.full_name;

                    // Set all data attributes
                    fullNameOption.setAttribute('data-kk', citizen.kk || '');
                    fullNameOption.setAttribute('data-address', citizen.address || '');
                    fullNameOption.setAttribute('data-postal-code', citizen.postal_code || '');
                    fullNameOption.setAttribute('data-rt', citizen.rt || '');
                    fullNameOption.setAttribute('data-rw', citizen.rw || '');
                    fullNameOption.setAttribute('data-telepon', citizen.telepon || '');
                    fullNameOption.setAttribute('data-email', citizen.email || '');
                    fullNameOption.setAttribute('data-province-id', citizen.province_id || '');
                    fullNameOption.setAttribute('data-district-id', citizen.district_id || '');
                    fullNameOption.setAttribute('data-sub-district-id', citizen.sub_district_id || '');
                    fullNameOption.setAttribute('data-village-id', citizen.village_id || '');
                    fullNameOption.setAttribute('data-dusun', citizen.dusun || '');

                    fullNameSelect.appendChild(fullNameOption);
                }

                // Initialize Select2 after populating options
                $('#kkSelect').select2({
                    placeholder: 'Pilih No KK',
                    width: '100%'
                });

                $('#full_name').select2({
                    placeholder: 'Pilih Nama Lengkap',
                    width: '100%'
                });
            }
        }
    } catch (error) {
        // Silently handle errors
        console.error("Error fetching citizens:", error);
    }
}

// Function to properly fetch and set location data
function fetchAndSetLocationData(provinceCode, districtCode, subDistrictCode, villageCode) {
    // Step 1: Set province and load its options
    if (!provinceCode) return;

    // Set province value and hidden ID
    $('#province_id').val(provinceCode);
    $('#province_id_hidden').val(provinceIdMap[provinceCode] || '');

    // Step 2: Fetch district data based on province code
    $.ajax({
        url: `${getBaseUrl()}/location/districts/${provinceCode}`,
        type: 'GET',
        success: function (response) {
            // Clear and prepare district dropdown
            $('#district_id').empty().append('<option value="">Pilih Kabupaten</option>');

            // Create fresh ID to Code mappings for districts
            districtIdMap = {};
            districtCodeMap = {};

            // Populate district options
            if (response.data && Array.isArray(response.data)) {
                response.data.forEach(function (item) {
                    // Store both mappings
                    districtIdMap[item.code] = item.id;
                    districtCodeMap[item.id] = item.code;

                    $('#district_id').append(`<option value="${item.code}">${item.name}</option>`);
                });
            }

            // Enable district dropdown and set the selected value using the code
            $('#district_id').prop('disabled', false).val(districtCode);

            // Set the hidden district ID
            if (districtCode && districtIdMap[districtCode]) {
                $('#district_id_hidden').val(districtIdMap[districtCode]);
            }

            // If district code exists, fetch sub-districts
            if (districtCode) {
                $.ajax({
                    url: `${getBaseUrl()}/location/sub-districts/${districtCode}`,
                    type: 'GET',
                    success: function (response) {
                        // Clear and prepare sub-district dropdown
                        $('#sub_district_id').empty().append('<option value="">Pilih Kecamatan</option>');

                        // Create fresh mappings for subdistricts from this district
                        subDistrictIdMap = {};
                        subDistrictCodeMap = {};

                        // Populate sub-district options
                        if (response.data && Array.isArray(response.data)) {
                            response.data.forEach(function (item) {
                                // Store both mappings
                                subDistrictIdMap[item.code] = item.id;
                                subDistrictCodeMap[item.id] = item.code;

                                $('#sub_district_id').append(`<option value="${item.code}">${item.name}</option>`);
                            });
                        }

                        // Enable sub-district dropdown and set the selected value using the code
                        $('#sub_district_id').prop('disabled', false).val(subDistrictCode);

                        // Set the hidden sub-district ID
                        if (subDistrictCode && subDistrictIdMap[subDistrictCode]) {
                            $('#sub_district_id_hidden').val(subDistrictIdMap[subDistrictCode]);
                        }

                        // If sub-district code exists, fetch villages
                        if (subDistrictCode) {
                            $.ajax({
                                url: `${getBaseUrl()}/location/villages/${subDistrictCode}`,
                                type: 'GET',
                                success: function (response) {
                                    // Clear and prepare village dropdown
                                    $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>');

                                    // Create fresh mappings for villages from this subdistrict
                                    villageIdMap = {};
                                    villageCodeMap = {};

                                    // Populate village options
                                    if (response.data && Array.isArray(response.data)) {
                                        response.data.forEach(function (item) {
                                            // Store both mappings
                                            villageIdMap[item.code] = item.id;
                                            villageCodeMap[item.id] = item.code;

                                            $('#village_id').append(`<option value="${item.code}">${item.name}</option>`);
                                        });
                                    }

                                    // Enable village dropdown and set the selected value using the code
                                    $('#village_id').prop('disabled', false).val(villageCode);

                                    // Set the hidden village ID
                                    if (villageCode && villageIdMap[villageCode]) {
                                        $('#village_id_hidden').val(villageIdMap[villageCode]);
                                    }
                                },
                                error: function (error) {
                                    $('#village_id').empty().append('<option value="">Error loading data</option>');
                                }
                            });
                        }
                    },
                    error: function (error) {
                        $('#sub_district_id').empty().append('<option value="">Error loading data</option>');
                    }
                });
            }
        },
        error: function (error) {
            $('#district_id').empty().append('<option value="">Error loading data</option>');
        }
    });
}

// Function to map location IDs to their corresponding codes and populate dropdowns
async function populateLocationDropdowns(provinceId, districtId, subDistrictId, villageId) {
    try {
        // First, store the IDs in hidden fields
        $('#province_id_hidden').val(provinceId || '');
        $('#district_id_hidden').val(districtId || '');
        $('#sub_district_id_hidden').val(subDistrictId || '');
        $('#village_id_hidden').val(villageId || '');

        // Then populate province dropdown (should already be populated on page load)
        if (provinceId) {
            // Get province data from API
            const provinceResponse = await axios.get(`${getBaseUrl()}/location/provinces`);
            if (provinceResponse.data && provinceResponse.data.data) {
                // Find matching province by ID
                const province = provinceResponse.data.data.find(p => p.id == provinceId);
                if (province) {
                    // Update province dropdown
                    $('#province_id').val(province.code);

                    // Now get district data for this province
                    const districtResponse = await axios.get(`${getBaseUrl()}/location/districts/${province.code}`);

                    if (districtResponse.data && Array.isArray(districtResponse.data)) {
                        // Clear and repopulate district dropdown
                        $('#district_id').empty()
                            .append('<option value="">Pilih Kabupaten</option>')
                            .prop('disabled', false);

                        // Add all districts
                        districtResponse.data.forEach(district => {
                            $('#district_id').append(
                                `<option value="${district.code}" data-id="${district.id}">${district.name}</option>`
                            );
                            // Store mapping
                            districtIdMap[district.code] = district.id;
                            districtCodeMap[district.id] = district.code;
                        });

                        // If we have a district ID, select it
                        if (districtId) {
                            // Find the district by ID
                            const district = districtResponse.data.find(d => d.id == districtId);
                            if (district) {
                                $('#district_id').val(district.code);

                                // Now get subdistrict data
                                const subDistrictResponse = await axios.get(`${getBaseUrl()}/location/sub-districts/${district.code}`);

                                if (subDistrictResponse.data && Array.isArray(subDistrictResponse.data)) {
                                    // Clear and repopulate subdistrict dropdown
                                    $('#sub_district_id').empty()
                                        .append('<option value="">Pilih Kecamatan</option>')
                                        .prop('disabled', false);

                                    // Add all subdistricts
                                    subDistrictResponse.data.forEach(subDistrict => {
                                        $('#sub_district_id').append(
                                            `<option value="${subDistrict.code}" data-id="${subDistrict.id}">${subDistrict.name}</option>`
                                        );
                                        // Store mapping
                                        subDistrictIdMap[subDistrict.code] = subDistrict.id;
                                        subDistrictCodeMap[subDistrict.id] = subDistrict.code;
                                    });

                                    // If we have a subdistrict ID, select it
                                    if (subDistrictId) {
                                        // Find the subdistrict by ID
                                        const subDistrict = subDistrictResponse.data.find(sd => sd.id == subDistrictId);
                                        if (subDistrict) {
                                            $('#sub_district_id').val(subDistrict.code);

                                            // Now get village data
                                            const villageResponse = await axios.get(`${getBaseUrl()}/location/villages/${subDistrict.code}`);

                                            if (villageResponse.data && Array.isArray(villageResponse.data)) {
                                                // Clear and repopulate village dropdown
                                                $('#village_id').empty()
                                                    .append('<option value="">Pilih Desa/Kelurahan</option>')
                                                    .prop('disabled', false);

                                                // Add all villages
                                                villageResponse.data.forEach(village => {
                                                    $('#village_id').append(
                                                        `<option value="${village.code}" data-id="${village.id}">${village.name}</option>`
                                                    );
                                                    // Store mapping
                                                    villageIdMap[village.code] = village.id;
                                                    villageCodeMap[village.id] = village.code;
                                                });

                                                // If we have a village ID, select it
                                                if (villageId) {
                                                    // Find the village by ID
                                                    const village = villageResponse.data.find(v => v.id == villageId);
                                                    if (village) {
                                                        $('#village_id').val(village.code);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    } catch (error) {
        // Silent error handling
        console.error("Error populating location dropdowns:", error);
    }
}

/**
 * Initialize the Family Card Manager
 */
function initializeFamilyCardManager() {
    // Initialize Select2 for dropdown elements
    $('#kkSelect').select2({
        placeholder: 'Pilih No KK',
        width: '100%'
    });

    $('#full_name').select2({
        placeholder: 'Pilih Nama Lengkap',
        width: '100%'
    });

    // KK select change handler
    $('#kkSelect').on('change', function () {
        if (isUpdating) return; // Avoid recursion
        isUpdating = true;

        const selectedKK = $(this).val();
        const selectedOption = $(this).find('option:selected');

        if (selectedKK) {
            // Get data from data-* attributes
            const fullName = selectedOption.attr('data-full-name');
            const address = selectedOption.attr('data-address');
            const postalCode = selectedOption.attr('data-postal-code');
            const rt = selectedOption.attr('data-rt');
            const rw = selectedOption.attr('data-rw');
            const telepon = selectedOption.attr('data-telepon') || '';
            const email = selectedOption.attr('data-email') || '';
            const provinceId = selectedOption.attr('data-province-id');
            const districtId = selectedOption.attr('data-district-id');
            const subDistrictId = selectedOption.attr('data-sub-district-id');
            const villageId = selectedOption.attr('data-village-id');
            const dusun = selectedOption.attr('data-dusun') || '';

            // Fill form fields
            $('#full_name').val(fullName || '').trigger('change.select2');
            $('#address').val(address || '');
            $('#postal_code').val(postalCode || '');
            $('#rt').val(rt || '');
            $('#rw').val(rw || '');
            $('#telepon').val(telepon);
            $('#email').val(email);
            $('#dusun').val(dusun);

            // Store the location IDs in hidden fields
            $('#province_id_hidden').val(provinceId || '');
            $('#district_id_hidden').val(districtId || '');
            $('#sub_district_id_hidden').val(subDistrictId || '');
            $('#village_id_hidden').val(villageId || '');

            // Populate location dropdowns
            populateLocationDropdowns(provinceId, districtId, subDistrictId, villageId);

            // Fetch family members
            $.ajax({
                url: `${getBaseUrl()}/getFamilyMembers`,
                type: "GET",
                data: { kk: selectedKK },
                success: function (response) {
                    if (response.status === "OK") {
                        $('#jml_anggota_kk').val(response.count);
                        $('#familyMembersContainer').empty();

                        // Create fields for each family member
                        if (response.data && Array.isArray(response.data)) {
                            response.data.forEach((member, index) => {
                                const fieldHtml = `
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Anggota ${index + 1}</label>
                                    <input type="text"
                                        value="${member.full_name} - ${member.family_status}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                                        readonly>
                                    <input type="hidden" name="family_members[${index}][full_name]" value="${member.full_name}">
                                    <input type="hidden" name="family_members[${index}][family_status]" value="${member.family_status}">
                                </div>
                                `;
                                $('#familyMembersContainer').append(fieldHtml);
                            });
                        }
                    } else {
                        $('#jml_anggota_kk').val(0);
                        $('#familyMembersContainer').empty();
                    }
                },
                error: function () {
                    $('#jml_anggota_kk').val(0);
                    $('#familyMembersContainer').empty();
                }
            });

        } else {
            // Clear fields if no selection
            $('#full_name').val('').trigger('change.select2');
            $('#address').val('');
            $('#postal_code').val('');
            $('#rt').val('');
            $('#rw').val('');
            $('#telepon').val('');
            $('#email').val('');
            $('#provinc_id').val('');
            $('#district_id').val('');
            $('#sub_district_id').val('');
            $('#village_id').val('');
            $('#dusun').val('');
            $('#jml_anggota_kk').val('');
            $('#familyMembersContainer').empty();
        }

        isUpdating = false;
    });

    // Full name select change handler
    $('#full_name').on('change', function() {
        if (isUpdating) return; // Avoid recursion
        isUpdating = true;

        const selectedName = $(this).val();
        const selectedOption = $(this).find('option:selected');

        if (selectedName) {
            // Get data from data-* attributes
            const kk = selectedOption.attr('data-kk');
            $('#kkSelect').val(kk).trigger('change.select2');

            // No need to manually populate fields, it will happen through the KK change event
        } else {
            // Clear fields if no selection
            $('#address').val('');
            $('#postal_code').val('');
            $('#rt').val('');
            $('#rw').val('');
            $('#telepon').val('');
            $('#email').val('');
            $('#province_id').val('');
            $('#district_id').val('');
            $('#sub_district_id').val('');
            $('#village_id').val('');
            $('#dusun').val('');
            $('#jml_anggota_kk').val('');
            $('#familyMembersContainer').empty();
        }

        isUpdating = false;
    });

    // Province change handler
    $('#province_id').on('change', function () {
        const provinceCode = $(this).val();

        if (provinceCode && provinceIdMap[provinceCode]) {
            $('#province_id_hidden').val(provinceIdMap[provinceCode]);
        } else {
            $('#province_id_hidden').val('');
        }

        // Reset dropdowns
        $('#district_id').empty().append('<option value="">Pilih Kabupaten</option>').prop('disabled', true);
        $('#sub_district_id').empty().append('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
        $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>').prop('disabled', true);

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
        $('#sub_district_id').empty().append('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
        $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>').prop('disabled', true);

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
        $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>').prop('disabled', true);

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

    // Fetch citizen data
    fetchCitizens();
}

// Initialize the Family Card Manager when the document is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeFamilyCardManager();

    // Set flash messages for SweetAlert
    document.body.setAttribute('data-success-message', document.body.getAttribute('data-success-message'));
    document.body.setAttribute('data-error-message', document.body.getAttribute('data-error-message'));
});

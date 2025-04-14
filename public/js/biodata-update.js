/**
 * JavaScript functionality for the biodata update page
 */

// Global variables
let citizenData = {};

// Cache DOM elements
function cacheDOMElements() {
    return {
        provinceSelect: document.getElementById('province_code'),
        districtSelect: document.getElementById('district_code'),
        subDistrictSelect: document.getElementById('sub_district_code'),
        villageSelect: document.getElementById('village_code'),
        provinceIdInput: document.getElementById('province_id'),
        districtIdInput: document.getElementById('district_id'),
        subDistrictIdInput: document.getElementById('sub_district_id'),
        villageIdInput: document.getElementById('village_id')
    };
}

// Helper function to reset select options
function resetSelect(select, defaultText = 'Pilih', hiddenInput = null) {
    select.innerHTML = `<option value="">${defaultText}</option>`;
    select.disabled = true;
    if (hiddenInput) hiddenInput.value = '';
}

// Helper function to populate select options with code as value and id as data attribute
function populateSelect(select, data, defaultText, selectedCode = null, hiddenInput = null, fixedId = null) {
    try {
        const fragment = document.createDocumentFragment();
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = defaultText;
        fragment.appendChild(defaultOption);

        let foundSelected = false;

        if (Array.isArray(data)) {
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.code;
                option.setAttribute('data-id', item.id);
                option.textContent = item.name;

                // Check if this should be selected
                // Either: 1. It matches the selectedCode OR 2. Its ID matches the fixedId
                if ((selectedCode && item.code == selectedCode) || (fixedId && item.id == fixedId)) {
                    option.selected = true;
                    if (hiddenInput) hiddenInput.value = item.id;
                    foundSelected = true;
                }

                fragment.appendChild(option);
            });
        }

        select.innerHTML = '';
        select.appendChild(fragment);
        select.disabled = false;

        // If we're using a fixed ID but didn't find a match, make sure to set the hidden input
        if (!foundSelected && fixedId && hiddenInput) {
            hiddenInput.value = fixedId;
        }

        return foundSelected;
    } catch (error) {
        console.error('Error populating select:', error);
        select.innerHTML = `<option value="">Error loading data</option>`;
        select.disabled = true;
        return false;
    }
}

// Update hidden input when selection changes
function updateHiddenInput(select, hiddenInput) {
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption && selectedOption.hasAttribute('data-id')) {
        hiddenInput.value = selectedOption.getAttribute('data-id');
    }
}

// Load districts based on province code
function loadDistricts(provinceCode, elements, fixedDistrictId) {
    return new Promise((resolve, reject) => {
        if (!provinceCode) {
            resetSelect(elements.districtSelect, 'Pilih Kabupaten', elements.districtIdInput);
            resolve(false);
            return;
        }

        resetSelect(elements.districtSelect, 'Loading...', elements.districtIdInput);

        const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content') || '';
        fetch(`${baseUrl}/location/districts/${provinceCode}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data && data.length > 0) {
                    // Try to match using the fixed District ID we already know
                    const foundSelected = populateSelect(
                        elements.districtSelect,
                        data,
                        'Pilih Kabupaten',
                        null,
                        elements.districtIdInput,
                        fixedDistrictId
                    );
                    elements.districtSelect.disabled = false;
                    resolve(foundSelected);
                } else {
                    resetSelect(elements.districtSelect, 'No data available', elements.districtIdInput);
                    resolve(false);
                }
            })
            .catch(error => {
                console.error('Error fetching districts:', error);
                resetSelect(elements.districtSelect, 'Error loading data', elements.districtIdInput);
                reject(error);
            });
    });
}

// Load sub-districts based on district code
function loadSubDistricts(districtCode, elements, fixedSubDistrictId) {
    return new Promise((resolve, reject) => {
        if (!districtCode) {
            resetSelect(elements.subDistrictSelect, 'Pilih Kecamatan', elements.subDistrictIdInput);
            resolve(false);
            return;
        }

        resetSelect(elements.subDistrictSelect, 'Loading...', elements.subDistrictIdInput);

        const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content') || '';
        fetch(`${baseUrl}/location/sub-districts/${districtCode}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data && data.length > 0) {
                    // Try to match using the fixed Sub-District ID we already know
                    const foundSelected = populateSelect(
                        elements.subDistrictSelect,
                        data,
                        'Pilih Kecamatan',
                        null,
                        elements.subDistrictIdInput,
                        fixedSubDistrictId
                    );
                    elements.subDistrictSelect.disabled = false;
                    resolve(foundSelected);
                } else {
                    resetSelect(elements.subDistrictSelect, 'No data available', elements.subDistrictIdInput);
                    resolve(false);
                }
            })
            .catch(error => {
                console.error('Error fetching sub-districts:', error);
                resetSelect(elements.subDistrictSelect, 'Error loading data', elements.subDistrictIdInput);
                reject(error);
            });
    });
}

// Load villages based on sub-district code
function loadVillages(subDistrictCode, elements, fixedVillageId) {
    return new Promise((resolve, reject) => {
        if (!subDistrictCode) {
            resetSelect(elements.villageSelect, 'Pilih Desa', elements.villageIdInput);
            resolve(false);
            return;
        }

        resetSelect(elements.villageSelect, 'Loading...', elements.villageIdInput);

        const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content') || '';
        fetch(`${baseUrl}/location/villages/${subDistrictCode}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data && data.length > 0) {
                    // Try to match using the fixed Village ID we already know
                    const foundSelected = populateSelect(
                        elements.villageSelect,
                        data,
                        'Pilih Desa',
                        null,
                        elements.villageIdInput,
                        fixedVillageId
                    );
                    elements.villageSelect.disabled = false;
                    resolve(foundSelected);
                } else {
                    resetSelect(elements.villageSelect, 'No data available', elements.villageIdInput);
                    resolve(false);
                }
            })
            .catch(error => {
                console.error('Error fetching villages:', error);
                resetSelect(elements.villageSelect, 'Error loading data', elements.villageIdInput);
                reject(error);
            });
    });
}

// Function to directly force set select values - updated to handle both text and numeric values
function setSelectValueDirectly(selectId, value) {
    if (value === undefined || value === null) return;

    const select = document.getElementById(selectId);
    if (!select) return;

    // Get the value type and make comparison accordingly
    const isNumeric = !isNaN(parseInt(value));

    // Value mapping for text to numeric conversion
    const valueMappings = {
        'gender': { 'Laki-Laki': '1', 'laki-laki': '1', 'Perempuan': '2', 'perempuan': '2' },
        'citizen_status': { 'WNA': '1', 'wna': '1', 'WNI': '2', 'wni': '2' },
        'birth_certificate': { 'Ada': '1', 'ada': '1', 'Tidak Ada': '2', 'tidak ada': '2' },
        'blood_type': { 'A': '1', 'B': '2', 'AB': '3', 'O': '4', 'A+': '5', 'A-': '6', 'B+': '7', 'B-': '8', 'AB+': '9', 'AB-': '10', 'O+': '11', 'O-': '12', 'Tidak Tahu': '13' },
        'religion': { 'Islam': '1', 'islam': '1', 'Kristen': '2', 'kristen': '2', 'Katholik': '3', 'katholik': '3', 'katolik': '3', 'Hindu': '4', 'hindu': '4', 'Buddha': '5', 'buddha': '5', 'Budha': '5', 'budha': '5', 'Kong Hu Cu': '6', 'kong hu cu': '6', 'konghucu': '6', 'Lainnya': '7', 'lainnya': '7' },
        'marital_status': { 'Belum Kawin': '1', 'belum kawin': '1', 'Kawin Tercatat': '2', 'kawin tercatat': '2', 'Kawin Belum Tercatat': '3', 'kawin belum tercatat': '3', 'Cerai Hidup Tercatat': '4', 'cerai hidup tercatat': '4', 'Cerai Hidup Belum Tercatat': '5', 'cerai hidup belum tercatat': '5', 'Cerai Mati': '6', 'cerai mati': '6' },
        'marital_certificate': { 'Ada': '1', 'ada': '1', 'Tidak Ada': '2', 'tidak ada': '2' },
        'divorce_certificate': { 'Ada': '1', 'ada': '1', 'Tidak Ada': '2', 'tidak ada': '2' },
        'family_status': { 'ANAK': '1', 'Anak': '1', 'anak': '1', 'KEPALA KELUARGA': '2', 'Kepala Keluarga': '2', 'kepala keluarga': '2', 'ISTRI': '3', 'Istri': '3', 'istri': '3', 'ORANG TUA': '4', 'Orang Tua': '4', 'orang tua': '4', 'MERTUA': '5', 'Mertua': '5', 'mertua': '5', 'CUCU': '6', 'Cucu': '6', 'cucu': '6', 'FAMILI LAIN': '7', 'Famili Lain': '7', 'famili lain': '7' },
        'mental_disorders': { 'Ada': '1', 'ada': '1', 'Tidak Ada': '2', 'tidak ada': '2' },
        'disabilities': { 'Fisik': '1', 'fisik': '1', 'Netra/Buta': '2', 'netra/buta': '2', 'Rungu/Wicara': '3', 'rungu/wicara': '3', 'Mental/Jiwa': '4', 'mental/jiwa': '4', 'Fisik dan Mental': '5', 'fisik dan mental': '5', 'Lainnya': '6', 'lainnya': '6' },
        'education_status': { 'Tidak/Belum Sekolah': '1', 'tidak/belum sekolah': '1', 'Belum tamat SD/Sederajat': '2', 'belum tamat sd/sederajat': '2', 'Tamat SD': '3', 'tamat sd': '3', 'SLTP/SMP/Sederajat': '4', 'sltp/smp/sederajat': '4', 'SLTA/SMA/Sederajat': '5', 'slta/sma/sederajat': '5', 'Diploma I/II': '6', 'diploma i/ii': '6', 'Akademi/Diploma III/ Sarjana Muda': '7', 'akademi/diploma iii/ sarjana muda': '7', 'Diploma IV/ Strata I/ Strata II': '8', 'diploma iv/ strata i/ strata ii': '8', 'Strata III': '9', 'strata iii': '9', 'Lainnya': '10', 'lainnya': '10' }
    };

    // Attempt conversion first
    let valueToUse = value;
    if (typeof value === 'string' && valueMappings[selectId]) {
        const lowerValue = value.toLowerCase();
        // Try to map the string value to a numeric value
        for (const [key, val] of Object.entries(valueMappings[selectId])) {
            if (key.toLowerCase() === lowerValue) {
                valueToUse = val;
                break;
            }
        }
    }

    // Method 1: Try to find the option with the exact value
    for (let i = 0; i < select.options.length; i++) {
        if (select.options[i].value === String(valueToUse)) {
            select.selectedIndex = i;
            select.dispatchEvent(new Event('change'));
            return true;
        }
    }

    // Method 2: Try case-insensitive text content match
    if (typeof value === 'string') {
        const lowerValue = value.toLowerCase();
        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].textContent.toLowerCase() === lowerValue) {
                select.selectedIndex = i;
                select.dispatchEvent(new Event('change'));
                return true;
            }
        }
    }

    // Method 3: For numeric values, try straight numeric comparison
    if (isNumeric) {
        const numValue = parseInt(value);
        for (let i = 0; i < select.options.length; i++) {
            if (parseInt(select.options[i].value) === numValue) {
                select.selectedIndex = i;
                select.dispatchEvent(new Event('change'));
                return true;
            }
        }
    }

    // If value is a number but stored as a string in the dropdown values
    if (isNumeric) {
        const numValue = String(parseInt(value));
        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].value === numValue) {
                select.selectedIndex = i;
                select.dispatchEvent(new Event('change'));
                return true;
            }
        }
    }

    return false;
}

// Function to force set all form values from citizen data
function forceSyncFormWithData() {
    // Define critical fields for selection
    const criticalFields = ['gender', 'citizen_status', 'birth_certificate', 'blood_type',
                          'religion', 'marital_status', 'marital_certificate',
                          'divorce_certificate', 'family_status', 'mental_disorders',
                          'disabilities', 'education_status'];

    // Set all fields one by one
    setSelectValueDirectly('gender', citizenData.gender);
    setSelectValueDirectly('citizen_status', citizenData.citizen_status);
    setSelectValueDirectly('birth_certificate', citizenData.birth_certificate);
    setSelectValueDirectly('blood_type', citizenData.blood_type);
    setSelectValueDirectly('religion', citizenData.religion);
    setSelectValueDirectly('marital_status', citizenData.marital_status);
    setSelectValueDirectly('marital_certificate', citizenData.marital_certificate);
    setSelectValueDirectly('divorce_certificate', citizenData.divorce_certificate);
    setSelectValueDirectly('family_status', citizenData.family_status);
    setSelectValueDirectly('mental_disorders', citizenData.mental_disorders);
    setSelectValueDirectly('disabilities', citizenData.disabilities);
    setSelectValueDirectly('education_status', citizenData.education_status);
    setSelectValueDirectly('job_type_id', citizenData.job_type_id);
}

// Initialize location dropdowns with already selected values
async function initializeLocations(elements, fixedIds) {
    // First, make sure our hidden inputs have the right values from the existing citizen data
    elements.provinceIdInput.value = fixedIds.provinceId;
    elements.districtIdInput.value = fixedIds.districtId;
    elements.subDistrictIdInput.value = fixedIds.subDistrictId;
    elements.villageIdInput.value = fixedIds.villageId;

    // If we have a province code but no district data loaded, load districts
    if (elements.provinceSelect.value) {
        const districtFound = await loadDistricts(elements.provinceSelect.value, elements, fixedIds.districtId);

        // If we found and selected the district, load sub-districts
        if (districtFound && elements.districtSelect.value) {
            const subDistrictFound = await loadSubDistricts(elements.districtSelect.value, elements, fixedIds.subDistrictId);

            // If we found and selected the sub-district, load villages
            if (subDistrictFound && elements.subDistrictSelect.value) {
                await loadVillages(elements.subDistrictSelect.value, elements, fixedIds.villageId);
            }
        }
    }
}

// Set up location dropdown event listeners
function setupLocationListeners(elements, fixedIds) {
    // Province change handler
    elements.provinceSelect.addEventListener('change', async function() {
        const provinceCode = this.value;

        // Update the hidden input with the ID
        updateHiddenInput(this, elements.provinceIdInput);

        // Reset and load new districts
        await loadDistricts(provinceCode, elements, null);

        // Reset sub-district and village
        resetSelect(elements.subDistrictSelect, 'Pilih Kecamatan', elements.subDistrictIdInput);
        resetSelect(elements.villageSelect, 'Pilih Desa', elements.villageIdInput);
    });

    // District change handler
    elements.districtSelect.addEventListener('change', async function() {
        const districtCode = this.value;

        // Update hidden input with ID
        updateHiddenInput(this, elements.districtIdInput);

        // Reset and load new sub-districts
        await loadSubDistricts(districtCode, elements, null);

        // Reset village
        resetSelect(elements.villageSelect, 'Pilih Desa', elements.villageIdInput);
    });

    // Sub-district change handler
    elements.subDistrictSelect.addEventListener('change', async function() {
        const subDistrictCode = this.value;

        // Update hidden input with ID
        updateHiddenInput(this, elements.subDistrictIdInput);

        // Reset and load new villages
        await loadVillages(subDistrictCode, elements, null);
    });

    // Village change handler
    elements.villageSelect.addEventListener('change', function() {
        // Update hidden input with ID
        updateHiddenInput(this, elements.villageIdInput);
    });
}

// Initialize the update page
document.addEventListener('DOMContentLoaded', function() {
    // Get citizen data from hidden input or page variable
    try {
        if (typeof window.citizenData !== 'undefined') {
            citizenData = window.citizenData;
        }
    } catch (error) {
        console.error('Error accessing citizen data:', error);
    }

    // Cache DOM elements
    const elements = cacheDOMElements();

    // Get fixed IDs for locations
    const fixedIds = {
        provinceId: elements.provinceIdInput.value || '',
        districtId: elements.districtIdInput.value || '',
        subDistrictId: elements.subDistrictIdInput.value || '',
        villageId: elements.villageIdInput.value || ''
    };

    // Initialize locations
    initializeLocations(elements, fixedIds);

    // Set up location dropdown listeners
    setupLocationListeners(elements, fixedIds);

    // Apply date formatting and force select values
    setTimeout(function() {
        // Format dates using the common function
        reformatAllDateInputs();

        // Force set select values from citizen data
        forceSyncFormWithData();
    }, 300);
});

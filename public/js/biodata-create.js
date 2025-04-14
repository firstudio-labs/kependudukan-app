/**
 * JavaScript functionality for the biodata create page
 */

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

// Helper function to populate select options
function populateSelect(select, data, defaultText, hiddenInput = null) {
    try {
        const fragment = document.createDocumentFragment();
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = defaultText;
        fragment.appendChild(defaultOption);

        if (Array.isArray(data)) {
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.code;
                option.setAttribute('data-id', item.id);
                option.textContent = item.name;
                fragment.appendChild(option);
            });
        }

        select.innerHTML = '';
        select.appendChild(fragment);
        select.disabled = false;

        return true;
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
    } else {
        hiddenInput.value = '';
    }
}

// Load districts based on province code
function loadDistricts(provinceCode, elements) {
    if (!provinceCode) {
        resetSelect(elements.districtSelect, 'Pilih Kabupaten', elements.districtIdInput);
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
                populateSelect(elements.districtSelect, data, 'Pilih Kabupaten', elements.districtIdInput);
                elements.districtSelect.disabled = false;
            } else {
                resetSelect(elements.districtSelect, 'No data available', elements.districtIdInput);
            }
        })
        .catch(error => {
            console.error('Error fetching districts:', error);
            resetSelect(elements.districtSelect, 'Error loading data', elements.districtIdInput);
        });
}

// Load sub-districts based on district code
function loadSubDistricts(districtCode, elements) {
    if (!districtCode) {
        resetSelect(elements.subDistrictSelect, 'Pilih Kecamatan', elements.subDistrictIdInput);
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
                populateSelect(elements.subDistrictSelect, data, 'Pilih Kecamatan', elements.subDistrictIdInput);
                elements.subDistrictSelect.disabled = false;
            } else {
                resetSelect(elements.subDistrictSelect, 'No data available', elements.subDistrictIdInput);
            }
        })
        .catch(error => {
            console.error('Error fetching sub-districts:', error);
            resetSelect(elements.subDistrictSelect, 'Error loading data', elements.subDistrictIdInput);
        });
}

// Load villages based on sub-district code
function loadVillages(subDistrictCode, elements) {
    if (!subDistrictCode) {
        resetSelect(elements.villageSelect, 'Pilih Desa', elements.villageIdInput);
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
                populateSelect(elements.villageSelect, data, 'Pilih Desa', elements.villageIdInput);
                elements.villageSelect.disabled = false;
            } else {
                resetSelect(elements.villageSelect, 'No data available', elements.villageIdInput);
            }
        })
        .catch(error => {
            console.error('Error fetching villages:', error);
            resetSelect(elements.villageSelect, 'Error loading data', elements.villageIdInput);
        });
}

// Set up location dropdown event listeners
function setupLocationListeners(elements) {
    // Province change handler
    elements.provinceSelect.addEventListener('change', function() {
        const provinceCode = this.value;

        // Update the hidden input with the ID
        updateHiddenInput(this, elements.provinceIdInput);

        // Reset and load new districts
        loadDistricts(provinceCode, elements);

        // Reset sub-district and village
        resetSelect(elements.subDistrictSelect, 'Pilih Kecamatan', elements.subDistrictIdInput);
        resetSelect(elements.villageSelect, 'Pilih Desa', elements.villageIdInput);
    });

    // District change handler
    elements.districtSelect.addEventListener('change', function() {
        const districtCode = this.value;

        // Update hidden input with ID
        updateHiddenInput(this, elements.districtIdInput);

        // Reset and load new sub-districts
        loadSubDistricts(districtCode, elements);

        // Reset village
        resetSelect(elements.villageSelect, 'Pilih Desa', elements.villageIdInput);
    });

    // Sub-district change handler
    elements.subDistrictSelect.addEventListener('change', function() {
        const subDistrictCode = this.value;

        // Update hidden input with ID
        updateHiddenInput(this, elements.subDistrictIdInput);

        // Reset and load new villages
        loadVillages(subDistrictCode, elements);
    });

    // Village change handler
    elements.villageSelect.addEventListener('change', function() {
        // Update hidden input with ID
        updateHiddenInput(this, elements.villageIdInput);
    });
}

// Initialize the create page
document.addEventListener('DOMContentLoaded', function() {
    // Cache DOM elements
    const elements = cacheDOMElements();

    // Set up location dropdown listeners
    setupLocationListeners(elements);

    // Ensure the province select has a default "Pilih Provinsi" option
    if (elements.provinceSelect && elements.provinceSelect.options.length > 0) {
        // Update the hidden input when a province is already selected
        if (elements.provinceSelect.value) {
            updateHiddenInput(elements.provinceSelect, elements.provinceIdInput);
        }
    }
});

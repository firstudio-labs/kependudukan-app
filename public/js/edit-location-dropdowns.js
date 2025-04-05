/**
 * Helper functions for managing location dropdowns in edit forms
 */

// Setup location dropdowns with pre-selected values
function setupLocationHandlers() {
    const provinceSelect = document.getElementById('province_code');
    const districtSelect = document.getElementById('district_code');
    const subDistrictSelect = document.getElementById('subdistrict_code');
    const villageSelect = document.getElementById('village_code');

    const provinceIdInput = document.getElementById('province_id');
    const districtIdInput = document.getElementById('district_id');
    const subDistrictIdInput = document.getElementById('subdistrict_id');
    const villageIdInput = document.getElementById('village_id');

    if (!provinceSelect || !districtSelect || !subDistrictSelect || !villageSelect) {
        console.error('Location dropdowns not found');
        return;
    }

    // Province change handler
    provinceSelect.addEventListener('change', function() {
        const provinceCode = this.value;
        updateHiddenInput(this, provinceIdInput);

        resetSelect(districtSelect, 'Loading...', districtIdInput);
        resetSelect(subDistrictSelect, 'Pilih Kecamatan', subDistrictIdInput);
        resetSelect(villageSelect, 'Pilih Desa', villageIdInput);

        if (provinceCode) {
            fetch(`/location/districts/${provinceCode}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        populateOptions(districtSelect, data, 'Pilih Kabupaten', districtIdInput);
                        districtSelect.disabled = false;
                    } else {
                        resetSelect(districtSelect, 'No data available', districtIdInput);
                    }
                })
                .catch(error => {
                    console.error('Error loading districts:', error);
                    resetSelect(districtSelect, 'Error loading data', districtIdInput);
                });
        }
    });

    // District change handler
    districtSelect.addEventListener('change', function() {
        const districtCode = this.value;
        updateHiddenInput(this, districtIdInput);

        resetSelect(subDistrictSelect, 'Loading...', subDistrictIdInput);
        resetSelect(villageSelect, 'Pilih Desa', villageIdInput);

        if (districtCode) {
            fetch(`/location/sub-districts/${districtCode}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        populateOptions(subDistrictSelect, data, 'Pilih Kecamatan', subDistrictIdInput);
                        subDistrictSelect.disabled = false;
                    } else {
                        resetSelect(subDistrictSelect, 'No data available', subDistrictIdInput);
                    }
                })
                .catch(error => {
                    console.error('Error loading sub-districts:', error);
                    resetSelect(subDistrictSelect, 'Error loading data', subDistrictIdInput);
                });
        }
    });

    // Sub-district change handler
    subDistrictSelect.addEventListener('change', function() {
        const subDistrictCode = this.value;
        updateHiddenInput(this, subDistrictIdInput);

        resetSelect(villageSelect, 'Loading...', villageIdInput);

        if (subDistrictCode) {
            fetch(`/location/villages/${subDistrictCode}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        populateOptions(villageSelect, data, 'Pilih Desa', villageIdInput);
                        villageSelect.disabled = false;
                    } else {
                        resetSelect(villageSelect, 'No data available', villageIdInput);
                    }
                })
                .catch(error => {
                    console.error('Error loading villages:', error);
                    resetSelect(villageSelect, 'Error loading data', villageIdInput);
                });
        }
    });

    // Village change handler
    villageSelect.addEventListener('change', function() {
        updateHiddenInput(this, villageIdInput);
    });
}

// Helper function to update hidden input
function updateHiddenInput(select, hiddenInput) {
    if (!hiddenInput) return;

    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption && selectedOption.hasAttribute('data-id')) {
        hiddenInput.value = selectedOption.getAttribute('data-id');
    } else {
        hiddenInput.value = '';
    }
}

// Helper function to reset select options
function resetSelect(select, defaultText = 'Pilih', hiddenInput = null) {
    if (!select) return;

    select.innerHTML = `<option value="">${defaultText}</option>`;
    select.disabled = true;

    if (hiddenInput) {
        hiddenInput.value = '';
    }
}

// Helper function to populate select options
function populateOptions(select, data, defaultText, hiddenInput = null) {
    if (!select) return;

    try {
        select.innerHTML = `<option value="">${defaultText}</option>`;

        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.code;
            option.textContent = item.name;
            option.setAttribute('data-id', item.id);
            select.appendChild(option);
        });

        select.disabled = false;

        if (hiddenInput) {
            hiddenInput.value = '';
        }
    } catch (error) {
        console.error('Error populating options:', error);
        select.innerHTML = `<option value="">Error loading data</option>`;
        select.disabled = true;

        if (hiddenInput) {
            hiddenInput.value = '';
        }
    }
}

// Populate location dropdowns with existing data
function populateLocationDropdowns(provinceId, districtId, subDistrictId, villageId) {
    const provinceSelect = document.getElementById('province_code');
    const provinceIdInput = document.getElementById('province_id');

    if (!provinceSelect || !provinceIdInput) return;

    // Set province ID in the hidden input
    if (provinceId) {
        provinceIdInput.value = provinceId;

        // Find and select the province option
        for (let i = 0; i < provinceSelect.options.length; i++) {
            const option = provinceSelect.options[i];
            if (option.hasAttribute('data-id') && option.getAttribute('data-id') == provinceId) {
                provinceSelect.selectedIndex = i;

                // Load districts for the selected province
                loadDistricts(provinceSelect.value, districtId, subDistrictId, villageId);
                break;
            }
        }
    }
}

// Add a simple cache for location data
const locationCache = {
    districts: {},
    subDistricts: {},
    villages: {}
};

// Load districts for a province and optionally select one
function loadDistricts(provinceCode, districtId, subDistrictId, villageId) {
    if (!provinceCode) return;

    const districtSelect = document.getElementById('district_code');
    const districtIdInput = document.getElementById('district_id');

    if (!districtSelect || !districtIdInput) return;

    districtSelect.innerHTML = '<option value="">Loading...</option>';
    districtSelect.disabled = true;

    // Check cache first
    if (locationCache.districts[provinceCode]) {
        populateDistrictOptions(locationCache.districts[provinceCode], districtId, subDistrictId, villageId);
        return;
    }

    fetch(`/location/districts/${provinceCode}`)
        .then(response => response.json())
        .then(data => {
            // Cache the results
            locationCache.districts[provinceCode] = data;
            populateDistrictOptions(data, districtId, subDistrictId, villageId);
        })
        .catch(error => {
            console.error('Error loading districts:', error);
            districtSelect.innerHTML = '<option value="">Error loading data</option>';
        });
}

// Helper function to populate district options and trigger next level loading
function populateDistrictOptions(data, districtId, subDistrictId, villageId) {
    const districtSelect = document.getElementById('district_code');
    const districtIdInput = document.getElementById('district_id');

    districtSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';

    if (data && data.length > 0) {
        data.forEach(district => {
            const option = document.createElement('option');
            option.value = district.code;
            option.textContent = district.name;
            option.setAttribute('data-id', district.id);

            // Select if matching the district_id
            if (districtId && district.id == districtId) {
                option.selected = true;
                districtIdInput.value = district.id;
            }

            districtSelect.appendChild(option);
        });

        districtSelect.disabled = false;

        // If a district was selected, load its sub-districts
        if (districtId) {
            const selectedOption = districtSelect.options[districtSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                // Load sub-districts and villages in parallel rather than sequentially
                let districtCode = selectedOption.value;
                loadSubDistricts(districtCode, subDistrictId, villageId);
            }
        }
    }
}

// Load sub-districts for a district and optionally select one
function loadSubDistricts(districtCode, subDistrictId, villageId) {
    if (!districtCode) return;

    const subDistrictSelect = document.getElementById('subdistrict_code');
    const subDistrictIdInput = document.getElementById('subdistrict_id');

    if (!subDistrictSelect || !subDistrictIdInput) return;

    subDistrictSelect.innerHTML = '<option value="">Loading...</option>';
    subDistrictSelect.disabled = true;

    // Check cache first
    if (locationCache.subDistricts[districtCode]) {
        populateSubDistrictOptions(locationCache.subDistricts[districtCode], subDistrictId, villageId);
        return;
    }

    fetch(`/location/sub-districts/${districtCode}`)
        .then(response => response.json())
        .then(data => {
            // Cache the results
            locationCache.subDistricts[districtCode] = data;
            populateSubDistrictOptions(data, subDistrictId, villageId);
        })
        .catch(error => {
            console.error('Error loading sub-districts:', error);
            subDistrictSelect.innerHTML = '<option value="">Error loading data</option>';
        });
}

// Helper function to populate sub-district options and trigger next level loading
function populateSubDistrictOptions(data, subDistrictId, villageId) {
    const subDistrictSelect = document.getElementById('subdistrict_code');
    const subDistrictIdInput = document.getElementById('subdistrict_id');

    subDistrictSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';

    if (data && data.length > 0) {
        data.forEach(subDistrict => {
            const option = document.createElement('option');
            option.value = subDistrict.code;
            option.textContent = subDistrict.name;
            option.setAttribute('data-id', subDistrict.id);

            // Select if matching the subdistrict_id
            if (subDistrictId && subDistrict.id == subDistrictId) {
                option.selected = true;
                subDistrictIdInput.value = subDistrict.id;
            }

            subDistrictSelect.appendChild(option);
        });

        subDistrictSelect.disabled = false;

        // If a sub-district was selected, load its villages
        if (subDistrictId) {
            const selectedOption = subDistrictSelect.options[subDistrictSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                loadVillages(selectedOption.value, villageId);
            }
        }
    }
}

// Load villages for a sub-district and optionally select one
function loadVillages(subDistrictCode, villageId) {
    if (!subDistrictCode) return;

    const villageSelect = document.getElementById('village_code');
    const villageIdInput = document.getElementById('village_id');

    if (!villageSelect || !villageIdInput) return;

    villageSelect.innerHTML = '<option value="">Loading...</option>';
    villageSelect.disabled = true;

    // Check cache first
    if (locationCache.villages[subDistrictCode]) {
        populateVillageOptions(locationCache.villages[subDistrictCode], villageId);
        return;
    }

    fetch(`/location/villages/${subDistrictCode}`)
        .then(response => response.json())
        .then(data => {
            // Cache the results
            locationCache.villages[subDistrictCode] = data;
            populateVillageOptions(data, villageId);
        })
        .catch(error => {
            console.error('Error loading villages:', error);
            villageSelect.innerHTML = '<option value="">Error loading data</option>';
        });
}

// Helper function to populate village options
function populateVillageOptions(data, villageId) {
    const villageSelect = document.getElementById('village_code');
    const villageIdInput = document.getElementById('village_id');

    villageSelect.innerHTML = '<option value="">Pilih Desa</option>';

    if (data && data.length > 0) {
        data.forEach(village => {
            const option = document.createElement('option');
            option.value = village.code;
            option.textContent = village.name;
            option.setAttribute('data-id', village.id);

            // Select if matching the village_id
            if (villageId && village.id == villageId) {
                option.selected = true;
                villageIdInput.value = village.id;
            }

            villageSelect.appendChild(option);
        });

        villageSelect.disabled = false;
    }
}

// Initial function to trigger loading of all location dropdowns at once on page load
function initLocationDropdowns() {
    const provinceId = document.getElementById('province_id').value;
    const districtId = document.getElementById('district_id').value;
    const subDistrictId = document.getElementById('subdistrict_id').value;
    const villageId = document.getElementById('village_id').value;

    if (provinceId) {
        const provinceSelect = document.getElementById('province_code');

        // Find and select the province option
        for (let i = 0; i < provinceSelect.options.length; i++) {
            const option = provinceSelect.options[i];
            if (option.hasAttribute('data-id') && option.getAttribute('data-id') == provinceId) {
                provinceSelect.selectedIndex = i;

                // Load districts with the selected province code
                loadDistricts(provinceSelect.value, districtId, subDistrictId, villageId);
                break;
            }
        }
    }

    // Set up event handlers for dropdown changes
    setupLocationHandlers();
}

// Auto-initialize when the document is ready
document.addEventListener('DOMContentLoaded', function() {
    initLocationDropdowns();
});

/**
 * Initialize the signing officer dropdown with data from the penandatangan table and select current value
 * @param {string} endpointUrl - URL for fetching signing officers data
 * @param {string} currentValue - Current value to pre-select
 */
function initializeSigningDropdownWithValue(endpointUrl, currentValue) {
    const signingSelect = document.getElementById('signing');

    if (!signingSelect) return;

    // Reset the dropdown
    signingSelect.innerHTML = '<option value="">Pilih Penandatangan</option>';

    // Fetch signing officers from the endpoint
    fetch(endpointUrl)
        .then(response => response.json())
        .then(data => {
            if (Array.isArray(data) && data.length > 0) {
                // Populate the select options
                data.forEach(officer => {
                    const option = document.createElement('option');
                    option.value = officer.judul;
                    option.textContent = `${officer.judul} - ${officer.keterangan}`;

                    // Pre-select the current value
                    if (currentValue && officer.judul === currentValue) {
                        option.selected = true;
                    }

                    signingSelect.appendChild(option);
                });

                signingSelect.disabled = false;
            } else {
                signingSelect.innerHTML = '<option value="">Tidak ada data penandatangan</option>';
            }
        })
        .catch(error => {
            console.error('Error loading signing officers data:', error);
            signingSelect.innerHTML = '<option value="">Error loading data</option>';

            // If there's a current value, add it as an option to preserve the data
            if (currentValue) {
                const option = document.createElement('option');
                option.value = currentValue;
                option.textContent = currentValue;
                option.selected = true;
                signingSelect.appendChild(option);
            }
        });
}

/**
 * Initialize the signing officer dropdown with data from the penandatangan table
 * @param {string} endpointUrl - URL for fetching signing officers data (optional)
 */
function initializeSigningDropdown(endpointUrl) {
    const signingSelect = document.getElementById('signing');

    if (!signingSelect) return;

    // Since this is the edit form, we should already have the correct value selected.
    // We only need to add more options if they're not already there.

    // If select already has multiple options, we don't need to do anything
    if (signingSelect.options.length > 1) {
        return;
    }

    // Check if we have signer data in the global scope
    if (typeof signerOptions !== 'undefined' && signerOptions && signerOptions.length > 0) {
        // Get currently selected value to restore it after we rebuild the dropdown
        const currentValue = signingSelect.value;

        // Clear existing options
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

            // If this is the previously selected value, mark it as selected
            if (option.value === currentValue) {
                option.selected = true;
            }

            signingSelect.appendChild(option);
        });
    } else if (endpointUrl) {
        // If we don't have the data in the global scope, try to fetch it
        // Get currently selected value to restore it after we rebuild the dropdown
        const currentValue = signingSelect.value;

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

                        // If this is the previously selected value, mark it as selected
                        if (option.value === currentValue) {
                            option.selected = true;
                        }

                        signingSelect.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching signing officers for edit:', error);
            });
    }
}

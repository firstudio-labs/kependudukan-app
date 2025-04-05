/**
 * Location Selector Script
 * Handles cascading dropdowns for provinces, districts, sub-districts, and villages
 */

document.addEventListener('DOMContentLoaded', function() {
    const provinceSelect = document.getElementById('province_code');
    const districtSelect = document.getElementById('district_code');
    const subDistrictSelect = document.getElementById('sub_district_code');
    const villageSelect = document.getElementById('village_code');

    // Hidden inputs for IDs
    const provinceIdInput = document.getElementById('province_id');
    const districtIdInput = document.getElementById('district_id');
    const subDistrictIdInput = document.getElementById('sub_district_id');
    const villageIdInput = document.getElementById('village_id');

    // Check if all required elements exist
    if (!provinceSelect || !districtSelect || !subDistrictSelect || !villageSelect) {
        console.error('Some location selection elements are missing');
        return;
    }

    // Helper function to reset select options
    function resetSelect(select, defaultText = 'Pilih', hiddenInput = null) {
        select.innerHTML = `<option value="">${defaultText}</option>`;
        select.disabled = true;
        if (hiddenInput) hiddenInput.value = '';
    }

    // Helper function to populate select options with code as value and id as data attribute
    function populateSelect(select, data, defaultText, hiddenInput = null) {
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

            if (hiddenInput) hiddenInput.value = '';
        } catch (error) {
            select.innerHTML = `<option value="">Error loading data</option>`;
            select.disabled = true;
            if (hiddenInput) hiddenInput.value = '';
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

    // Get base URL from a meta tag or directly from window.location
    const getBaseUrl = () => {
        const metaUrl = document.querySelector('meta[name="base-url"]');
        return metaUrl ? metaUrl.getAttribute('content') : window.location.origin;
    };

    const baseUrl = getBaseUrl();

    // Province change handler
    provinceSelect.addEventListener('change', function() {
        const provinceCode = this.value;

        // Update the hidden input with the ID
        updateHiddenInput(this, provinceIdInput);

        resetSelect(districtSelect, 'Loading...', districtIdInput);
        resetSelect(subDistrictSelect, 'Pilih Kecamatan', subDistrictIdInput);
        resetSelect(villageSelect, 'Pilih Desa', villageIdInput);

        if (provinceCode) {
            fetch(`${baseUrl}/location/districts/${provinceCode}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.length > 0) {
                        populateSelect(districtSelect, data, 'Pilih Kabupaten', districtIdInput);
                        districtSelect.disabled = false;
                    } else {
                        resetSelect(districtSelect, 'No data available', districtIdInput);
                    }
                })
                .catch(error => {
                    console.error('Error fetching districts:', error);
                    resetSelect(districtSelect, 'Error loading data', districtIdInput);
                });
        }
    });

    // District change handler
    districtSelect.addEventListener('change', function() {
        const districtCode = this.value;
        // Update hidden input with ID
        updateHiddenInput(this, districtIdInput);

        resetSelect(subDistrictSelect, 'Loading...', subDistrictIdInput);
        resetSelect(villageSelect, 'Pilih Desa', villageIdInput);

        if (districtCode) {
            fetch(`${baseUrl}/location/sub-districts/${districtCode}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.length > 0) {
                        populateSelect(subDistrictSelect, data, 'Pilih Kecamatan', subDistrictIdInput);
                        subDistrictSelect.disabled = false;
                    } else {
                        resetSelect(subDistrictSelect, 'No data available', subDistrictIdInput);
                    }
                })
                .catch(error => {
                    console.error('Error fetching sub-districts:', error);
                    resetSelect(subDistrictSelect, 'Error loading data', subDistrictIdInput);
                });
        }
    });

    // Sub-district change handler
    subDistrictSelect.addEventListener('change', function() {
        const subDistrictCode = this.value;
        // Update hidden input with ID
        updateHiddenInput(this, subDistrictIdInput);

        resetSelect(villageSelect, 'Loading...', villageIdInput);

        if (subDistrictCode) {
            fetch(`${baseUrl}/location/villages/${subDistrictCode}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.length > 0) {
                        populateSelect(villageSelect, data, 'Pilih Desa', villageIdInput);
                        villageSelect.disabled = false;
                    } else {
                        resetSelect(villageSelect, 'No data available', villageIdInput);
                    }
                })
                .catch(error => {
                    console.error('Error fetching villages:', error);
                    resetSelect(villageSelect, 'Error loading data', villageIdInput);
                });
        }
    });

    // Village change handler
    villageSelect.addEventListener('change', function() {
        // Update hidden input with ID
        updateHiddenInput(this, villageIdInput);
    });

    // Form validation to check if both IDs and codes are set
    document.querySelector('form').addEventListener('submit', function(e) {
        const provinceId = document.getElementById('province_id').value;
        const districtId = document.getElementById('district_id').value;
        const subDistrictId = document.getElementById('sub_district_id').value;
        const villageId = document.getElementById('village_id').value;

        if (!provinceId || !districtId || !subDistrictId || !villageId) {
            e.preventDefault();
            showWarningAlert('Silakan pilih Provinsi, Kabupaten, Kecamatan, dan Desa');
            return false;
        }
    });
});

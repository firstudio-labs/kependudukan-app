/**
 * Location Selector Script for Edit Mode
 * Handles cascading dropdowns for provinces, districts, sub-districts, and villages
 * with support for pre-populated data from an existing citizen record
 */

document.addEventListener('DOMContentLoaded', function() {
    // Cache DOM elements
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

    // Store fixed ID values that we already know from the page load
    const fixedProvinceId = citizenData.province_id;
    const fixedDistrictId = citizenData.district_id;
    const fixedSubDistrictId = citizenData.sub_district_id;
    const fixedVillageId = citizenData.village_id;

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

    // Get base URL from a meta tag or directly from window.location
    const getBaseUrl = () => {
        const metaUrl = document.querySelector('meta[name="base-url"]');
        return metaUrl ? metaUrl.getAttribute('content') : window.location.origin;
    };

    const baseUrl = getBaseUrl();

    // Load districts based on province code
    function loadDistricts(provinceCode) {
        return new Promise((resolve, reject) => {
            if (!provinceCode) {
                resetSelect(districtSelect, 'Pilih Kabupaten', districtIdInput);
                resolve(false);
                return;
            }

            resetSelect(districtSelect, 'Loading...', districtIdInput);

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
                        const foundSelected = populateSelect(districtSelect, data, 'Pilih Kabupaten', null, districtIdInput, fixedDistrictId);
                        districtSelect.disabled = false;
                        resolve(foundSelected);
                    } else {
                        resetSelect(districtSelect, 'No data available', districtIdInput);
                        resolve(false);
                    }
                })
                .catch(error => {
                    console.error('Error fetching districts:', error);
                    resetSelect(districtSelect, 'Error loading data', districtIdInput);
                    reject(error);
                });
        });
    }

    // Load sub-districts based on district code
    function loadSubDistricts(districtCode) {
        return new Promise((resolve, reject) => {
            if (!districtCode) {
                resetSelect(subDistrictSelect, 'Pilih Kecamatan', subDistrictIdInput);
                resolve(false);
                return;
            }

            resetSelect(subDistrictSelect, 'Loading...', subDistrictIdInput);

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
                        const foundSelected = populateSelect(subDistrictSelect, data, 'Pilih Kecamatan', null, subDistrictIdInput, fixedSubDistrictId);
                        subDistrictSelect.disabled = false;
                        resolve(foundSelected);
                    } else {
                        resetSelect(subDistrictSelect, 'No data available', subDistrictIdInput);
                        resolve(false);
                    }
                })
                .catch(error => {
                    console.error('Error fetching sub-districts:', error);
                    resetSelect(subDistrictSelect, 'Error loading data', subDistrictIdInput);
                    reject(error);
                });
        });
    }

    // Load villages based on sub-district code
    function loadVillages(subDistrictCode) {
        return new Promise((resolve, reject) => {
            if (!subDistrictCode) {
                resetSelect(villageSelect, 'Pilih Desa', villageIdInput);
                resolve(false);
                return;
            }

            resetSelect(villageSelect, 'Loading...', villageIdInput);

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
                        const foundSelected = populateSelect(villageSelect, data, 'Pilih Desa', null, villageIdInput, fixedVillageId);
                        villageSelect.disabled = false;
                        resolve(foundSelected);
                    } else {
                        resetSelect(villageSelect, 'No data available', villageIdInput);
                        resolve(false);
                    }
                })
                .catch(error => {
                    console.error('Error fetching villages:', error);
                    resetSelect(villageSelect, 'Error loading data', villageIdInput);
                    reject(error);
                });
        });
    }

    // Initialize location dropdowns with already selected values
    async function initializeLocations() {
        // First, make sure our hidden inputs have the right values from the existing citizen data
        provinceIdInput.value = fixedProvinceId;
        districtIdInput.value = fixedDistrictId;
        subDistrictIdInput.value = fixedSubDistrictId;
        villageIdInput.value = fixedVillageId;

        // If the province dropdown doesn't have a selected value with matching ID,
        // we need to find which province code corresponds to our province ID
        if (!provinceSelect.querySelector(`option[data-id="${fixedProvinceId}"]`)) {
            // This is unlikely since we populate provinces server-side
        }

        // If we have a province code but no district data loaded, load districts
        if (provinceSelect.value) {
            const districtFound = await loadDistricts(provinceSelect.value);

            // If we found and selected the district, load sub-districts
            if (districtFound && districtSelect.value) {
                const subDistrictFound = await loadSubDistricts(districtSelect.value);

                // If we found and selected the sub-district, load villages
                if (subDistrictFound && subDistrictSelect.value) {
                    await loadVillages(subDistrictSelect.value);
                }
            }
        }
    }

    // Initialize locations on page load
    initializeLocations();

    // Province change handler
    provinceSelect.addEventListener('change', async function() {
        const provinceCode = this.value;

        // Update the hidden input with the ID
        updateHiddenInput(this, provinceIdInput);

        // Reset and load new districts
        await loadDistricts(provinceCode);

        // Reset sub-district and village
        resetSelect(subDistrictSelect, 'Pilih Kecamatan', subDistrictIdInput);
        resetSelect(villageSelect, 'Pilih Desa', villageIdInput);
    });

    // District change handler
    districtSelect.addEventListener('change', async function() {
        const districtCode = this.value;

        // Update hidden input with ID
        updateHiddenInput(this, districtIdInput);

        // Reset and load new sub-districts
        await loadSubDistricts(districtCode);

        // Reset village
        resetSelect(villageSelect, 'Pilih Desa', villageIdInput);
    });

    // Sub-district change handler
    subDistrictSelect.addEventListener('change', async function() {
        const subDistrictCode = this.value;

        // Update hidden input with ID
        updateHiddenInput(this, subDistrictIdInput);

        // Reset and load new villages
        await loadVillages(subDistrictCode);
    });

    // Village change handler
    villageSelect.addEventListener('change', function() {
        // Update hidden input with ID
        updateHiddenInput(this, villageIdInput);
    });

    // Format dates to YYYY-MM-DD for HTML date inputs
    function formatDateForInput(dateString) {
        if (!dateString || dateString === " " || dateString === "null") return "";

        // Check if the date is already in yyyy-MM-dd format
        if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
            return dateString;
        }

        try {
            // Handle different possible date formats
            let date;

            // Check for dd/MM/yyyy format
            if (/^\d{2}\/\d{2}\/\d{4}$/.test(dateString)) {
                const parts = dateString.split('/');
                date = new Date(parseInt(parts[2]), parseInt(parts[1]) - 1, parseInt(parts[0]));
            } else {
                // Otherwise try to parse the date directly
                date = new Date(dateString);
            }

            // Make sure the date is valid
            if (isNaN(date.getTime())) {
                return "";
            }

            // Format to YYYY-MM-DD
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        } catch (error) {
            return "";
        }
    }

    // Apply date formatting to all date input fields
    function reformatAllDateInputs() {
        const dateInputs = document.querySelectorAll('input[type="date"]');
        dateInputs.forEach(input => {
            const originalValue = input.getAttribute('value') || input.value;

            if (originalValue && originalValue !== " ") {
                const formattedDate = formatDateForInput(originalValue);
                input.value = formattedDate;
            }
        });

        // Specifically check these fields
        const dateFields = ['birth_date', 'marriage_date', 'divorce_certificate_date'];
        dateFields.forEach(fieldId => {
            const input = document.getElementById(fieldId);
            if (input) {
                const originalValue = input.getAttribute('value') || input.value;

                if (originalValue && originalValue !== " ") {
                    // For problematic dates, manually construct from parts if needed
                    if (/^\d{2}\/\d{2}\/\d{4}$/.test(originalValue)) {
                        const parts = originalValue.split('/');
                        const formattedDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
                        input.value = formattedDate;
                    } else {
                        const formattedDate = formatDateForInput(originalValue);
                        input.value = formattedDate;
                    }
                }
            }
        });
    }

    // Function to directly force set select values - handles both text and numeric values
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

    // Form validation to check both date formats and location IDs
    document.querySelector('form').addEventListener('submit', function(e) {
        // Check location IDs
        const provinceId = document.getElementById('province_id').value;
        const districtId = document.getElementById('district_id').value;
        const subDistrictId = document.getElementById('sub_district_id').value;
        const villageId = document.getElementById('village_id').value;

        if (!provinceId || !districtId || !subDistrictId || !villageId) {
            e.preventDefault();
            showWarningAlert('Silakan pilih Provinsi, Kabupaten, Kecamatan, dan Desa');
            return false;
        }

        // Ensure all date fields are correctly formatted
        const dateInputs = document.querySelectorAll('input[type="date"]');
        let allDatesValid = true;

        dateInputs.forEach(input => {
            if (input.value && !/^\d{4}-\d{2}-\d{2}$/.test(input.value)) {
                // Try to fix it one last time
                const fixedDate = formatDateForInput(input.value);
                if (fixedDate && /^\d{4}-\d{2}-\d{2}$/.test(fixedDate)) {
                    input.value = fixedDate;
                } else {
                    allDatesValid = false;
                    e.preventDefault();
                    showErrorAlert(`Format tanggal untuk "${input.id}" tidak valid. Format yang benar adalah YYYY-MM-DD.`);
                    return false;
                }
            }
        });

        if (!allDatesValid) {
            e.preventDefault();
            return false;
        }
    });

    // Apply date formatting and force select values - increase timeout to ensure DOM is ready
    setTimeout(function() {
        // Format dates
        reformatAllDateInputs();

        // Force set select values from citizen data
        forceSyncFormWithData();
    }, 300); // Increased timeout to 300ms
});

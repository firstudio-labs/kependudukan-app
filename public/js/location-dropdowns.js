/**
 * Location Dropdowns Helper
 * Contains functions for handling location dropdowns and citizen data
 */

// Helper function to populate location dropdowns when selecting citizen by NIK or name
async function populateLocationDropdowns(provinceId, districtId, subDistrictId, villageId) {
    try {
        // Set the hidden inputs
        $('#province_id').val(provinceId || '');
        $('#district_id').val(districtId || '');
        $('#subdistrict_id').val(subDistrictId || '');
        $('#village_id').val(villageId || '');

        // If we have a province ID, fetch its data
        if (provinceId) {
            // Get province data from API
            const provinceResponse = await fetch('/location/provinces');
            const provinceData = await provinceResponse.json();

            // Find province by ID
            let province = null;
            if (provinceData && Array.isArray(provinceData)) {
                province = provinceData.find(p => p.id == provinceId);
            } else if (provinceData && provinceData.data && Array.isArray(provinceData.data)) {
                province = provinceData.data.find(p => p.id == provinceId);
            }

            if (province) {
                // Set province dropdown value
                $('#province_code').val(province.code);

                // If we have a district ID, fetch district data
                if (districtId) {
                    // Fetch districts for this province
                    const districtResponse = await fetch(`/location/districts/${province.code}`);
                    const districtData = await districtResponse.json();

                    // Make sure we have valid district data
                    let districts = districtData;
                    if (districtData && districtData.data && Array.isArray(districtData.data)) {
                        districts = districtData.data;
                    }

                    if (Array.isArray(districts) && districts.length > 0) {
                        // Clear and populate district select
                        const districtSelect = document.getElementById('district_code');
                        districtSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';

                        // Add district options
                        districts.forEach(district => {
                            const option = document.createElement('option');
                            option.value = district.code;
                            option.textContent = district.name;
                            option.setAttribute('data-id', district.id);
                            districtSelect.appendChild(option);
                        });

                        // Find and select the matching district
                        const selectedDistrict = districts.find(d => d.id == districtId);
                        if (selectedDistrict) {
                            $('#district_code').val(selectedDistrict.code);

                            // If we have a subdistrict ID, fetch subdistrict data
                            if (subDistrictId) {
                                // Fetch subdistricts for this district
                                const subDistrictResponse = await fetch(`/location/sub-districts/${selectedDistrict.code}`);
                                const subDistrictData = await subDistrictResponse.json();

                                // Make sure we have valid subdistrict data
                                let subDistricts = subDistrictData;
                                if (subDistrictData && subDistrictData.data && Array.isArray(subDistrictData.data)) {
                                    subDistricts = subDistrictData.data;
                                }

                                if (Array.isArray(subDistricts) && subDistricts.length > 0) {
                                    // Clear and populate subdistrict select
                                    const subDistrictSelect = document.getElementById('subdistrict_code');
                                    subDistrictSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';

                                    // Add subdistrict options
                                    subDistricts.forEach(subDistrict => {
                                        const option = document.createElement('option');
                                        option.value = subDistrict.code;
                                        option.textContent = subDistrict.name;
                                        option.setAttribute('data-id', subDistrict.id);
                                        subDistrictSelect.appendChild(option);
                                    });

                                    // Find and select the matching subdistrict
                                    const selectedSubDistrict = subDistricts.find(sd => sd.id == subDistrictId);
                                    if (selectedSubDistrict) {
                                        $('#subdistrict_code').val(selectedSubDistrict.code);

                                        // If we have a village ID, fetch village data
                                        if (villageId) {
                                            // Fetch villages for this subdistrict
                                            const villageResponse = await fetch(`/location/villages/${selectedSubDistrict.code}`);
                                            const villageData = await villageResponse.json();

                                            // Make sure we have valid village data
                                            let villages = villageData;
                                            if (villageData && villageData.data && Array.isArray(villageData.data)) {
                                                villages = villageData.data;
                                            }

                                            if (Array.isArray(villages) && villages.length > 0) {
                                                // Clear and populate village select
                                                const villageSelect = document.getElementById('village_code');
                                                villageSelect.innerHTML = '<option value="">Pilih Desa</option>';

                                                // Add village options
                                                villages.forEach(village => {
                                                    const option = document.createElement('option');
                                                    option.value = village.code;
                                                    option.textContent = village.name;
                                                    option.setAttribute('data-id', village.id);
                                                    villageSelect.appendChild(option);
                                                });

                                                // Find and select the matching village
                                                const selectedVillage = villages.find(v => v.id == villageId);
                                                if (selectedVillage) {
                                                    $('#village_code').val(selectedVillage.code);
                                                }

                                                // Enable the village select
                                                $('#village_code').prop('disabled', false);
                                            }
                                        }
                                    }

                                    // Enable the subdistrict select
                                    $('#subdistrict_code').prop('disabled', false);
                                }
                            }
                        }

                        // Enable the district select
                        $('#district_code').prop('disabled', false);
                    }
                }
            }
        }
    } catch (error) {
        console.error('Error populating location dropdowns:', error);
    }
}

// Setup location dropdown cascade events
function setupLocationDropdowns() {
    const provinceSelect = document.getElementById('province_code');
    const districtSelect = document.getElementById('district_code');
    const subDistrictSelect = document.getElementById('subdistrict_code');
    const villageSelect = document.getElementById('village_code');

    // Hidden inputs for IDs
    const provinceIdInput = document.getElementById('province_id');
    const districtIdInput = document.getElementById('district_id');
    const subDistrictIdInput = document.getElementById('subdistrict_id');
    const villageIdInput = document.getElementById('village_id');

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

    // Province change handler
    if (provinceSelect) {
        provinceSelect.addEventListener('change', function() {
            const provinceCode = this.value;

            // Update the hidden input with the ID
            updateHiddenInput(this, provinceIdInput);

            resetSelect(districtSelect, 'Loading...', districtIdInput);
            resetSelect(subDistrictSelect, 'Pilih Kecamatan', subDistrictIdInput);
            resetSelect(villageSelect, 'Pilih Desa', villageIdInput);

            if (provinceCode) {
                fetch(`/location/districts/${provinceCode}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            populateSelect(districtSelect, data, 'Pilih Kabupaten', districtIdInput);
                            districtSelect.disabled = false;
                        } else {
                            resetSelect(districtSelect, 'No data available', districtIdInput);
                        }
                    })
                    .catch(error => {
                        resetSelect(districtSelect, 'Error loading data', districtIdInput);
                    });
            }
        });
    }

    // District change handler
    if (districtSelect) {
        districtSelect.addEventListener('change', function() {
            const districtCode = this.value;
            // Update hidden input with ID
            updateHiddenInput(this, districtIdInput);

            resetSelect(subDistrictSelect, 'Loading...', subDistrictIdInput);
            resetSelect(villageSelect, 'Pilih Desa', villageIdInput);

            if (districtCode) {
                fetch(`/location/sub-districts/${districtCode}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            populateSelect(subDistrictSelect, data, 'Pilih Kecamatan', subDistrictIdInput);
                            subDistrictSelect.disabled = false;
                        } else {
                            resetSelect(subDistrictSelect, 'No data available', subDistrictIdInput);
                        }
                    })
                    .catch(error => {
                        resetSelect(subDistrictSelect, 'Error loading data', subDistrictIdInput);
                    });
            }
        });
    }

    // Sub-district change handler
    if (subDistrictSelect) {
        subDistrictSelect.addEventListener('change', function() {
            const subDistrictCode = this.value;
            // Update hidden input with ID
            updateHiddenInput(this, subDistrictIdInput);

            resetSelect(villageSelect, 'Loading...', villageIdInput);

            if (subDistrictCode) {
                fetch(`/location/villages/${subDistrictCode}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            populateSelect(villageSelect, data, 'Pilih Desa', villageIdInput);
                            villageSelect.disabled = false;
                        } else {
                            resetSelect(villageSelect, 'No data available', villageIdInput);
                        }
                    })
                    .catch(error => {
                        resetSelect(villageSelect, 'Error loading data', villageIdInput);
                    });
            }
        });
    }

    // Village change handler
    if (villageSelect) {
        villageSelect.addEventListener('change', function() {
            // Update hidden input with ID
            updateHiddenInput(this, villageIdInput);
        });
    }
}

// Initialize citizen data (NIK and Name) select fields with Select2
function initializeCitizenSelect(routeUrl, onDataLoaded = null, options = {}) {
    // Opsi default
    const filterByVillage = options.filterByVillage !== undefined ? options.filterByVillage : true;
    const useTextInput = options.useTextInput !== undefined ? options.useTextInput : false;

    let isUpdating = false;
    let allCitizens = [];

    // Siapkan data request
    let requestData = { limit: 10000 };
    if (filterByVillage) {
        // Ambil village_id dari input hidden jika ada
        const villageId = document.getElementById('village_id')?.value;
        if (villageId) requestData.village_id = villageId;
    }

    // AJAX request
    $.ajax({
        url: routeUrl,
        type: 'GET',
        dataType: 'json',
        data: requestData,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(data) {
            // Transform the response to match what we expect
            let processedData = data;
            if (data && data.data && Array.isArray(data.data)) {
                processedData = data.data;
            } else if (data && Array.isArray(data)) {
                processedData = data;
            }

            // Make sure we have valid data
            if (!Array.isArray(processedData)) {
                return;
            }

            allCitizens = processedData;

            // Jika pakai input text (superadmin)
            if (useTextInput) {
                setupTextInputAutofill(allCitizens);
            } else {
            setupSelect2WithData(allCitizens);
            }

            // If callback provided, call it with the loaded citizen data
            if (typeof onDataLoaded === 'function') {
                onDataLoaded(allCitizens);
            }
        },
        error: function(error) {
            // Initialize Select2 anyway with empty data
            setupSelect2WithData([]);
        }
    });

    function setupSelect2WithData(citizens) {
        // Create NIK options array
        const nikOptions = [];
        const nameOptions = [];

        // Process citizen data for Select2
        for (let i = 0; i < citizens.length; i++) {
            const citizen = citizens[i];

            // Handle cases where NIK might be coming from various fields
            let nikValue = null;

            if (typeof citizen.nik !== 'undefined' && citizen.nik !== null) {
                nikValue = citizen.nik;
            } else if (typeof citizen.id !== 'undefined' && citizen.id !== null && !isNaN(citizen.id)) {
                // If id is numeric (not a name), it might be the NIK
                nikValue = citizen.id;
            }

            if (nikValue !== null) {
                const nikString = nikValue.toString();
                nikOptions.push({
                    id: nikString,
                    text: nikString,
                    citizen: citizen,
                    address: citizen.address // Explicitly include address for data-address attribute
                });
            }

            // Only add if full_name is available
            if (citizen.full_name) {
                nameOptions.push({
                    id: citizen.full_name,
                    text: citizen.full_name,
                    citizen: citizen,
                    address: citizen.address // Explicitly include address for data-address attribute
                });
            }
        }

        // Initialize NIK Select2
        $('#nikSelect').select2({
            placeholder: 'Pilih NIK',
            width: '100%',
            data: nikOptions,
            language: {
                noResults: function() {
                    return 'Tidak ada data yang ditemukan';
                },
                searching: function() {
                    return 'Mencari...';
                }
            },
            escapeMarkup: function(markup) {
                return markup;
            },
            templateResult: function(data) {
                if (data.loading) return data.text;
                return '<div>' + data.text + '</div>';
            },
            templateSelection: function(data) {
                // This sets data-address on the rendered option
                if (data.address) {
                    setTimeout(() => {
                        $('.select2-selection').attr('data-address', data.address);
                    }, 0);
                }
                return data.text;
            }
        }).on("select2:open", function() {
            // This ensures all options are visible when dropdown opens
            $('.select2-results__options').css('max-height', '400px');
        });

        // Initialize Full Name Select2
        $('#fullNameSelect').select2({
            placeholder: 'Pilih Nama Lengkap',
            width: '100%',
            data: nameOptions,
            language: {
                noResults: function() {
                    return 'Tidak ada data yang ditemukan';
                },
                searching: function() {
                    return 'Mencari...';
                }
            },
            templateSelection: function(data) {
                // This sets data-address on the rendered option
                if (data.address) {
                    setTimeout(() => {
                        $('.select2-selection').attr('data-address', data.address);
                    }, 0);
                }
                return data.text;
            }
        }).on("select2:open", function() {
            // This ensures all options are visible when dropdown opens
            $('.select2-results__options').css('max-height', '400px');
        });

        // When NIK is selected, fill in other fields
        $('#nikSelect').on('select2:select', function (e) {
            if (isUpdating) return; // Prevent recursion
            isUpdating = true;

            // Get the selected citizen data
            const citizen = e.params.data.citizen;

            if (citizen) {
                // Set Full Name in dropdown
                $('#fullNameSelect').val(citizen.full_name).trigger('change.select2'); // Just update UI

                // Fill other form fields
                populateCitizenData(citizen);

                // Set domicile_address immediately from citizen data
                if (citizen.address) {
                    $('#domicile_address').val(citizen.address);
                }

                // Auto-populate location dropdowns based on citizen's location IDs
                populateLocationDropdowns(
                    citizen.province_id,
                    citizen.district_id,
                    citizen.subdistrict_id || citizen.sub_district_id,
                    citizen.village_id
                );
            }

            isUpdating = false;
        });

        // When Full Name is selected, fill in other fields
        $('#fullNameSelect').on('select2:select', function (e) {
            if (isUpdating) return; // Prevent recursion
            isUpdating = true;

            const citizen = e.params.data.citizen;

            if (citizen) {
                // Set NIK in dropdown without triggering the full change event
                const nikValue = citizen.nik ? citizen.nik.toString() : '';
                $('#nikSelect').val(nikValue).trigger('change.select2');  // Just update the UI

                // Fill other form fields
                populateCitizenData(citizen);

                // Set domicile_address immediately from citizen data
                if (citizen.address) {
                    $('#domicile_address').val(citizen.address);
                }

                // Auto-populate location dropdowns based on citizen's location IDs
                populateLocationDropdowns(
                    citizen.province_id,
                    citizen.district_id,
                    citizen.subdistrict_id || citizen.sub_district_id,
                    citizen.village_id
                );
            }

            isUpdating = false;
        });

        // Manually trigger domicile address update if a citizen is already selected
        setTimeout(() => {
            const nikSelect = document.querySelector('#nikSelect');
            if (nikSelect && nikSelect.value) {
                const selectedCitizen = allCitizens.find(c => c.nik == nikSelect.value || c.id == nikSelect.value);
                if (selectedCitizen && selectedCitizen.address) {
                    document.querySelector('#domicile_address').value = selectedCitizen.address;
                }
            }
        }, 500);
    }

    // Fungsi autofill untuk input text
    function setupTextInputAutofill(citizens) {
        // NIK
        const nikInput = document.getElementById('nik') || document.getElementById('nikSelect');
        if (nikInput) {
            nikInput.addEventListener('input', function() {
                const nikValue = this.value.trim();
                if (nikValue.length === 16 && /^\d+$/.test(nikValue)) {
                    const matched = citizens.find(c => c.nik == nikValue);
                    if (matched) populateCitizenData(matched);
                }
            });
        }
        // Nama
        const nameInput = document.getElementById('full_name') || document.getElementById('fullNameSelect');
        if (nameInput) {
            nameInput.addEventListener('input', function() {
                const nameValue = this.value.trim().toLowerCase();
                if (nameValue.length >= 3) {
                    const matched = citizens.find(c => c.full_name && c.full_name.toLowerCase() === nameValue);
                    if (matched) populateCitizenData(matched);
                }
            });
        }
        // RF ID
        const rfidInput = document.getElementById('rf_id_tag');
        if (rfidInput) {
            // Remove any existing event listeners by cloning the element
            const newRfidInput = rfidInput.cloneNode(true);
            rfidInput.parentNode.replaceChild(newRfidInput, rfidInput);

            // Add input event listener with debouncing
            let inputTimeout;
            newRfidInput.addEventListener('input', function() {
                const rfidValue = this.value.trim();

                // Clear previous timeout
                clearTimeout(inputTimeout);

                if (rfidValue.length > 0) {
                    // Set a timeout to process the RF ID after a short delay
                    inputTimeout = setTimeout(() => {
                        const matched = citizens.find(c => c.rf_id_tag && c.rf_id_tag.toString() === rfidValue);
                        if (matched) {
                            populateCitizenData(matched);
                            // Visual feedback
                            $(newRfidInput).addClass('border-green-500').removeClass('border-red-500 border-gray-300');
                            setTimeout(() => {
                                $(newRfidInput).removeClass('border-green-500').addClass('border-gray-300');
                            }, 2000);
                        }
                    }, 300); // 300ms delay to prevent immediate processing
                }
            });

            // Handle paste events with immediate processing
            newRfidInput.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                this.value = pastedText;

                // Process immediately after paste
                setTimeout(() => {
                    const rfidValue = this.value.trim();
                    if (rfidValue.length > 0) {
                        const matched = citizens.find(c => c.rf_id_tag && c.rf_id_tag.toString() === rfidValue);
                        if (matched) {
                            populateCitizenData(matched);
                        }
                    }
                }, 100);
            });
        }
    }

    // Fill citizen data into form fields
    function populateCitizenData(citizen) {
        // Set NIK field
        if (citizen.nik) {
            const nikValue = citizen.nik.toString();
            $('#nik').val(nikValue);
        }

        // Set full name field
        if (citizen.full_name) {
            $('#full_name').val(citizen.full_name);
        }

        // Fill other form fields - PERSONAL INFO
        $('#birth_place').val(citizen.birth_place || '');

        // Handle birth_date - reformatting if needed
        if (citizen.birth_date) {
            // Check if birth_date is in DD/MM/YYYY format and convert it
            if (citizen.birth_date.includes('/')) {
                const [day, month, year] = citizen.birth_date.split('/');
                $('#birth_date').val(`${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`);
            } else {
                $('#birth_date').val(citizen.birth_date);
            }
        } else {
            $('#birth_date').val('');
        }

        // Set address field
        $('#address').val(citizen.address || '');

        // Handle gender selection - convert string to numeric value
        let gender = citizen.gender;
        if (typeof gender === 'string') {
            // Convert string gender values to numeric
            if (gender.toLowerCase() === 'laki-laki') {
                gender = 1;
            } else if (gender.toLowerCase() === 'perempuan') {
                gender = 2;
            }
        }
        $('#gender').val(gender).trigger('change');

        // Handle religion selection - convert string to numeric value
        let religion = citizen.religion;
        if (typeof religion === 'string') {
            const religionMap = {
                'islam': 1,
                'kristen': 2,
                'katholik': 3,
                'hindu': 4,
                'buddha': 5,
                'kong hu cu': 6,
                'lainnya': 7
            };
            religion = religionMap[religion.toLowerCase()] || '';
        }
        $('#religion').val(religion).trigger('change');

        // Ensure job_type_id is numeric
        const jobTypeId = parseInt(citizen.job_type_id) || '';
        $('#job_type_id').val(jobTypeId).trigger('change');

        // Handle citizen status conversion properly
        let citizenStatus = citizen.citizen_status;
        if (typeof citizenStatus === 'string') {
            if (citizenStatus.toLowerCase() === 'wna') {
                citizenStatus = 1;
            } else if (citizenStatus.toLowerCase() === 'wni') {
                citizenStatus = 2;
            }
        }
        $('#citizen_status').val(citizenStatus).trigger('change');

        // Convert rt to text if it's a number
        const rt = citizen.rt ? citizen.rt.toString() : '';
        $('#rt').val(rt);

        // Set domicile_address if it exists
        if (citizen.address && document.querySelector('#domicile_address')) {
            document.querySelector('#domicile_address').value = citizen.address;
        }
    }
}

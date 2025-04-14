/**
 * Birth Certificate Form Functionality
 */

// Function to handle success and error messages using SweetAlert
function showSweetAlert(type, title, text) {
    Swal.fire({
        icon: type,
        title: title,
        text: text,
        timer: 3000,
        showConfirmButton: false
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Define isUpdating in the global scope so all handlers can access it
    let isUpdating = false;

    // Store the loaded citizens for reuse
    let allCitizens = [];

    // Get variables passed from PHP
    const citizenApiRoute = document.getElementById('birth-form-container').dataset.citizenRoute;
    const success = document.getElementById('birth-form-container').dataset.success;
    const error = document.getElementById('birth-form-container').dataset.error;

    // Show notifications if needed
    if (success) {
        showSweetAlert('success', 'Sukses!', success);
    }

    if (error) {
        showSweetAlert('error', 'Gagal!', error);
    }

    // Cascading region selection code for province, district, subdistrict, village
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

    // Helper function to populate select options
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

    // Village change handler
    villageSelect.addEventListener('change', function() {
        updateHiddenInput(this, villageIdInput);
    });

    // Form validation - modify to allow submission even with errors
    document.querySelector('form').addEventListener('submit', function(e) {
        // Check all required fields
        const requiredFields = document.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value) {
                isValid = false;
                field.classList.add('border-red-500');
            } else {
                field.classList.remove('border-red-500');
            }
        });

        // Check location data
        const provinceId = document.getElementById('province_id').value;
        const districtId = document.getElementById('district_id').value;
        const subDistrictId = document.getElementById('subdistrict_id').value;
        const villageId = document.getElementById('village_id').value;

        if (!isValid || !provinceId || !districtId || !subDistrictId || !villageId) {
            // Add alert but allow form to submit (for debugging)
            alert('Ada beberapa field yang belum terisi dengan benar. Form akan tetap dikirim untuk debugging.');
            // Log form data for debugging
            console.log('Form Data:', {
                provinceId,
                districtId,
                subDistrictId,
                villageId,
                'child_name': document.getElementById('child_name').value,
                'child_gender': document.getElementById('child_gender').value,
                'child_religion': document.getElementById('child_religion').value
            });
            // Do not return false to allow submission
        }
    });

    // Load all citizens first before initializing Select2
    $.ajax({
        url: citizenApiRoute,
        type: 'GET',
        dataType: 'json',
        data: {
            limit: 10000 // Increase limit to load more citizens at once
        },
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

            // Now initialize Select2 with the pre-loaded data
            initializeParentSelect2WithData();
        },
        error: function(error) {
            // Initialize Select2 anyway, but it will use AJAX for searching
            initializeParentSelect2WithData();
        }
    });

    function initializeParentSelect2WithData() {
        // Create NIK options arrays for father and mother
        const fatherNikOptions = [];
        const fatherNameOptions = [];
        const motherNikOptions = [];
        const motherNameOptions = [];

        // Process citizen data for Select2
        for (let i = 0; i < allCitizens.length; i++) {
            const citizen = allCitizens[i];

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
                fatherNikOptions.push({
                    id: nikString,
                    text: nikString,
                    citizen: citizen
                });
                motherNikOptions.push({
                    id: nikString,
                    text: nikString,
                    citizen: citizen
                });
            }

            // Only add if full_name is available
            if (citizen.full_name) {
                fatherNameOptions.push({
                    id: citizen.full_name,
                    text: citizen.full_name,
                    citizen: citizen
                });
                motherNameOptions.push({
                    id: citizen.full_name,
                    text: citizen.full_name,
                    citizen: citizen
                });
            }
        }

        // Initialize Father NIK Select2
        $('#father_nik').select2({
            placeholder: 'Pilih NIK',
            width: '100%',
            data: fatherNikOptions,
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
            }
        }).on("select2:open", function() {
            // This ensures all options are visible when dropdown opens
            $('.select2-results__options').css('max-height', '400px');
        });

        // Initialize Father Full Name Select2
        $('#father_full_name').select2({
            placeholder: 'Pilih Nama Lengkap',
            width: '100%',
            data: fatherNameOptions,
            language: {
                noResults: function() {
                    return 'Tidak ada data yang ditemukan';
                },
                searching: function() {
                    return 'Mencari...';
                }
            }
        }).on("select2:open", function() {
            // This ensures all options are visible when dropdown opens
            $('.select2-results__options').css('max-height', '400px');
        });

        // Initialize Mother NIK Select2
        $('#mother_nik').select2({
            placeholder: 'Pilih NIK',
            width: '100%',
            data: motherNikOptions,
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
            }
        }).on("select2:open", function() {
            // This ensures all options are visible when dropdown opens
            $('.select2-results__options').css('max-height', '400px');
        });

        // Initialize Mother Full Name Select2
        $('#mother_full_name').select2({
            placeholder: 'Pilih Nama Lengkap',
            width: '100%',
            data: motherNameOptions,
            language: {
                noResults: function() {
                    return 'Tidak ada data yang ditemukan';
                },
                searching: function() {
                    return 'Mencari...';
                }
            }
        }).on("select2:open", function() {
            // This ensures all options are visible when dropdown opens
            $('.select2-results__options').css('max-height', '400px');
        });

        // When Father NIK is selected, fill in other father fields
        $('#father_nik').on('select2:select', function (e) {
            if (isUpdating) return; // Prevent recursion
            isUpdating = true;

            // Get the selected citizen data
            const citizen = e.params.data.citizen;

            if (citizen) {
                // Set Full Name in dropdown
                $('#father_full_name').val(citizen.full_name).trigger('change.select2'); // Just update UI, not trigger full change

                populateParentFields(citizen, 'father');

                // Always populate child address from father's address if available
                if (citizen.address) {
                    $('#child_address').val(citizen.address);
                }

                // Auto-fill location fields with father's location data
                if (citizen.province_id && citizen.district_id && (citizen.subdistrict_id || citizen.sub_district_id) && citizen.village_id) {
                    populateLocationFromCitizen(citizen, 'ayah');
                }
            }

            isUpdating = false;
        });

        // When Father Full Name is selected, fill in other father fields
        $('#father_full_name').on('select2:select', function (e) {
            if (isUpdating) return; // Prevent recursion
            isUpdating = true;

            const citizen = e.params.data.citizen;

            if (citizen) {
                // Set NIK in dropdown without triggering the full change event
                const nikValue = citizen.nik ? citizen.nik.toString() : '';
                $('#father_nik').val(nikValue).trigger('change.select2');  // Just update the UI

                populateParentFields(citizen, 'father');

                // Always populate child address from father's address if available
                if (citizen.address) {
                    $('#child_address').val(citizen.address);
                }

                // Auto-fill location fields with father's location data
                if (citizen.province_id && citizen.district_id && (citizen.subdistrict_id || citizen.sub_district_id) && citizen.village_id) {
                    populateLocationFromCitizen(citizen, 'ayah');
                }
            }

            isUpdating = false;
        });

        // When Mother NIK is selected, fill in other mother fields
        $('#mother_nik').on('select2:select', function (e) {
            if (isUpdating) return; // Prevent recursion
            isUpdating = true;

            // Get the selected citizen data
            const citizen = e.params.data.citizen;

            if (citizen) {
                // Set Full Name in dropdown
                $('#mother_full_name').val(citizen.full_name).trigger('change.select2'); // Just update UI, not trigger full change

                populateParentFields(citizen, 'mother');

                // Always populate child address from mother's address if available
                if (citizen.address) {
                    $('#child_address').val(citizen.address);
                }

                // Auto-fill location fields with mother's location data
                if (citizen.province_id && citizen.district_id && (citizen.subdistrict_id || citizen.sub_district_id) && citizen.village_id) {
                    populateLocationFromCitizen(citizen, 'ibu');
                }
            }

            isUpdating = false;
        });

        // When Mother Full Name is selected, fill in other mother fields
        $('#mother_full_name').on('select2:select', function (e) {
            if (isUpdating) return; // Prevent recursion
            isUpdating = true;

            const citizen = e.params.data.citizen;

            if (citizen) {
                // Set NIK in dropdown without triggering the full change event
                const nikValue = citizen.nik ? citizen.nik.toString() : '';
                $('#mother_nik').val(nikValue).trigger('change.select2');  // Just update the UI

                populateParentFields(citizen, 'mother');

                // Always populate child address from mother's address if available
                if (citizen.address) {
                    $('#child_address').val(citizen.address);
                }

                // Auto-fill location fields with mother's location data
                if (citizen.province_id && citizen.district_id && (citizen.subdistrict_id || citizen.sub_district_id) && citizen.village_id) {
                    populateLocationFromCitizen(citizen, 'ibu');
                }
            }

            isUpdating = false;
        });
    }
});

// Function to populate parent fields with citizen data
function populateParentFields(citizen, parentType) {
    // Set fields based on parent type (father or mother)
    document.getElementById(`${parentType}_birth_place`).value = citizen.birth_place || '';

    // Handle birth_date - reformatting if needed
    if (citizen.birth_date) {
        // Check if birth_date is in DD/MM/YYYY format and convert it
        if (citizen.birth_date.includes('/')) {
            const [day, month, year] = citizen.birth_date.split('/');
            document.getElementById(`${parentType}_birth_date`).value = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
        } else {
            document.getElementById(`${parentType}_birth_date`).value = citizen.birth_date;
        }
    } else {
        document.getElementById(`${parentType}_birth_date`).value = '';
    }

    // Set address field
    document.getElementById(`${parentType}_address`).value = citizen.address || '';

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
    document.getElementById(`${parentType}_religion`).value = religion;

    // Set job type ID if available
    if (citizen.job_type_id) {
        document.getElementById(`${parentType}_job`).value = citizen.job_type_id;
    }
}

// Function to populate location fields from citizen data
function populateLocationFromCitizen(citizen, parentType) {
    // Support both naming conventions for subdistrict
    const subDistrictId = citizen.subdistrict_id || citizen.sub_district_id;

    // Only attempt to populate if we have valid location data
    if (!citizen.province_id || !citizen.district_id || !subDistrictId || !citizen.village_id) {
        return;
    }

    // Set hidden ID fields directly without confirmation
    $('#province_id').val(citizen.province_id);
    $('#district_id').val(citizen.district_id);
    $('#subdistrict_id').val(subDistrictId);
    $('#village_id').val(citizen.village_id);

    // Find and select the correct province option
    const provinceSelect = document.getElementById('province_code');
    let provinceFound = false;

    for (let i = 0; i < provinceSelect.options.length; i++) {
        const option = provinceSelect.options[i];
        if (option.getAttribute('data-id') == citizen.province_id) {
            provinceSelect.value = option.value;
            provinceFound = true;

            // Now load districts with improved error handling
            fetch(`/location/districts/${option.value}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(districts => {
                    if (!districts || !Array.isArray(districts) || districts.length === 0) {
                        return;
                    }

                    // Populate district dropdown
                    const districtSelect = document.getElementById('district_code');
                    districtSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';

                    let districtFound = false;
                    let selectedDistrictCode = null;

                    districts.forEach(district => {
                        const districtOption = document.createElement('option');
                        districtOption.value = district.code;
                        districtOption.textContent = district.name;
                        districtOption.setAttribute('data-id', district.id);

                        if (district.id == citizen.district_id) {
                            districtOption.selected = true;
                            selectedDistrictCode = district.code;
                            districtFound = true;
                        }

                        districtSelect.appendChild(districtOption);
                    });

                    districtSelect.disabled = false;

                    if (districtFound && selectedDistrictCode) {
                        // Load the subdistricts and villages
                        loadSubdistrictsAndVillages(selectedDistrictCode, subDistrictId, citizen.village_id);
                    }
                })
                .catch(error => {
                    const districtSelect = document.getElementById('district_code');
                    districtSelect.innerHTML = '<option value="">Error loading data</option>';
                });

            break;
        }
    }
}

// Helper function to load subdistricts and villages
function loadSubdistrictsAndVillages(districtCode, subDistrictId, villageId) {
    fetch(`/location/sub-districts/${districtCode}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(subdistricts => {
            if (!subdistricts || !Array.isArray(subdistricts) || subdistricts.length === 0) {
                return;
            }

            // Populate subdistrict dropdown
            const subdistrictSelect = document.getElementById('subdistrict_code');
            subdistrictSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            subdistricts.forEach(subdistrict => {
                const subdistrictOption = document.createElement('option');
                subdistrictOption.value = subdistrict.code;
                subdistrictOption.textContent = subdistrict.name;
                subdistrictOption.setAttribute('data-id', subdistrict.id);
                if (subdistrict.id == subDistrictId) {
                    subdistrictOption.selected = true;
                }
                subdistrictSelect.appendChild(subdistrictOption);
            });
            subdistrictSelect.disabled = false;

            // Ensure we update the hidden input for subdistrict
            if (subdistrictSelect.selectedIndex > 0) {
                const selectedOption = subdistrictSelect.options[subdistrictSelect.selectedIndex];
                $('#subdistrict_id').val(selectedOption.getAttribute('data-id'));

                // Find the selected subdistrict code to load villages
                const selectedSubdistrictCode = selectedOption.value;

                if (selectedSubdistrictCode) {
                    loadVillages(selectedSubdistrictCode, villageId);
                }
            } else {
                // Try to find matching subdistrict
                for (let i = 0; i < subdistrictSelect.options.length; i++) {
                    if (subdistrictSelect.options[i].getAttribute('data-id') == subDistrictId) {
                        subdistrictSelect.selectedIndex = i;
                        $('#subdistrict_id').val(subDistrictId);
                        loadVillages(subdistrictSelect.options[i].value, villageId);
                        break;
                    }
                }
            }
        })
        .catch(error => {
            const subdistrictSelect = document.getElementById('subdistrict_code');
            subdistrictSelect.innerHTML = '<option value="">Error loading data</option>';
        });
}

// Separate function to load villages for better organization
function loadVillages(subdistrictCode, villageId) {
    fetch(`/location/villages/${subdistrictCode}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(villages => {
            if (!villages || !Array.isArray(villages) || villages.length === 0) {
                return;
            }

            // Populate village dropdown
            const villageSelect = document.getElementById('village_code');
            villageSelect.innerHTML = '<option value="">Pilih Desa</option>';
            villages.forEach(village => {
                const villageOption = document.createElement('option');
                villageOption.value = village.code;
                villageOption.textContent = village.name;
                villageOption.setAttribute('data-id', village.id);
                if (village.id == villageId) {
                    villageOption.selected = true;
                }
                villageSelect.appendChild(villageOption);
            });
            villageSelect.disabled = false;

            // Ensure we update the hidden input for village
            if (villageSelect.selectedIndex > 0) {
                const selectedOption = villageSelect.options[villageSelect.selectedIndex];
                $('#village_id').val(selectedOption.getAttribute('data-id'));
            }
        })
        .catch(error => {
            const villageSelect = document.getElementById('village_code');
            villageSelect.innerHTML = '<option value="">Error loading data</option>';
        });
}

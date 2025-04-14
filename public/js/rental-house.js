/**
 * Rental House Form Functionality
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
    // Store the loaded citizens for reuse
    let allCitizens = [];

    // Get variables from data attributes
    const formContainer = document.getElementById('rental-house-form-container');
    const citizenApiRoute = formContainer.dataset.citizenRoute;
    const success = formContainer.dataset.success;
    const error = formContainer.dataset.error;

    // Show notifications if needed
    if (success) {
        showSweetAlert('success', 'Sukses!', success);
    }

    if (error) {
        showSweetAlert('error', 'Gagal!', error);
    }

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
                console.error('Invalid citizen data format');
                return;
            }

            allCitizens = processedData;

            // Setup the interfaces
            setupLocationHandlers();
            setupOrganizerFields();
            setupResponsibleNameField();
        },
        error: function(error) {
            console.error('Failed to load citizen data:', error);
            // Setup the interfaces anyway with empty data
            setupLocationHandlers();
            setupOrganizerFields();
            setupResponsibleNameField();
        }
    });

    function setupLocationHandlers() {
        const provinceSelect = document.getElementById('province_code');
        const districtSelect = document.getElementById('district_code');
        const subDistrictSelect = document.getElementById('subdistrict_code');
        const villageSelect = document.getElementById('village_code');

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
    }

    // Setup organizer fields (NIK, name, address) as a connected unit
    function setupOrganizerFields() {
        // Process citizens for Select2
        function prepareCitizenOptions() {
            const nikOptions = [];
            const nameOptions = [];

            allCitizens.forEach(citizen => {
                // Handle cases where NIK might be coming from various fields
                let nikValue = null;

                if (typeof citizen.nik !== 'undefined' && citizen.nik !== null) {
                    nikValue = citizen.nik;
                } else if (typeof citizen.id !== 'undefined' && citizen.id !== null && !isNaN(citizen.id)) {
                    nikValue = citizen.id;
                }

                if (nikValue !== null) {
                    const nikString = nikValue.toString();
                    nikOptions.push({
                        id: nikString,
                        text: nikString,
                        citizen: citizen
                    });
                }

                // Only add if full_name is available
                if (citizen.full_name) {
                    nameOptions.push({
                        id: citizen.full_name,
                        text: citizen.full_name,
                        citizen: citizen
                    });
                }
            });

            return { nikOptions, nameOptions };
        }

        const { nikOptions, nameOptions } = prepareCitizenOptions();
        const nikSelect = document.getElementById('nikSelect');
        const nameSelect = document.getElementById('fullNameSelect');

        // Track if we're in the middle of an update to prevent recursion
        let isUpdating = false;

        // Initialize NIK Select2 with pre-loaded data
        $(nikSelect).select2({
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
            }
        }).on("select2:open", function() {
            $('.select2-results__options').css('max-height', '400px');
        });

        // Initialize Name Select2 with pre-loaded data
        $(nameSelect).select2({
            placeholder: 'Pilih Nama',
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
            escapeMarkup: function(markup) {
                return markup;
            }
        }).on("select2:open", function() {
            $('.select2-results__options').css('max-height', '400px');
        });

        // NIK select change handler
        $(nikSelect).on('select2:select', function(e) {
            if (isUpdating) return;
            isUpdating = true;

            const citizen = e.params.data.citizen;
            if (citizen) {
                // Update full name
                $(nameSelect).val(citizen.full_name).trigger('change.select2');

                // Fill address field
                $('#address').val(citizen.address || '');

                // Also populate location fields from citizen data
                populateLocationFields(citizen);
            }

            isUpdating = false;
        });

        // Full name select change handler
        $(nameSelect).on('select2:select', function(e) {
            if (isUpdating) return;
            isUpdating = true;

            const citizen = e.params.data.citizen;
            if (citizen) {
                // Update NIK
                if (citizen.nik) {
                    $(nikSelect).val(citizen.nik.toString()).trigger('change.select2');
                }

                // Fill address field
                $('#address').val(citizen.address || '');

                // Also populate location fields from citizen data
                populateLocationFields(citizen);
            }

            isUpdating = false;
        });
    }

    // New function to populate location fields from citizen data
    function populateLocationFields(citizen) {
        // Support both naming conventions for subdistrict
        const subDistrictId = citizen.subdistrict_id || citizen.sub_district_id;

        // Only attempt to populate if we have valid location data
        if (!citizen.province_id || !citizen.district_id || !subDistrictId || !citizen.village_id) {
            console.log('Incomplete location data for citizen');
            return;
        }

        // Set hidden ID fields directly
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

                // Now load districts
                fetch(`/location/districts/${option.value}`)
                    .then(response => response.json())
                    .then(districts => {
                        if (!districts || !Array.isArray(districts) || districts.length === 0) return;

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
                            // Load subdistricts
                            fetch(`/location/sub-districts/${selectedDistrictCode}`)
                                .then(response => response.json())
                                .then(subdistricts => {
                                    if (!subdistricts || !Array.isArray(subdistricts) || subdistricts.length === 0) return;

                                    // Populate subdistrict dropdown
                                    const subdistrictSelect = document.getElementById('subdistrict_code');
                                    subdistrictSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';

                                    let subdistrictFound = false;
                                    let selectedSubdistrictCode = null;

                                    subdistricts.forEach(subdistrict => {
                                        const subdistrictOption = document.createElement('option');
                                        subdistrictOption.value = subdistrict.code;
                                        subdistrictOption.textContent = subdistrict.name;
                                        subdistrictOption.setAttribute('data-id', subdistrict.id);

                                        if (subdistrict.id == subDistrictId) {
                                            subdistrictOption.selected = true;
                                            selectedSubdistrictCode = subdistrict.code;
                                            subdistrictFound = true;
                                        }

                                        subdistrictSelect.appendChild(subdistrictOption);
                                    });

                                    subdistrictSelect.disabled = false;

                                    if (subdistrictFound && selectedSubdistrictCode) {
                                        // Load villages
                                        fetch(`/location/villages/${selectedSubdistrictCode}`)
                                            .then(response => response.json())
                                            .then(villages => {
                                                if (!villages || !Array.isArray(villages) || villages.length === 0) return;

                                                // Populate village dropdown
                                                const villageSelect = document.getElementById('village_code');
                                                villageSelect.innerHTML = '<option value="">Pilih Desa</option>';

                                                villages.forEach(village => {
                                                    const villageOption = document.createElement('option');
                                                    villageOption.value = village.code;
                                                    villageOption.textContent = village.name;
                                                    villageOption.setAttribute('data-id', village.id);

                                                    if (village.id == citizen.village_id) {
                                                        villageOption.selected = true;
                                                    }

                                                    villageSelect.appendChild(villageOption);
                                                });

                                                villageSelect.disabled = false;
                                            })
                                            .catch(error => {
                                                console.error('Error loading villages:', error);
                                            });
                                    }
                                })
                                .catch(error => {
                                    console.error('Error loading subdistricts:', error);
                                });
                        }
                    })
                    .catch(error => {
                        console.error('Error loading districts:', error);
                    });

                break;
            }
        }

        if (!provinceFound) {
            console.log('Matching province not found in dropdown');
        }
    }

    // Setup responsible name field as an independent selection
    function setupResponsibleNameField() {
        // Process citizens for Select2 - only names
        function prepareNameOptions() {
            const nameOptions = [];

            allCitizens.forEach(citizen => {
                // Only add if full_name is available
                if (citizen.full_name) {
                    nameOptions.push({
                        id: citizen.full_name,
                        text: citizen.full_name
                    });
                }
            });

            return nameOptions;
        }

        const nameOptions = prepareNameOptions();
        const responsibleSelect = document.getElementById('responsibleNameSelect');

        // Initialize Name Select2 with pre-loaded data
        $(responsibleSelect).select2({
            placeholder: 'Pilih Nama Penanggung Jawab',
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
            escapeMarkup: function(markup) {
                return markup;
            }
        }).on("select2:open", function() {
            $('.select2-results__options').css('max-height', '400px');
        });
    }

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const provinceId = document.getElementById('province_id').value;
        const districtId = document.getElementById('district_id').value;
        const subDistrictId = document.getElementById('subdistrict_id').value;
        const villageId = document.getElementById('village_id');

        if (!provinceId || !districtId || !subDistrictId || !villageId) {
            e.preventDefault();
            alert('Silakan pilih Provinsi, Kabupaten, Kecamatan, dan Desa');
            return false;
        }
    });
});

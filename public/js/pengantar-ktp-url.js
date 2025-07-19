document.addEventListener('DOMContentLoaded', function() {
    // Define isUpdating in the global scope so all handlers can access it
    let isUpdating = false;

    // Store the loaded citizens for reuse
    let allCitizens = [];

    // Create mapping objects to convert between IDs and codes
    let provinceCodeMap = {};
    let districtCodeMap = {};
    let subDistrictCodeMap = {};
    let villageCodeMap = {};

    // Reverse maps to get ID from code
    let provinceIdMap = {};
    let districtIdMap = {};
    let subDistrictIdMap = {};
    let villageIdMap = {};

    // Initialize only the name select with Select2
    $('#fullNameSelect').select2({
        placeholder: 'Pilih Nama Lengkap',
        width: '100%',
        language: {
            noResults: function() {
                return 'Tidak ada data yang ditemukan';
            },
            searching: function() {
                return 'Mencari...';
            }
        }
    });

    // Function to load province codes and store the ID-to-code mapping
    async function loadProvinceCodeMap() {
        try {
            const response = await $.ajax({
                url: `${BASE_URL}/location/provinces`,
                type: 'GET'
            });

            let provinces = [];
            if (response && Array.isArray(response)) {
                provinces = response;
            } else if (response && response.data && Array.isArray(response.data)) {
                provinces = response.data;
            }

            if (provinces.length > 0) {
                // Create mappings in both directions
                provinces.forEach(province => {
                    provinceCodeMap[province.id] = province.code;
                    provinceIdMap[province.code] = province.id;
                });
            }
        } catch (error) {
            console.error('Error loading province maps:', error);
        }
    }

    // Function to get district code map for a specific province
    async function loadDistrictCodeMap(provinceCode) {
        try {
            const response = await $.ajax({
                url: `${BASE_URL}/location/districts/${provinceCode}`,
                type: 'GET'
            });

            let districts = [];
            if (response && Array.isArray(response)) {
                districts = response;
            } else if (response && response.data && Array.isArray(response.data)) {
                districts = response.data;
            }

            // Reset the maps before adding new data
            districtCodeMap = {};
            districtIdMap = {};

            if (districts.length > 0) {
                // Create mappings in both directions
                districts.forEach(district => {
                    districtCodeMap[district.id] = district.code;
                    districtIdMap[district.code] = district.id;
                });
            }
            return districts;
        } catch (error) {
            console.error('Error loading district maps:', error);
            return [];
        }
    }

    // Function to get subdistrict code map for a specific district
    async function loadSubDistrictCodeMap(districtCode) {
        try {
            const response = await $.ajax({
                url: `${BASE_URL}/location/sub-districts/${districtCode}`,
                type: 'GET'
            });

            let subDistricts = [];
            if (response && Array.isArray(response)) {
                subDistricts = response;
            } else if (response && response.data && Array.isArray(response.data)) {
                subDistricts = response.data;
            }

            // Reset the maps before adding new data
            subDistrictCodeMap = {};
            subDistrictIdMap = {};

            if (subDistricts.length > 0) {
                // Create mappings in both directions
                subDistricts.forEach(subDistrict => {
                    subDistrictCodeMap[subDistrict.id] = subDistrict.code;
                    subDistrictIdMap[subDistrict.code] = subDistrict.id;
                });
            }
            return subDistricts;
        } catch (error) {
            console.error('Error loading subdistrict maps:', error);
            return [];
        }
    }

    // Function to get village code map for a specific subdistrict
    async function loadVillageCodeMap(subDistrictCode) {
        try {
            const response = await $.ajax({
                url: `${BASE_URL}/location/villages/${subDistrictCode}`,
                type: 'GET'
            });

            let villages = [];
            if (response && Array.isArray(response)) {
                villages = response;
            } else if (response && response.data && Array.isArray(response.data)) {
                villages = response.data;
            }

            // Reset the maps before adding new data
            villageCodeMap = {};
            villageIdMap = {};

            if (villages.length > 0) {
                // Create mappings in both directions
                villages.forEach(village => {
                    villageCodeMap[village.id] = village.code;
                    villageIdMap[village.code] = village.id;
                });
            }
            return villages;
        } catch (error) {
            console.error('Error loading village maps:', error);
            return [];
        }
    }

    // Load citizens data from the administrasi route
    async function fetchCitizens() {
        try {
            const response = await $.ajax({
                url: CITIZENS_URL,
                type: 'GET',
                dataType: 'json',
                data: {
                    limit: 10000 // Increase limit to load more citizens at once
                },
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            // Transform the response to match what we expect
            let citizensList = [];
            if (response && response.data && Array.isArray(response.data)) {
                citizensList = response.data;
            } else if (response && Array.isArray(response)) {
                citizensList = response;
            }

            allCitizens = citizensList;

            // Setup NIK input
            setupNikInput(citizensList);

            // Setup name select with data
            setupNameSelect(citizensList);

            // Add RF ID Tag event listener
            setupRfIdTagListener(citizensList);
        } catch (error) {
            console.error('Error fetching citizens data:', error);
        }
    }

    function setupNikInput(citizens) {
        const nikInput = document.getElementById('nikSelect');
        if (!nikInput) return;

        // Add input event listener
        nikInput.addEventListener('input', function() {
            const nikValue = this.value.trim();

            // Only process if NIK is exactly 16 digits
            if (nikValue.length === 16 && /^\d+$/.test(nikValue)) {
                // Find citizen with matching NIK
                const matchedCitizen = citizens.find(citizen => {
                    const citizenNik = citizen.nik ? citizen.nik.toString() : '';
                    return citizenNik === nikValue;
                });

                if (matchedCitizen) {
                    // Fill form with citizen data
                    populateCitizenData(matchedCitizen);

                    // Update full name select if it exists
                    if ($('#fullNameSelect').length) {
                        $('#fullNameSelect').val(matchedCitizen.full_name).trigger('change');
                    }

                    // Visual feedback for success
                    $(nikInput).addClass('border-green-500').removeClass('border-red-500 border-gray-300');
                    setTimeout(() => {
                        $(nikInput).removeClass('border-green-500').addClass('border-gray-300');
                    }, 2000);
                } else {
                    // Show error alert for NIK not found
                    Swal.fire({
                        title: 'Data Tidak Ditemukan',
                        text: 'NIK yang dimasukkan tidak terdaftar dalam sistem',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });

                    // Visual feedback for error
                    $(nikInput).addClass('border-red-500').removeClass('border-green-500 border-gray-300');
                    setTimeout(() => {
                        $(nikInput).removeClass('border-red-500').addClass('border-gray-300');
                    }, 2000);
                }
            }
        });
    }

    function setupNameSelect(citizens) {
        const nameSelect = document.getElementById('fullNameSelect');
        if (!nameSelect) return;

        // Create name options array
        const nameOptions = [];

        // Process citizen data for Select2
        citizens.forEach(citizen => {
            if (citizen.full_name) {
                nameOptions.push({
                    id: citizen.full_name,
                    text: citizen.full_name,
                    citizen: citizen
                });
            }
        });

        // Initialize Full Name Select2 dengan minimum input length
        $('#fullNameSelect').select2({
            placeholder: 'Ketik nama untuk mencari...',
            width: '100%',
            data: nameOptions,
            minimumInputLength: 3, // Minimal 3 karakter sebelum dropdown muncul
            language: {
                noResults: function() {
                    return 'Tidak ada data yang ditemukan';
                },
                searching: function() {
                    return 'Mencari...';
                },
                inputTooShort: function() {
                    return 'Ketik minimal 3 karakter untuk mencari';
                }
            },
            // Tambahkan delay untuk mengurangi request berlebihan
            delay: 300,
            // Fungsi untuk filter data berdasarkan input
            matcher: function(params, data) {
                // Jika tidak ada input, jangan tampilkan hasil
                if (!params.term) {
                    return null;
                }

                // Jika input kurang dari 3 karakter, jangan tampilkan hasil
                if (params.term.length < 3) {
                    return null;
                }

                // Cari berdasarkan nama yang mengandung input
                const term = params.term.toLowerCase();
                const text = data.text.toLowerCase();

                if (text.indexOf(term) > -1) {
                    return data;
                }

                return null;
            }
        }).on("select2:open", function() {
            // This ensures all options are visible when dropdown opens
            $('.select2-results__options').css('max-height', '400px');
        });

        // When Full Name is selected, fill in other fields
        $('#fullNameSelect').on('select2:select', function (e) {
            if (isUpdating) return; // Prevent recursion
            isUpdating = true;

            const citizen = e.params.data.citizen;

            if (citizen) {
                // Set NIK in input
                const nikValue = citizen.nik ? citizen.nik.toString() : '';
                $('#nikSelect').val(nikValue);

                // Fill other form fields
                populateCitizenData(citizen);
            }

            isUpdating = false;
        });
    }

    // Load citizens data when the page loads
    fetchCitizens();

    // Function to populate NIK and name dropdowns with data
    function populateCitizensDropdowns(citizens) {
        if (!citizens || !Array.isArray(citizens)) return;

        // Clear existing options first
        $('#nikSelect').empty().append('<option value="">Pilih NIK</option>');
        $('#fullNameSelect').empty().append('<option value="">Pilih Nama Lengkap</option>');

        // Create NIK options array and Name options array
        const nikOptions = [];
        const nameOptions = [];

        // Process citizen data for Select2
        citizens.forEach(citizen => {
            // For NIK dropdown
            if (citizen.nik) {
                const nikString = citizen.nik.toString();
                nikOptions.push({
                    id: nikString,
                    text: nikString,
                    citizen: citizen
                });
            }

            // For Full Name dropdown
            if (citizen.full_name) {
                nameOptions.push({
                    id: citizen.full_name,
                    text: citizen.full_name,
                    citizen: citizen
                });
            }
        });

        // Initialize NIK Select2 with data
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
            }
        });

        // Initialize Full Name Select2 with data
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
            }
        });

        // Add RF ID Tag event listener
        setupRfIdTagListener(citizens);
    }

    // Function to populate location dropdowns using ID or code
    async function populateLocationDropdowns(provinceId, districtId, subDistrictId, villageId) {
        try {
            // Update the address section selectors hidden fields
            $('#sub_district_id_hidden').val(subDistrictId || '');
            $('#village_id_hidden').val(villageId || '');

            // If we have province ID but not the code, we need to load it
            if (provinceId && !provinceCodeMap[provinceId]) {
                await loadProvinceCodeMap();
            }

            // Get the province code
            const provinceCode = provinceCodeMap[provinceId];
            if (!provinceCode) {
                return;
            }

            // Now load district data for this province without changing the top dropdown
            const districts = await loadDistrictCodeMap(provinceCode);

            // Get the district code
            const districtCode = districtCodeMap[districtId];
            if (!districtCode) {
                return;
            }

            // Now load subdistrict data for this district
            const subdistricts = await loadSubDistrictCodeMap(districtCode);

            // Get the subdistrict code
            const subDistrictCode = subDistrictCodeMap[subDistrictId];
            if (!subDistrictCode) {
                return;
            }

            // Now load village data for this subdistrict
            const villages = await loadVillageCodeMap(subDistrictCode);

            // Get the village code
            const villageCode = villageCodeMap[villageId];
            if (!villageCode) {
                return;
            }

            // Update only the address info section fields

            // Update subdistrict in address section
            const selectedSubdistrict = subdistricts.find(sd => sd.id == subDistrictId);
            if (selectedSubdistrict) {
                // Update the sub_district_selector dropdown with available options
                $('#sub_district_selector').html('<option value="">Pilih Kecamatan</option>');
                subdistricts.forEach(subdistrict => {
                    const option = $('<option></option>')
                        .val(subdistrict.code)
                        .text(subdistrict.name)
                        .attr('data-id', subdistrict.id);

                    if (subdistrict.id == subDistrictId) {
                        option.prop('selected', true);
                    }

                    $('#sub_district_selector').append(option);
                });
            }

            // Update village in address section
            const selectedVillage = villages.find(v => v.id == villageId);
            if (selectedVillage) {
                // Update the village_selector dropdown with available options
                $('#village_selector').html('<option value="">Pilih Desa</option>');
                villages.forEach(village => {
                    const option = $('<option></option>')
                        .val(village.code)
                        .text(village.name)
                        .attr('data-id', village.id);

                    if (village.id == villageId) {
                        option.prop('selected', true);
                    }

                    $('#village_selector').append(option);
                });
            }

        } catch (error) {
            console.error('Error populating location dropdowns:', error);
        }
    }

    // Handle NIK select change - Update all related fields including KK, address, etc.
    $('#nikSelect').on('select2:select', async function (e) {
        if (isUpdating) return; // Prevent recursion
        isUpdating = true;

        try {
            const selectedData = e.params.data;
            if (selectedData && selectedData.citizen) {
                const citizen = selectedData.citizen;

                // Update name dropdown
                $('#fullNameSelect').val(citizen.full_name).trigger('change.select2');

                // Fill in KK, address, and other fields
                $('#kk').val(citizen.kk || '');
                $('#address').val(citizen.address || '');
                $('#rt').val(citizen.rt || '');
                $('#rw').val(citizen.rw || '');

                // Automatically populate hamlet based on address, RT, and RW
                const address = citizen.address || '';
                const rt = citizen.rt || '';
                const rw = citizen.rw || '';
                const hamlet = `${address}, RT ${rt}, RW ${rw}`;
                $('#hamlet').val(hamlet);

                // Set location IDs from citizen data
                const provinceId = citizen.province_id;
                const districtId = citizen.district_id;
                const subDistrictId = citizen.subdistrict_id || citizen.sub_district_id;
                const villageId = citizen.village_id;

                // Update the main location fields (top section)
                if (provinceId) {
                    // Set the province_id hidden input
                    $('#province_id').val(provinceId);

                    // Find and select the correct province in the dropdown
                    let provinceFound = false;
                    const provinceSelect = document.getElementById('province_code');
                    for (let i = 0; i < provinceSelect.options.length; i++) {
                        const option = provinceSelect.options[i];
                        if (option.getAttribute('data-id') == provinceId) {
                            provinceSelect.value = option.value;
                            provinceFound = true;

                            // Trigger loading of districts for this province
                            const provinceCode = option.value;

                            // Show loading state
                            const districtSelect = document.getElementById('district_code');
                            districtSelect.innerHTML = '<option value="">Loading...</option>';
                            districtSelect.disabled = false;

                            // Load districts
                            const districts = await fetch(`${BASE_URL}/location/districts/${provinceCode}`)
                                .then(response => response.json())
                                .catch(error => {
                                    console.error('Error loading districts:', error);
                                    return [];
                                });

                            // Populate district dropdown
                            districtSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';
                            let districtFound = false;

                            if (districts && districts.length > 0) {
                                districts.forEach(district => {
                                    const districtOption = document.createElement('option');
                                    districtOption.value = district.code;
                                    districtOption.textContent = district.name;
                                    districtOption.setAttribute('data-id', district.id);
                                    districtSelect.appendChild(districtOption);

                                    // Select this option if it matches the citizen's district
                                    if (district.id == districtId) {
                                        districtOption.selected = true;
                                        districtFound = true;
                                        $('#district_id').val(districtId);

                                        // Also load subdistricts for this district
                                        const districtCode = district.code;
                                        const subDistrictSelect = document.getElementById('subdistrict_code');
                                        subDistrictSelect.innerHTML = '<option value="">Loading...</option>';
                                        subDistrictSelect.disabled = false;

                                        fetch(`${BASE_URL}/location/sub-districts/${districtCode}`)
                                            .then(response => response.json())
                                            .then(subdistricts => {
                                                subDistrictSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                                                let subDistrictFound = false;

                                                if (subdistricts && subdistricts.length > 0) {
                                                    subdistricts.forEach(subdistrict => {
                                                        const subDistrictOption = document.createElement('option');
                                                        subDistrictOption.value = subdistrict.code;
                                                        subDistrictOption.textContent = subdistrict.name;
                                                        subDistrictOption.setAttribute('data-id', subdistrict.id);
                                                        subDistrictSelect.appendChild(subDistrictOption);

                                                        // Select this option if it matches the citizen's subdistrict
                                                        if (subdistrict.id == subDistrictId) {
                                                            subDistrictOption.selected = true;
                                                            subDistrictFound = true;
                                                            $('#subdistrict_id').val(subDistrictId);

                                                            // Also load villages for this subdistrict
                                                            const subDistrictCode = subdistrict.code;
                                                            const villageSelect = document.getElementById('village_code');
                                                            villageSelect.innerHTML = '<option value="">Loading...</option>';
                                                            villageSelect.disabled = false;

                                                            fetch(`${BASE_URL}/location/villages/${subDistrictCode}`)
                                                                .then(response => response.json())
                                                                .then(villages => {
                                                                    villageSelect.innerHTML = '<option value="">Pilih Desa</option>';

                                                                    if (villages && villages.length > 0) {
                                                                        villages.forEach(village => {
                                                                            const villageOption = document.createElement('option');
                                                                            villageOption.value = village.code;
                                                                            villageOption.textContent = village.name;
                                                                            villageOption.setAttribute('data-id', village.id);
                                                                            villageSelect.appendChild(villageOption);

                                                                            // Select this option if it matches the citizen's village
                                                                            if (village.id == villageId) {
                                                                                villageOption.selected = true;
                                                                                $('#village_id').val(villageId);
                                                                            }
                                                                        });
                                                                    }
                                                                })
                                                                .catch(error => {
                                                                    console.error('Error loading villages:', error);
                                                                    villageSelect.innerHTML = '<option value="">Error loading data</option>';
                                                                });
                                                        }
                                                    });
                                                }

                                                if (!subDistrictFound) {
                                                    subDistrictSelect.disabled = !districtFound;
                                                }
                                            })
                                            .catch(error => {
                                                console.error('Error loading subdistricts:', error);
                                                subDistrictSelect.innerHTML = '<option value="">Error loading data</option>';
                                            });
                                    }
                                });
                            }

                            if (!districtFound) {
                                districtSelect.disabled = !provinceFound;
                            }

                            break;
                        }
                    }
                }

                // If we have location IDs, populate ONLY the address section location dropdowns
                if (subDistrictId || villageId) {
                    await populateLocationDropdowns(provinceId, districtId, subDistrictId, villageId);
                }
            }
        } catch (error) {
            console.error('Error in nikSelect handler:', error);
        } finally {
            isUpdating = false;
        }
    });

    // Full name select change handler - similar to NIK but starting with name
    $('#fullNameSelect').on('select2:select', async function (e) {
        if (isUpdating) return; // Prevent recursion
        isUpdating = true;

        try {
            const selectedData = e.params.data;
            if (selectedData && selectedData.citizen) {
                const citizen = selectedData.citizen;

                // Update NIK dropdown
                $('#nikSelect').val(citizen.nik.toString()).trigger('change.select2');

                // Fill in KK, address, and other fields
                $('#kk').val(citizen.kk || '');
                $('#address').val(citizen.address || '');
                $('#rt').val(citizen.rt || '');
                $('#rw').val(citizen.rw || '');

                // Automatically populate hamlet based on address, RT, and RW
                const address = citizen.address || '';
                const rt = citizen.rt || '';
                const rw = citizen.rw || '';
                const hamlet = `${address}, RT ${rt}, RW ${rw}`;
                $('#hamlet').val(hamlet);

                // Set location IDs from citizen data
                const provinceId = citizen.province_id;
                const districtId = citizen.district_id;
                const subDistrictId = citizen.subdistrict_id || citizen.sub_district_id;
                const villageId = citizen.village_id;

                // Update the main location fields (top section)
                if (provinceId) {
                    // Set the province_id hidden input
                    $('#province_id').val(provinceId);

                    // Find and select the correct province in the dropdown
                    let provinceFound = false;
                    const provinceSelect = document.getElementById('province_code');
                    for (let i = 0; i < provinceSelect.options.length; i++) {
                        const option = provinceSelect.options[i];
                        if (option.getAttribute('data-id') == provinceId) {
                            provinceSelect.value = option.value;
                            provinceFound = true;

                            // Trigger loading of districts for this province
                            const provinceCode = option.value;

                            // Show loading state
                            const districtSelect = document.getElementById('district_code');
                            districtSelect.innerHTML = '<option value="">Loading...</option>';
                            districtSelect.disabled = false;

                            // Load districts
                            const districts = await fetch(`${BASE_URL}/location/districts/${provinceCode}`)
                                .then(response => response.json())
                                .catch(error => {
                                    console.error('Error loading districts:', error);
                                    return [];
                                });

                            // Populate district dropdown
                            districtSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';
                            let districtFound = false;

                            if (districts && districts.length > 0) {
                                districts.forEach(district => {
                                    const districtOption = document.createElement('option');
                                    districtOption.value = district.code;
                                    districtOption.textContent = district.name;
                                    districtOption.setAttribute('data-id', district.id);
                                    districtSelect.appendChild(districtOption);

                                    // Select this option if it matches the citizen's district
                                    if (district.id == districtId) {
                                        districtOption.selected = true;
                                        districtFound = true;
                                        $('#district_id').val(districtId);

                                        // Also load subdistricts for this district
                                        const districtCode = district.code;
                                        const subDistrictSelect = document.getElementById('subdistrict_code');
                                        subDistrictSelect.innerHTML = '<option value="">Loading...</option>';
                                        subDistrictSelect.disabled = false;

                                        fetch(`${BASE_URL}/location/sub-districts/${districtCode}`)
                                            .then(response => response.json())
                                            .then(subdistricts => {
                                                subDistrictSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                                                let subDistrictFound = false;

                                                if (subdistricts && subdistricts.length > 0) {
                                                    subdistricts.forEach(subdistrict => {
                                                        const subDistrictOption = document.createElement('option');
                                                        subDistrictOption.value = subdistrict.code;
                                                        subDistrictOption.textContent = subdistrict.name;
                                                        subDistrictOption.setAttribute('data-id', subdistrict.id);
                                                        subDistrictSelect.appendChild(subDistrictOption);

                                                        // Select this option if it matches the citizen's subdistrict
                                                        if (subdistrict.id == subDistrictId) {
                                                            subDistrictOption.selected = true;
                                                            subDistrictFound = true;
                                                            $('#subdistrict_id').val(subDistrictId);

                                                            // Also load villages for this subdistrict
                                                            const subDistrictCode = subdistrict.code;
                                                            const villageSelect = document.getElementById('village_code');
                                                            villageSelect.innerHTML = '<option value="">Loading...</option>';
                                                            villageSelect.disabled = false;

                                                            fetch(`${BASE_URL}/location/villages/${subDistrictCode}`)
                                                                .then(response => response.json())
                                                                .then(villages => {
                                                                    villageSelect.innerHTML = '<option value="">Pilih Desa</option>';

                                                                    if (villages && villages.length > 0) {
                                                                        villages.forEach(village => {
                                                                            const villageOption = document.createElement('option');
                                                                            villageOption.value = village.code;
                                                                            villageOption.textContent = village.name;
                                                                            villageOption.setAttribute('data-id', village.id);
                                                                            villageSelect.appendChild(villageOption);

                                                                            // Select this option if it matches the citizen's village
                                                                            if (village.id == villageId) {
                                                                                villageOption.selected = true;
                                                                                $('#village_id').val(villageId);
                                                                            }
                                                                        });
                                                                    }
                                                                })
                                                                .catch(error => {
                                                                    console.error('Error loading villages:', error);
                                                                    villageSelect.innerHTML = '<option value="">Error loading data</option>';
                                                                });
                                                        }
                                                    });
                                                }

                                                if (!subDistrictFound) {
                                                    subDistrictSelect.disabled = !districtFound;
                                                }
                                            })
                                            .catch(error => {
                                                console.error('Error loading subdistricts:', error);
                                                subDistrictSelect.innerHTML = '<option value="">Error loading data</option>';
                                            });
                                    }
                                });
                            }

                            if (!districtFound) {
                                districtSelect.disabled = !provinceFound;
                            }

                            break;
                        }
                    }
                }

                // If we have location IDs, populate the location dropdowns in the address section
                if (subDistrictId || villageId) {
                    await populateLocationDropdowns(provinceId, districtId, subDistrictId, villageId);
                }
            }
        } catch (error) {
            console.error('Error in fullNameSelect handler:', error);
        } finally {
            isUpdating = false;
        }
    });

    // Add event handlers for kk_subdistrict_code and kk_village_code
    $('#kk_subdistrict_code').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const subDistrictId = selectedOption.attr('data-id');
        const subDistrictCode = $(this).val();

        // Update hidden field
        $('#kk_subdistrict_id').val(subDistrictId || '');

        // If we have a subdistrict code but no villages, load villages
        if (subDistrictCode && $('#kk_village_code option').length <= 1) {
            // Show loading state
            $('#kk_village_code').html('<option value="">Loading...</option>').prop('disabled', true);

            // Fetch villages for this subdistrict
            fetch(`${BASE_URL}/location/villages/${subDistrictCode}`)
                .then(response => response.json())
                .then(data => {
                    let villages = [];
                    if (data && Array.isArray(data)) {
                        villages = data;
                    } else if (data && data.data && Array.isArray(data.data)) {
                        villages = data.data;
                    }

                    // Clear and repopulate village dropdown
                    $('#kk_village_code').html('<option value="">Pilih Desa</option>').prop('disabled', false);

                    villages.forEach(village => {
                        const option = document.createElement('option');
                        option.value = village.code;
                        option.textContent = village.name;
                        option.setAttribute('data-id', village.id);
                        document.getElementById('kk_village_code').appendChild(option);

                        // Also update the village maps
                        villageCodeMap[village.id] = village.code;
                        villageIdMap[village.code] = village.id;
                    });
                })
                .catch(error => {
                    console.error('Error fetching villages:', error);
                    $('#kk_village_code').html('<option value="">Error loading villages</option>').prop('disabled', false);
                });
        }
    });

    $('#kk_village_code').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const villageId = selectedOption.attr('data-id');
        const villageCode = $(this).val();

        // Update hidden field
        $('#kk_village_id').val(villageId || '');
    });

    // Add event handlers for sub_district_selector and village_selector
    $('#sub_district_selector').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const subDistrictId = selectedOption.attr('data-id');
        const subDistrictCode = $(this).val();

        // Store only the ID
        $('#sub_district_id_hidden').val(subDistrictId || '');

        // If we have a subdistrict code but no villages, load villages
        if (subDistrictCode) {
            // Show loading state
            $('#village_selector').html('<option value="">Loading...</option>').prop('disabled', true);

            // Fetch villages for this subdistrict
            fetch(`${BASE_URL}/location/villages/${subDistrictCode}`)
                .then(response => response.json())
                .then(data => {
                    let villages = [];
                    if (data && Array.isArray(data)) {
                        villages = data;
                    } else if (data && data.data && Array.isArray(data.data)) {
                        villages = data.data;
                    }

                    // Clear and repopulate village dropdown
                    $('#village_selector').html('<option value="">Pilih Desa</option>').prop('disabled', false);

                    villages.forEach(village => {
                        const option = document.createElement('option');
                        option.value = village.code;
                        option.textContent = village.name;
                        option.setAttribute('data-id', village.id);
                        document.getElementById('village_selector').appendChild(option);

                        // Also update the village maps
                        villageCodeMap[village.id] = village.code;
                        villageIdMap[village.code] = village.id;
                    });
                })
                .catch(error => {
                    console.error('Error fetching villages:', error);
                    $('#village_selector').html('<option value="">Error loading villages</option>').prop('disabled', false);
                });
        }
    });

    $('#village_selector').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const villageId = selectedOption.attr('data-id');
        const villageCode = $(this).val();

        // Store only the ID
        $('#village_id_hidden').val(villageId || '');
    });

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

    // Extract location parameters from URL and add them as hidden fields to the form
    function setupLocationFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        const locationParams = {
            'province_id': urlParams.get('province_id'),
            'district_id': urlParams.get('district_id'),
            'subdistrict_id': urlParams.get('sub_district_id') || urlParams.get('subdistrict_id'), // Handle both naming conventions
            'village_id': urlParams.get('village_id')
        };

        const form = document.querySelector('form');
        if (!form) return;

        // Add hidden fields for each location parameter if they don't already exist
        for (const [key, value] of Object.entries(locationParams)) {
            if (!value) continue; // Skip if parameter is not present

            // Check if field already exists
            let input = form.querySelector(`input[name="${key}"]`);

            if (!input) {
                // Create new hidden field if it doesn't exist
                input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                form.appendChild(input);
            }

            // Set the value from URL parameter
            input.value = value;
        }
    }

    // Call this function immediately to set up location fields from URL
    setupLocationFromUrl();

    // Update form validation to include location check
    document.querySelector('form').addEventListener('submit', function(e) {
        // Make sure URL location params are set before submission
        setupLocationFromUrl();

        // Get all form values
        const formData = new FormData(this);

        // Critical field checks - must match database column names
        const criticalFields = [
            'province_id', 'district_id', 'subdistrict_id', 'village_id',
            'application_type', 'nik', 'full_name', 'kk', 'address', 'rt', 'rw',
            'hamlet'
        ];

        let missingFields = [];
        criticalFields.forEach(field => {
            if (!formData.get(field) || formData.get(field).trim() === '') {
                missingFields.push(field);
            }
        });

        // Check if any critical fields are missing
        if (missingFields.length > 0) {
            e.preventDefault();
            showSweetAlert('error', 'Form tidak lengkap', 'Pastikan semua field yang diperlukan telah diisi');
            return false;
        }

        // Make sure numeric values are actually numeric
        const numericFields = ['province_id', 'district_id', 'subdistrict_id', 'village_id', 'nik', 'kk'];
        let invalidFields = [];

        numericFields.forEach(field => {
            const value = formData.get(field);
            if (value && isNaN(Number(value))) {
                invalidFields.push(field);
            }
        });

        if (invalidFields.length > 0) {
            e.preventDefault();
            showSweetAlert('error', 'Data tidak valid', 'Beberapa field harus berupa angka');
            return false;
        }
    });

    // Handle success/error alerts from session flashes
    if (typeof Swal !== 'undefined') {
        if (SUCCESS_MESSAGE) {
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: SUCCESS_MESSAGE,
                timer: 3000,
                showConfirmButton: false
            });
        }

        if (ERROR_MESSAGE) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: ERROR_MESSAGE,
                timer: 3000,
                showConfirmButton: false
            });
        }
    }

    // Add event listeners for address, RT, and RW fields to update hamlet
    $('#address, #rt, #rw').on('change', function() {
        const address = $('#address').val() || '';
        const rt = $('#rt').val() || '';
        const rw = $('#rw').val() || '';
        const hamlet = `${address}, RT ${rt}, RW ${rw}`;
        $('#hamlet').val(hamlet);
    });

    // Add RF ID Tag event listener
    function setupRfIdTagListener(citizens) {
        const rfIdInput = document.getElementById('rf_id_tag');
        if (!rfIdInput) return;

        // Tambahkan event untuk input dan paste
        rfIdInput.addEventListener('input', function() {
            const rfIdValue = this.value.trim();
            if (rfIdValue.length > 0) {
                // Cari data warga dengan RF ID Tag yang sama
                const matchedCitizen = citizens.find(citizen => {
                    // Jika citizen tidak memiliki rf_id_tag, lewati
                    if (citizen.rf_id_tag === undefined || citizen.rf_id_tag === null) {
                        return false;
                    }

                    // Konversi ke string dan normalisasi
                    const normalizedInput = rfIdValue.toString().replace(/^0+/, '').trim();
                    const normalizedStored = citizen.rf_id_tag.toString().replace(/^0+/, '').trim();

                    // Cek kecocokan persis
                    const exactMatch = normalizedInput === normalizedStored;

                    // Cek kecocokan sebagian (jika input adalah bagian dari rf_id_tag)
                    const partialMatch = normalizedStored.includes(normalizedInput) && normalizedInput.length >= 5;

                    // Kembalikan true jika ada kecocokan persis atau sebagian
                    return exactMatch || partialMatch;
                });

                // Jika ditemukan, isi form
                if (matchedCitizen) {
                    populateCitizenData(matchedCitizen);

                    // Update dropdown NIK dan Nama dengan trigger yang benar
                    if ($('#nikSelect').length) {
                        $('#nikSelect').val(matchedCitizen.nik).trigger('change.select2');
                    }
                    if ($('#fullNameSelect').length) {
                        $('#fullNameSelect').val(matchedCitizen.full_name).trigger('change.select2');
                    }

                    // Set domicile_address jika ada
                    if (matchedCitizen.address && $('#domicile_address').length) {
                        $('#domicile_address').val(matchedCitizen.address);
                    }

                    // Feedback visual berhasil
                    $(rfIdInput).addClass('border-green-500').removeClass('border-red-500 border-gray-300');
                    setTimeout(() => {
                        $(rfIdInput).removeClass('border-green-500').addClass('border-gray-300');
                    }, 2000);
                } else if (rfIdValue.length >= 5) {
                    // Feedback visual tidak ditemukan (hanya untuk input yang cukup panjang)
                    $(rfIdInput).addClass('border-red-500').removeClass('border-green-500 border-gray-300');
                    setTimeout(() => {
                        $(rfIdInput).removeClass('border-red-500').addClass('border-gray-300');
                    }, 2000);
                }
            }
        });

        // Tambahkan event untuk paste
        rfIdInput.addEventListener('paste', function() {
            // Trigger input event after paste
            setTimeout(() => {
                this.dispatchEvent(new Event('input'));
            }, 10);
        });
    }

    // Add this function to populate citizen data
    function populateCitizenData(citizen) {
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

        // Set KK field
        $('#kk').val(citizen.kk || '');

        // Set RT field
        $('#rt').val(citizen.rt || '');

        // Set RW field
        $('#rw').val(citizen.rw || '');

        // Set dusun/hamlet field
        if (citizen.address || citizen.rt || citizen.rw) {
            const address = citizen.address || '';
            const rt = citizen.rt || '';
            const rw = citizen.rw || '';
            const hamlet = `${address}, RT ${rt}, RW ${rw}`;
            $('#hamlet').val(hamlet);
        }

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

        // Set domicile_address if it exists
        if (citizen.address && document.querySelector('#domicile_address')) {
            document.querySelector('#domicile_address').value = citizen.address;
        }

        // Set location IDs if they exist
        if (citizen.province_id) {
            $('#province_id').val(citizen.province_id);
        }
        if (citizen.district_id) {
            $('#district_id').val(citizen.district_id);
        }
        if (citizen.subdistrict_id || citizen.sub_district_id) {
            $('#subdistrict_id').val(citizen.subdistrict_id || citizen.sub_district_id);
        }
        if (citizen.village_id) {
            $('#village_id').val(citizen.village_id);
        }
    }
});

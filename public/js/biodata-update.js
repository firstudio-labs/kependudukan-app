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
        select.disabled = true; // Keep disabled for region fields

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
                    elements.districtSelect.disabled = true; // Keep disabled
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
                    elements.subDistrictSelect.disabled = true; // Keep disabled
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
                    elements.villageSelect.disabled = true; // Keep disabled
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
        'birth_certificate': { 'Ada': '1', 'ada': '1', 'Tidak Ada': '2', 'tidak ada': '2', 'ADA': '1', 'TIDAK ADA': '2' },
        'blood_type': { 'A': '1', 'B': '2', 'AB': '3', 'O': '4', 'A+': '5', 'A-': '6', 'B+': '7', 'B-': '8', 'AB+': '9', 'AB-': '10', 'O+': '11', 'O-': '12', 'Tidak Tahu': '13' },
        'religion': { 'Islam': '1', 'islam': '1', 'Kristen': '2', 'kristen': '2', 'Katholik': '3', 'katholik': '3', 'katolik': '3', 'Hindu': '4', 'hindu': '4', 'Buddha': '5', 'buddha': '5', 'Budha': '5', 'budha': '5', 'Kong Hu Cu': '6', 'kong hu cu': '6', 'konghucu': '6', 'Lainnya': '7', 'lainnya': '7' },
        'marital_status': { 'Belum Kawin': '1', 'belum kawin': '1', 'Kawin Tercatat': '2', 'kawin tercatat': '2', 'Kawin Belum Tercatat': '3', 'kawin belum tercatat': '3', 'Cerai Hidup Tercatat': '4', 'cerai hidup tercatat': '4', 'Cerai Hidup Belum Tercatat': '5', 'cerai hidup belum tercatat': '5', 'Cerai Mati': '6', 'cerai mati': '6', 'BELUM KAWIN': '1', 'KAWIN TERCATAT': '2', 'KAWIN BELUM TERCATAT': '3', 'CERAI HIDUP TERCATAT': '4', 'CERAI HIDUP BELUM TERCATAT': '5', 'CERAI MATI': '6' },
        'marital_certificate': { 'Ada': '1', 'ada': '1', 'Tidak Ada': '2', 'tidak ada': '2', 'ADA': '1', 'TIDAK ADA': '2' },
        'divorce_certificate': { 'Ada': '1', 'ada': '1', 'Tidak Ada': '2', 'tidak ada': '2', 'ADA': '1', 'TIDAK ADA': '2' },
        'family_status': { 'ANAK': '1', 'Anak': '1', 'anak': '1', 'KEPALA KELUARGA': '2', 'Kepala Keluarga': '2', 'kepala keluarga': '2', 'ISTRI': '3', 'Istri': '3', 'istri': '3', 'ORANG TUA': '4', 'Orang Tua': '4', 'orang tua': '4', 'MERTUA': '5', 'Mertua': '5', 'mertua': '5', 'CUCU': '6', 'Cucu': '6', 'cucu': '6', 'FAMILI LAIN': '7', 'Famili Lain': '7', 'famili lain': '7' },
        'mental_disorders': { 'Ada': '1', 'ada': '1', 'Tidak Ada': '2', 'tidak ada': '2', 'ADA': '1', 'TIDAK ADA': '2' },
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

// Set up location dropdown event listeners - DISABLED for region fields
function setupLocationListeners(elements, fixedIds) {
    // Region fields are disabled, so no event listeners needed
    // All region selects will remain disabled and readonly
}

// Function to setup RF ID Tag listener for biodata update
function setupRfIdTagListener() {
    const rfIdInput = document.getElementById('rf_id_tag');
    if (!rfIdInput) {
        return;
    }

    // Remove any existing event listeners by cloning the element
    const newRfIdInput = rfIdInput.cloneNode(true);
    rfIdInput.parentNode.replaceChild(newRfIdInput, rfIdInput);

    // Prevent form submission when Enter is pressed on RF ID input
    newRfIdInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    });

    // Add input event listener with debouncing
    let inputTimeout;
    newRfIdInput.addEventListener('input', function() {
        const rfIdValue = this.value.trim();

        // Clear previous timeout
        clearTimeout(inputTimeout);

        if (rfIdValue.length > 0) {
            // Set a timeout to process the RF ID after a short delay
            inputTimeout = setTimeout(() => {
                processRfIdValue(rfIdValue, newRfIdInput);
            }, 300); // 300ms delay to prevent immediate processing
        }
    });

    // Handle paste events with immediate processing
    newRfIdInput.addEventListener('paste', function(e) {
        e.preventDefault();
        const pastedText = (e.clipboardData || window.clipboardData).getData('text');
        this.value = pastedText;

        // Process immediately after paste
        setTimeout(() => {
            const rfIdValue = this.value.trim();
            if (rfIdValue.length > 0) {
                processRfIdValue(rfIdValue, newRfIdInput);
            }
        }, 100);
    });

    // Handle keyup events for RF ID scanner
    newRfIdInput.addEventListener('keyup', function(e) {
        // Prevent form submission on Enter
        if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }

        const rfIdValue = this.value.trim();
        if (rfIdValue.length > 0) {
            // Clear previous timeout
            clearTimeout(inputTimeout);

            // Set a timeout to process the RF ID
            inputTimeout = setTimeout(() => {
                processRfIdValue(rfIdValue, newRfIdInput);
            }, 200); // 200ms delay for keyup events
        }
    });
}

// Function to process RF ID value for biodata update
function processRfIdValue(rfIdValue, inputElement) {
    // Get all citizens data from the page
    let allCitizens = [];

    // Try to get citizens data from various sources
    if (typeof window.allCitizens !== 'undefined' && Array.isArray(window.allCitizens)) {
        allCitizens = window.allCitizens;
    } else if (typeof window.citizens !== 'undefined' && Array.isArray(window.citizens)) {
        allCitizens = window.citizens;
    } else {
        console.warn('Data citizens tidak tersedia untuk pencarian RF ID');
        return;
    }

    // Cari data warga dengan RF ID Tag yang sama
    const matchedCitizen = allCitizens.find(citizen => {
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
        populateCitizenDataForUpdate(matchedCitizen);

        // Feedback visual berhasil
        $(inputElement).addClass('border-green-500').removeClass('border-red-500 border-gray-300');
        setTimeout(() => {
            $(inputElement).removeClass('border-green-500').addClass('border-gray-300');
        }, 2000);
    } else if (rfIdValue.length >= 5) {
        // Feedback visual tidak ditemukan (hanya untuk input yang cukup panjang)
        $(inputElement).addClass('border-red-500').removeClass('border-green-500 border-gray-300');
        setTimeout(() => {
            $(inputElement).removeClass('border-red-500').addClass('border-gray-300');
        }, 2000);
    }
}

// Function to populate citizen data for biodata update form
function populateCitizenDataForUpdate(citizen) {
    // Set NIK field
    if (citizen.nik) {
        const nikValue = citizen.nik.toString();
        $('#nik').val(nikValue);
    }

    // Set full name field
    if (citizen.full_name) {
        $('#full_name').val(citizen.full_name);
    }

    // Set KK field
    if (citizen.kk) {
        $('#kk').val(citizen.kk.toString());
    }

    // Set address field
    if (citizen.address) {
        $('#address').val(citizen.address);
    }

    // Set RT field
    if (citizen.rt) {
        $('#rt').val(citizen.rt.toString());
    }

    // Set RW field
    if (citizen.rw) {
        $('#rw').val(citizen.rw.toString());
    }

    // Set birth place
    if (citizen.birth_place) {
        $('#birth_place').val(citizen.birth_place);
    }

    // Handle birth_date - reformatting if needed
    if (citizen.birth_date) {
        // Check if birth_date is in DD/MM/YYYY format and convert it
        if (citizen.birth_date.includes('/')) {
            const [day, month, year] = citizen.birth_date.split('/');
            const formattedDate = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
            $('#birth_date').val(formattedDate);
        } else {
            $('#birth_date').val(citizen.birth_date);
        }
    }

    // Set age
    if (citizen.age) {
        $('#age').val(citizen.age);
    }

    // Handle gender selection
    if (citizen.gender) {
        setSelectValueDirectly('gender', citizen.gender);
    }

    // Handle religion selection
    if (citizen.religion) {
        setSelectValueDirectly('religion', citizen.religion);
    }

    // Handle citizen status
    if (citizen.citizen_status) {
        setSelectValueDirectly('citizen_status', citizen.citizen_status);
    }

    // Handle blood type
    if (citizen.blood_type) {
        setSelectValueDirectly('blood_type', citizen.blood_type);
    }

    // Handle family status
    if (citizen.family_status) {
        setSelectValueDirectly('family_status', citizen.family_status);
    }

    // Handle education status
    if (citizen.education_status) {
        setSelectValueDirectly('education_status', citizen.education_status);
    }

    // Handle job type
    if (citizen.job_type_id) {
        setSelectValueDirectly('job_type_id', citizen.job_type_id);
    }

    // Set parent information
    if (citizen.father) {
        $('#father').val(citizen.father);
    }
    if (citizen.mother) {
        $('#mother').val(citizen.mother);
    }
    if (citizen.nik_father) {
        $('#nik_father').val(citizen.nik_father);
    }
    if (citizen.nik_mother) {
        $('#nik_mother').val(citizen.nik_mother);
    }

    // Set contact information
    if (citizen.telephone) {
        $('#telephone').val(citizen.telephone);
    }
    if (citizen.email) {
        $('#email').val(citizen.email);
    }

    // Set hamlet
    if (citizen.hamlet) {
        $('#hamlet').val(citizen.hamlet);
    }

    // Set RF ID Tag field
    if (citizen.rf_id_tag) {
        $('#rf_id_tag').val(citizen.rf_id_tag.toString());
    }

    // Handle birth certificate
    if (citizen.birth_certificate) {
        setSelectValueDirectly('birth_certificate', citizen.birth_certificate);
    }

    // Set birth certificate number
    if (citizen.birth_certificate_no) {
        $('#birth_certificate_no').val(citizen.birth_certificate_no);
    }

    // Handle marital status
    if (citizen.marital_status) {
        setSelectValueDirectly('marital_status', citizen.marital_status);
    }

    // Handle marital certificate
    if (citizen.marital_certificate) {
        setSelectValueDirectly('marital_certificate', citizen.marital_certificate);
    }

    // Set marital certificate number
    if (citizen.marital_certificate_no) {
        $('#marital_certificate_no').val(citizen.marital_certificate_no);
    }

    // Handle marriage date
    if (citizen.marriage_date) {
        // Check if marriage_date is in DD/MM/YYYY format and convert it
        if (citizen.marriage_date.includes('/')) {
            const [day, month, year] = citizen.marriage_date.split('/');
            const formattedDate = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
            $('#marriage_date').val(formattedDate);
        } else {
            $('#marriage_date').val(citizen.marriage_date);
        }
    }

    // Handle divorce certificate
    if (citizen.divorce_certificate) {
        setSelectValueDirectly('divorce_certificate', citizen.divorce_certificate);
    }

    // Set divorce certificate number
    if (citizen.divorce_certificate_no) {
        $('#divorce_certificate_no').val(citizen.divorce_certificate_no);
    }

    // Handle divorce certificate date
    if (citizen.divorce_certificate_date) {
        // Check if divorce_certificate_date is in DD/MM/YYYY format and convert it
        if (citizen.divorce_certificate_date.includes('/')) {
            const [day, month, year] = citizen.divorce_certificate_date.split('/');
            const formattedDate = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
            $('#divorce_certificate_date').val(formattedDate);
        } else {
            $('#divorce_certificate_date').val(citizen.divorce_certificate_date);
        }
    }

    // Handle mental disorders
    if (citizen.mental_disorders) {
        setSelectValueDirectly('mental_disorders', citizen.mental_disorders);
    }

    // Handle disabilities
    if (citizen.disabilities !== undefined && citizen.disabilities !== null) {
        setSelectValueDirectly('disabilities', citizen.disabilities);
    }

    // Set postal code
    if (citizen.postal_code) {
        $('#postal_code').val(citizen.postal_code);
    }
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

    // Set up location dropdown listeners (disabled for region fields)
    setupLocationListeners(elements, fixedIds);

    // Setup RF ID Tag listener
    setupRfIdTagListener();

    // Apply date formatting and force select values
    setTimeout(function() {
        // Format dates using the common function
        reformatAllDateInputs();

        // Force set select values from citizen data
        forceSyncFormWithData();
    }, 300);
});

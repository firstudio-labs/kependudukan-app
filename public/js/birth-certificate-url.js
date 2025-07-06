/**
 * Birth Certificate Form Handler
 * Uses hidden location fields populated from URL parameters
 */

document.addEventListener('DOMContentLoaded', function() {
    // Flag to prevent recursive updates
    let isFatherUpdating = false;
    let isMotherUpdating = false;

    // Get form container and API route
    const formContainer = document.getElementById('birth-form-container');
    const citizenApiRoute = formContainer.dataset.citizenRoute;
    const success = formContainer.dataset.success;
    const error = formContainer.dataset.error;

    // Show notifications if needed
    if (success) showAlert('success', success);
    if (error) showAlert('error', error);

    // Get location IDs from URL query parameters
    const urlParams = new URLSearchParams(window.location.search);
    const provinceId = urlParams.get('province_id');
    const districtId = urlParams.get('district_id');
    const subDistrictId = urlParams.get('sub_district_id');
    const villageId = urlParams.get('village_id');

    // Set form hidden input values
    if (provinceId) document.getElementById('province_id').value = provinceId;
    if (districtId) document.getElementById('district_id').value = districtId;
    if (subDistrictId) document.getElementById('subdistrict_id').value = subDistrictId;
    if (villageId) document.getElementById('village_id').value = villageId;

    // Load citizens data
    $.ajax({
        url: citizenApiRoute,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            let citizens = [];
            if (data && data.data && Array.isArray(data.data)) {
                citizens = data.data;
            } else if (Array.isArray(data)) {
                citizens = data;
            }

            // Setup NIK inputs for both parents
            setupParentNikInput('father', citizens);
            setupParentNikInput('mother', citizens);

            // Initialize Father NIK Select2 with AJAX-only approach
            $('#father_nik').select2({
                placeholder: 'Ketik untuk mencari NIK Ayah',
                width: '100%',
                allowClear: true,
                minimumInputLength: 1,
                ajax: {
                    url: citizenApiRoute,
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;

                        let processedData = [];
                        if (data && data.data && Array.isArray(data.data)) {
                            processedData = data.data;
                        } else if (Array.isArray(data)) {
                            processedData = data;
                        }

                        return {
                            results: processedData.map(citizen => ({
                                id: citizen.nik?.toString() || '',
                                text: citizen.nik?.toString() || '',
                                citizen: citizen
                            })).filter(item => item.id !== ''),
                            pagination: {
                                more: (params.page * 10) < (data.total || 1000)
                            }
                        };
                    },
                    cache: true
                },
                language: {
                    inputTooShort: function() {
                        return 'Ketik minimal 1 karakter untuk mencari NIK...';
                    },
                    noResults: function() {
                        return 'Tidak ada data yang ditemukan';
                    },
                    searching: function() {
                        return 'Mencari...';
                    }
                }
            });

            // Initialize Father Full Name Select2 with AJAX-only approach
            $('#father_full_name').select2({
                placeholder: 'Ketik untuk mencari nama Ayah',
                width: '100%',
                allowClear: true,
                minimumInputLength: 1,
                ajax: {
                    url: citizenApiRoute,
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;

                        let processedData = [];
                        if (data && data.data && Array.isArray(data.data)) {
                            processedData = data.data;
                        } else if (Array.isArray(data)) {
                            processedData = data;
                        }

                        return {
                            results: processedData.map(citizen => ({
                                id: citizen.full_name || '',
                                text: citizen.full_name || '',
                                citizen: citizen
                            })).filter(item => item.id !== ''),
                            pagination: {
                                more: (params.page * 10) < (data.total || 1000)
                            }
                        };
                    },
                    cache: true
                },
                language: {
                    inputTooShort: function() {
                        return 'Ketik minimal 1 karakter untuk mencari nama...';
                    },
                    noResults: function() {
                        return 'Tidak ada data yang ditemukan';
                    },
                    searching: function() {
                        return 'Mencari...';
                    }
                }
            });

            // Similar AJAX configuration for mother_nik
            $('#mother_nik').select2({
                placeholder: 'Ketik untuk mencari NIK Ibu',
                width: '100%',
                allowClear: true,
                minimumInputLength: 1,
                ajax: {
                    url: citizenApiRoute,
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;

                        let processedData = [];
                        if (data && data.data && Array.isArray(data.data)) {
                            processedData = data.data;
                        } else if (Array.isArray(data)) {
                            processedData = data;
                        }

                        return {
                            results: processedData.map(citizen => ({
                                id: citizen.nik?.toString() || '',
                                text: citizen.nik?.toString() || '',
                                citizen: citizen
                            })).filter(item => item.id !== ''),
                            pagination: {
                                more: (params.page * 10) < (data.total || 1000)
                            }
                        };
                    },
                    cache: true
                },
                language: {
                    inputTooShort: function() {
                        return 'Ketik minimal 1 karakter untuk mencari NIK...';
                    },
                    noResults: function() {
                        return 'Tidak ada data yang ditemukan';
                    },
                    searching: function() {
                        return 'Mencari...';
                    }
                }
            });

            // Similar AJAX configuration for mother_full_name
            $('#mother_full_name').select2({
                placeholder: 'Ketik untuk mencari nama Ibu',
                width: '100%',
                allowClear: true,
                minimumInputLength: 1,
                ajax: {
                    url: citizenApiRoute,
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;

                        let processedData = [];
                        if (data && data.data && Array.isArray(data.data)) {
                            processedData = data.data;
                        } else if (Array.isArray(data)) {
                            processedData = data;
                        }

                        return {
                            results: processedData.map(citizen => ({
                                id: citizen.full_name || '',
                                text: citizen.full_name || '',
                                citizen: citizen
                            })).filter(item => item.id !== ''),
                            pagination: {
                                more: (params.page * 10) < (data.total || 1000)
                            }
                        };
                    },
                    cache: true
                },
                language: {
                    inputTooShort: function() {
                        return 'Ketik minimal 1 karakter untuk mencari nama...';
                    },
                    noResults: function() {
                        return 'Tidak ada data yang ditemukan';
                    },
                    searching: function() {
                        return 'Mencari...';
                    }
                }
            });

            // When Father NIK is selected, fill in other fields
            $('#father_nik').on('select2:select', function (e) {
                if (isFatherUpdating) return;
                isFatherUpdating = true;

                const citizen = e.params.data.citizen;
                if (citizen) {
                    // Update father_full_name select
                    $('#father_full_name').val(citizen.full_name).trigger('change.select2');

                    // Fill other father fields
                    populateParentFields(citizen, 'father');

                    // Also set child address if it's empty
                    if (!$('#child_address').val() && citizen.address) {
                        $('#child_address').val(citizen.address);
                    }
                }

                isFatherUpdating = false;
            });

            // Other event handlers for fields
            $('#father_full_name').on('select2:select', function(e) {
                if (isFatherUpdating) return;
                isFatherUpdating = true;

                const citizen = e.params.data.citizen;
                if (citizen) {
                    // Update father_nik select
                    $('#father_nik').val(citizen.nik ? citizen.nik.toString() : '').trigger('change.select2');

                    // Fill other father fields
                    populateParentFields(citizen, 'father');

                    // Also set child address if it's empty
                    if (!$('#child_address').val() && citizen.address) {
                        $('#child_address').val(citizen.address);
                    }
                }

                isFatherUpdating = false;
            });

            $('#mother_nik').on('select2:select', function(e) {
                if (isMotherUpdating) return;
                isMotherUpdating = true;

                const citizen = e.params.data.citizen;
                if (citizen) {
                    // Update mother_full_name select
                    $('#mother_full_name').val(citizen.full_name).trigger('change.select2');

                    // Fill other mother fields
                    populateParentFields(citizen, 'mother');

                    // Also set child address if it's empty
                    if (!$('#child_address').val() && citizen.address) {
                        $('#child_address').val(citizen.address);
                    }
                }

                isMotherUpdating = false;
            });

            $('#mother_full_name').on('select2:select', function(e) {
                if (isMotherUpdating) return;
                isMotherUpdating = true;

                const citizen = e.params.data.citizen;
                if (citizen) {
                    // Update mother_nik select
                    $('#mother_nik').val(citizen.nik ? citizen.nik.toString() : '').trigger('change.select2');

                    // Fill other mother fields
                    populateParentFields(citizen, 'mother');

                    // Also set child address if it's empty
                    if (!$('#child_address').val() && citizen.address) {
                        $('#child_address').val(citizen.address);
                    }
                }

                isMotherUpdating = false;
            });
        }
    });

    function populateParentFields(citizen, parentType) {
        // Birth place
        $(`#${parentType}_birth_place`).val(citizen.birth_place || '');

        // Birth date - handle formatting
        if (citizen.birth_date) {
            let birthDate = citizen.birth_date;
            if (birthDate.includes('/')) {
                const [day, month, year] = birthDate.split('/');
                birthDate = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
            }
            $(`#${parentType}_birth_date`).val(birthDate);
        }

        // Address
        $(`#${parentType}_address`).val(citizen.address || '');

        // Religion - handle conversion
        let religion = citizen.religion;
        if (typeof religion === 'string') {
            const religionMap = {
                'islam': 1, 'kristen': 2, 'katholik': 3, 'hindu': 4,
                'buddha': 5, 'kong hu cu': 6, 'lainnya': 7
            };
            religion = religionMap[religion.toLowerCase()] || '';
        }
        $(`#${parentType}_religion`).val(religion);

        // Job
        $(`#${parentType}_job`).val(citizen.job_type_id || '');
    }
});

// Function to handle success and error messages using SweetAlert
function showAlert(type, text) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: type,
            title: type === 'success' ? 'Sukses!' : 'Gagal!',
            text: text,
            timer: 3000,
            showConfirmButton: false
        });
    } else {
        alert(text);
    }
}

// Add this function to handle NIK input for both parents
function setupParentNikInput(parentType, citizens) {
    const nikInput = document.getElementById(`${parentType}_nik`);
    if (!nikInput) return;

    // Remove Select2 if it exists
    if ($(nikInput).hasClass('select2-hidden-accessible')) {
        $(nikInput).select2('destroy');
    }

    // Convert to regular input
    nikInput.type = 'text';
    nikInput.placeholder = `Masukkan NIK ${parentType === 'father' ? 'Ayah' : 'Ibu'} (16 digit)`;

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
                // Fill parent fields
                populateParentFields(matchedCitizen, parentType);

                // Update full name select
                $(`#${parentType}_full_name`).val(matchedCitizen.full_name).trigger('change');

                // Visual feedback for success
                $(nikInput).addClass('border-green-500').removeClass('border-red-500 border-gray-300');
                setTimeout(() => {
                    $(nikInput).removeClass('border-green-500').addClass('border-gray-300');
                }, 2000);
            } else {
                // Show error alert for NIK not found
                Swal.fire({
                    title: 'Data Tidak Ditemukan',
                    text: `NIK ${parentType === 'father' ? 'Ayah' : 'Ibu'} tidak terdaftar dalam sistem`,
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

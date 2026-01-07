/**
 * Common JavaScript functions for biodata forms
 */

// Display SweetAlert notifications based on session values
function showSweetAlertNotifications() {
    const successMessage = document.body.getAttribute('data-success-message');
    const errorMessage = document.body.getAttribute('data-error-message');

    if (successMessage && successMessage !== "null" && successMessage !== "") {
        Swal.fire({
            icon: 'success',
            title: 'Sukses!',
            text: successMessage,
            timer: 3000,
            showConfirmButton: false
        });
    }

    if (errorMessage && errorMessage !== "null" && errorMessage !== "") {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: errorMessage,
            timer: 3000,
            showConfirmButton: false
        });
    }
}

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
                // For date inputs, ensure yyyy-mm-dd format
                const formattedDate = formatDateForInput(originalValue);
                input.value = formattedDate;
            }
        }
    });
}

// Validate the form before submission
function validateBiodataForm(e) {
    // Check location IDs
    const provinceId = document.getElementById('province_id').value;
    const districtId = document.getElementById('district_id').value;
    const subDistrictId = document.getElementById('sub_district_id').value;
    const villageId = document.getElementById('village_id').value;

    if (!provinceId || !districtId || !subDistrictId || !villageId) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Validasi Gagal',
            text: 'Silakan pilih Provinsi, Kabupaten, Kecamatan, dan Desa',
        });
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
                Swal.fire({
                    icon: 'error',
                    title: 'Format Tanggal Salah',
                    text: `Format tanggal untuk "${input.id}" tidak valid. Format yang benar adalah YYYY-MM-DD.`,
                });
                return false;
            }
        }
    });

    return allDatesValid;
}

// Document ready helper
function documentReady(fn) {
    if (document.readyState === "complete" || document.readyState === "interactive") {
        setTimeout(fn, 1);
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
}

// Initialize the page
documentReady(function() {
    showSweetAlertNotifications();

    // Attach form validation
    const biodataForm = document.querySelector('form');
    if (biodataForm) {
        biodataForm.addEventListener('submit', validateBiodataForm);
    }
});

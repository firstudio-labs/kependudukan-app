/**
 * Sweet Alert Utilities
 * Contains functions for showing different types of alerts
 */

// Function to show success alert
function showSuccessAlert(message) {
    Swal.fire({
        icon: 'success',
        title: 'Sukses!',
        text: message,
        timer: 3000,
        showConfirmButton: false
    });
}

// Function to show error alert
function showErrorAlert(message) {
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: message,
        timer: 3000,
        showConfirmButton: false
    });
}

// Function to show warning alert
function showWarningAlert(message) {
    Swal.fire({
        icon: 'warning',
        title: 'Perhatian!',
        text: message,
        timer: 3000,
        showConfirmButton: false
    });
}

// Function to show info alert
function showInfoAlert(message) {
    Swal.fire({
        icon: 'info',
        title: 'Informasi',
        text: message,
        timer: 3000,
        showConfirmButton: false
    });
}

// Function to show confirmation dialog
function showConfirmDialog(title, message, confirmCallback, cancelCallback) {
    Swal.fire({
        title: title,
        text: message,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed && typeof confirmCallback === 'function') {
            confirmCallback();
        } else if (result.dismiss === Swal.DismissReason.cancel && typeof cancelCallback === 'function') {
            cancelCallback();
        }
    });
}

// Handle form delete confirmation
function setupDeleteConfirmation() {
    // Find all forms with delete-form class
    document.querySelectorAll('form.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const currentForm = this;

            showConfirmDialog('Apakah anda yakin?', 'Data yang dihapus tidak dapat dikembalikan!', function() {
                currentForm.submit();
            });
        });
    });
}

// Initialize alerts from session flash messages
function initializeAlerts() {
    // Check for success and error messages in data attributes
    const successMessage = document.body.getAttribute('data-success-message');
    const errorMessage = document.body.getAttribute('data-error-message');

    if (successMessage) {
        showSuccessAlert(successMessage);
    }

    if (errorMessage) {
        showErrorAlert(errorMessage);
    }
}

// Document ready function that sets up all alert-related functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize alerts
    initializeAlerts();

    // Setup delete confirmations
    setupDeleteConfirmation();
});

/**
 * ============================================
 * Job Consultancy Web Application - JavaScript
 * ============================================
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    initTooltips();
    
    // Form validation
    initFormValidation();
    
    // Confirm delete
    initDeleteConfirmation();
    
    // Search functionality
    initSearch();
});

/**
 * Initialize tooltips (Bootstrap)
 */
function initTooltips() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Form validation
 */
function initFormValidation() {
    var forms = document.querySelectorAll('form.needs-validation');
    
    [].slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
}

/**
 * Confirm delete action
 */
function initDeleteConfirmation() {
    var deleteButtons = document.querySelectorAll('.delete-confirm');
    
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
}

/**
 * Search functionality
 */
function initSearch() {
    var searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            var query = this.value.toLowerCase();
            var items = document.querySelectorAll('.searchable-item');
            
            items.forEach(function(item) {
                var text = item.textContent.toLowerCase();
                
                if (text.includes(query)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
}

/**
 * Show/hide password toggle
 */
function togglePasswordVisibility(inputId) {
    var input = document.getElementById(inputId);
    
    if (input.type === 'password') {
        input.type = 'text';
    } else {
        input.type = 'password';
    }
}

/**
 * Format salary
 */
function formatSalary(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}

/**
 * Export table to CSV
 */
function exportTableToCSV(filename, tableId) {
    var csv = [];
    var table = document.getElementById(tableId);
    
    // Get headers
    var headers = [];
    table.querySelectorAll('th').forEach(function(th) {
        headers.push(th.innerText);
    });
    csv.push(headers.join(','));
    
    // Get rows
    table.querySelectorAll('tr').forEach(function(tr) {
        var row = [];
        tr.querySelectorAll('td').forEach(function(td) {
            row.push('"' + td.innerText.replace(/"/g, '""') + '"');
        });
        if (row.length > 0) {
            csv.push(row.join(','));
        }
    });
    
    // Download
    downloadCSV(csv.join('\n'), filename);
}

/**
 * Download CSV file
 */
function downloadCSV(csv, filename) {
    var link = document.createElement('a');
    link.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
    link.download = filename || 'export.csv';
    link.click();
}

/**
 * Show loading spinner
 */
function showLoading(elementId) {
    var element = document.getElementById(elementId);
    if (element) {
        element.innerHTML = '<div class="spinner"></div> Loading...';
    }
}

/**
 * Hide loading spinner
 */
function hideLoading(elementId) {
    var element = document.getElementById(elementId);
    if (element) {
        element.innerHTML = '';
    }
}

/**
 * AJAX request helper
 */
function ajaxRequest(url, method, data, callback) {
    fetch(url, {
        method: method || 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: data ? JSON.stringify(data) : null,
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (callback) {
            callback(data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

/**
 * Display alert message
 */
function showAlert(message, type) {
    var alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-' + (type || 'info') + ' alert-dismissible fade show';
    alertDiv.setAttribute('role', 'alert');
    alertDiv.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    
    // Insert after navbar
    var navbar = document.querySelector('.navbar');
    if (navbar) {
        navbar.parentNode.insertBefore(alertDiv, navbar.nextSibling);
    }
    
    // Auto-close after 5 seconds
    setTimeout(function() {
        var alert = new bootstrap.Alert(alertDiv);
        alert.close();
    }, 5000);
}

/**
 * Debounce function
 */
function debounce(func, wait) {
    var timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Validate email format
 */
function isValidEmail(email) {
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Validate password strength
 */
function getPasswordStrength(password) {
    var strength = 0;
    
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    
    return strength;
}

/**
 * Get password strength label
 */
function getPasswordStrengthLabel(strength) {
    switch(strength) {
        case 1: return { text: 'Weak', color: 'danger' };
        case 2: return { text: 'Fair', color: 'warning' };
        case 3: return { text: 'Good', color: 'info' };
        case 4: return { text: 'Strong', color: 'success' };
        case 5: return { text: 'Very Strong', color: 'success' };
        default: return { text: 'Very Weak', color: 'danger' };
    }
}

/**
 * Real-time password strength indicator
 */
function initPasswordStrengthIndicator(inputId, indicatorId) {
    var input = document.getElementById(inputId);
    var indicator = document.getElementById(indicatorId);
    
    if (input && indicator) {
        input.addEventListener('input', function() {
            var strength = getPasswordStrength(this.value);
            var label = getPasswordStrengthLabel(strength);
            
            indicator.innerHTML = 'Strength: <span class="badge badge-' + label.color + '">' + label.text + '</span>';
        });
    }
}

/**
 * Format date
 */
function formatDate(dateString) {
    var options = { year: 'numeric', month: 'short', day: 'numeric' };
    var date = new Date(dateString);
    return date.toLocaleDateString('en-US', options);
}

/**
 * Show modal
 */
function showModal(modalId) {
    var modal = new bootstrap.Modal(document.getElementById(modalId));
    modal.show();
}

/**
 * Hide modal
 */
function hideModal(modalId) {
    var modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
    if (modal) {
        modal.hide();
    }
}

console.log('Job Consultancy Application JS Loaded');

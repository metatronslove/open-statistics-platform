/**
 * Open Statistics Platform - Main JavaScript
 */

import './bootstrap';
import 'admin-lte';
import Chart from 'chart.js/auto';

// Global variables
window.Chart = Chart;

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize all popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Auto-dismiss alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Form validation enhancements
    initFormValidation();

    // Table search functionality
    initTableSearch();

    // Date picker enhancements
    initDatePickers();

    // Chart initialization
    initCharts();

    // API token management
    initApiTokenManager();

    // Data export functionality
    initDataExport();
});

/**
 * Initialize form validation
 */
function initFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });

    // Custom validation for numeric inputs
    const numericInputs = document.querySelectorAll('input[type="number"]');
    numericInputs.forEach(input => {
        input.addEventListener('input', function() {
            const value = parseFloat(this.value);
            const min = parseFloat(this.min) || 0;
            const max = parseFloat(this.max) || Infinity;
            
            if (this.value && (value < min || value > max)) {
                this.setCustomValidity(`Değer ${min} ile ${max} arasında olmalıdır.`);
            } else {
                this.setCustomValidity('');
            }
        });
    });

    // Date validation
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        if (input.max) {
            input.addEventListener('change', function() {
                const selectedDate = new Date(this.value);
                const maxDate = new Date(this.max);
                
                if (selectedDate > maxDate) {
                    this.setCustomValidity(`Tarih ${this.max} tarihinden sonra olamaz.`);
                } else {
                    this.setCustomValidity('');
                }
            });
        }
    });
}

/**
 * Initialize table search functionality
 */
function initTableSearch() {
    const searchInputs = document.querySelectorAll('input[name="table_search"]');
    
    searchInputs.forEach(input => {
        const table = input.closest('.card').querySelector('table');
        if (!table) return;
        
        input.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    });
}

/**
 * Initialize date pickers
 */
function initDatePickers() {
    // Set max date for date inputs
    const dateInputs = document.querySelectorAll('input[type="date"]:not([max])');
    const today = new Date().toISOString().split('T')[0];
    
    dateInputs.forEach(input => {
        if (!input.max && !input.hasAttribute('data-no-max')) {
            input.max = today;
        }
    });

    // Date range pickers
    const dateRangePickers = document.querySelectorAll('.date-range-picker');
    dateRangePickers.forEach(picker => {
        const startInput = picker.querySelector('.start-date');
        const endInput = picker.querySelector('.end-date');
        
        if (startInput && endInput) {
            startInput.addEventListener('change', function() {
                endInput.min = this.value;
                if (endInput.value && new Date(endInput.value) < new Date(this.value)) {
                    endInput.value = this.value;
                }
            });
            
            endInput.addEventListener('change', function() {
                if (startInput.value && new Date(this.value) < new Date(startInput.value)) {
                    this.value = startInput.value;
                }
            });
        }
    });
}

/**
 * Initialize charts
 */
function initCharts() {
    // Dataset charts are initialized in their respective views
    // This function handles global chart configurations
    
    Chart.defaults.plugins.legend.display = true;
    Chart.defaults.plugins.legend.position = 'top';
    Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(0, 0, 0, 0.8)';
    Chart.defaults.plugins.tooltip.padding = 10;
    Chart.defaults.plugins.tooltip.cornerRadius = 4;
    Chart.defaults.animation.duration = 1000;
}

/**
 * Initialize API token manager
 */
function initApiTokenManager() {
    const tokenButtons = document.querySelectorAll('.btn-api-token');
    
    tokenButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const action = this.dataset.action;
            const tokenId = this.dataset.tokenId;
            
            try {
                if (action === 'create') {
                    await createApiToken();
                } else if (action === 'revoke' && tokenId) {
                    await revokeApiToken(tokenId);
                }
            } catch (error) {
                showToast('Hata oluştu: ' + error.message, 'error');
            }
        });
    });
}

/**
 * Create new API token
 */
async function createApiToken() {
    const tokenName = prompt('API token adı girin:');
    if (!tokenName) return;
    
    try {
        const response = await fetch('/sanctum/token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ name: tokenName })
        });
        
        const data = await response.json();
        
        if (data.token) {
            showTokenModal(data.token);
            showToast('API token başarıyla oluşturuldu!', 'success');
        } else {
            throw new Error(data.message || 'Token oluşturulamadı');
        }
    } catch (error) {
        showToast('Token oluşturma hatası: ' + error.message, 'error');
    }
}

/**
 * Show token in modal
 */
function showTokenModal(token) {
    const modal = `
        <div class="modal fade" id="tokenModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Yeni API Token</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Bu token'ı güvenli bir yere kaydedin. Bir daha gösterilmeyecek!
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" value="${token}" id="tokenInput" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToken()">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modal);
    const tokenModal = new bootstrap.Modal(document.getElementById('tokenModal'));
    tokenModal.show();
    
    // Remove modal after hiding
    document.getElementById('tokenModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

/**
 * Copy token to clipboard
 */
window.copyToken = function() {
    const tokenInput = document.getElementById('tokenInput');
    tokenInput.select();
    document.execCommand('copy');
    showToast('Token panoya kopyalandı!', 'success');
};

/**
 * Revoke API token
 */
async function revokeApiToken(tokenId) {
    if (!confirm('Bu token\'ı iptal etmek istediğinize emin misiniz?')) {
        return;
    }
    
    try {
        const response = await fetch(`/sanctum/token/${tokenId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            showToast('Token başarıyla iptal edildi!', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            throw new Error('Token iptal edilemedi');
        }
    } catch (error) {
        showToast('Token iptal hatası: ' + error.message, 'error');
    }
}

/**
 * Initialize data export functionality
 */
function initDataExport() {
    const exportButtons = document.querySelectorAll('.btn-export');
    
    exportButtons.forEach(button => {
        button.addEventListener('click', function() {
            const format = this.dataset.format || 'csv';
            const datasetId = this.dataset.datasetId;
            const startDate = this.dataset.startDate;
            const endDate = this.dataset.endDate;
            
            exportData(format, datasetId, startDate, endDate);
        });
    });
}

/**
 * Export data
 */
function exportData(format, datasetId, startDate, endDate) {
    let url = `/api/datasets/${datasetId}/export`;
    const params = new URLSearchParams();
    
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);
    params.append('format', format);
    
    url += '?' + params.toString();
    
    // Create temporary link for download
    const link = document.createElement('a');
    link.href = url;
    link.download = `dataset-${datasetId}-${new Date().toISOString().split('T')[0]}.${format}`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="toast-body">
            ${message}
            <button type="button" class="btn-close float-end" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: 3000
    });
    
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

/**
 * Format number with thousand separators
 */
window.formatNumber = function(number, decimals = 2) {
    return new Intl.NumberFormat('tr-TR', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(number);
};

/**
 * Format date
 */
window.formatDate = function(dateString, format = 'tr-TR') {
    const date = new Date(dateString);
    return date.toLocaleDateString(format);
};

/**
 * Debounce function for performance
 */
window.debounce = function(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

// Make functions available globally
window.initFormValidation = initFormValidation;
window.initTableSearch = initTableSearch;
window.initDatePickers = initDatePickers;
window.showToast = showToast;

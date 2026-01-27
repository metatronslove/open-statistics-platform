import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Add CSRF token to all requests
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}

// Response interceptor for handling errors
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response) {
            switch (error.response.status) {
                case 401:
                    window.location.href = '/login';
                    break;
                case 403:
                    showToast('Bu işlem için yetkiniz bulunmamaktadır.', 'error');
                    break;
                case 419:
                    showToast('Oturum süreniz doldu. Lütfen tekrar giriş yapın.', 'warning');
                    setTimeout(() => window.location.href = '/login', 2000);
                    break;
                case 422:
                    // Validation errors are handled in forms
                    break;
                case 500:
                    showToast('Sunucu hatası oluştu. Lütfen daha sonra tekrar deneyin.', 'error');
                    break;
            }
        }
        return Promise.reject(error);
    }
);

// Import jQuery and Bootstrap
import $ from 'jquery';
window.$ = window.jQuery = $;

import 'bootstrap';

// Import FontAwesome
import '@fortawesome/fontawesome-free/js/all';

// Import AdminLTE
import 'admin-lte/dist/js/adminlte.min.js';

// Import Chart.js
import Chart from 'chart.js/auto';
window.Chart = Chart;

// Import additional plugins if needed
// import 'select2';
// import 'daterangepicker';

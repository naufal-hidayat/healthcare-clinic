import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF Token setup
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Request interceptor
axios.interceptors.request.use(function (config) {
    // Show loading indicator for long requests
    if (config.url.includes('/api/chatbot/')) {
        console.log('Chatbot API request:', config);
    }
    return config;
}, function (error) {
    return Promise.reject(error);
});

// Response interceptor
axios.interceptors.response.use(function (response) {
    return response;
}, function (error) {
    if (error.response && error.response.status === 429) {
        console.warn('Rate limit exceeded. Please try again later.');
    }
    return Promise.reject(error);
});
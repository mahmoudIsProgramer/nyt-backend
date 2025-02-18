// API endpoints
const API_ENDPOINTS = {
    login: '/api/auth/login',
    register: '/api/auth/register'
};

// DOM Elements
const elements = {
    loginForm: document.getElementById('loginForm'),
    registerForm: document.getElementById('registerForm'),
    alertMessage: document.getElementById('alertMessage')
};

// Toggle between login and register forms
function toggleForms() {
    elements.loginForm.classList.toggle('hidden');
    elements.registerForm.classList.toggle('hidden');
    hideAlert();
}

// Show alert message
function showAlert(message, type = 'error') {
    elements.alertMessage.textContent = message;
    elements.alertMessage.className = `alert ${type}`;
    setTimeout(hideAlert, 5000);
}

// Hide alert message
function hideAlert() {
    elements.alertMessage.className = 'alert hidden';
}

// Handle login form submission
async function handleLogin(event) {
    event.preventDefault();
    
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;

    try {
        const response = await fetch(API_ENDPOINTS.login, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Login failed');
        }

        // Store token in localStorage
        localStorage.setItem('token', data.token);
        localStorage.setItem('user', JSON.stringify(data.user));

        showAlert('Login successful! Redirecting...', 'success');
        setTimeout(() => {
            window.location.href = '/dashboard.html';
        }, 1500);

    } catch (error) {
        showAlert(error.message);
    }
}

// Handle register form submission
async function handleRegister(event) {
    event.preventDefault();
    
    const name = document.getElementById('registerName').value;
    const email = document.getElementById('registerEmail').value;
    const password = document.getElementById('registerPassword').value;

    try {
        const response = await fetch(API_ENDPOINTS.register, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ name, email, password })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Registration failed');
        }

        // Store token in localStorage
        localStorage.setItem('token', data.token);
        localStorage.setItem('user', JSON.stringify(data.user));

        showAlert('Registration successful! Redirecting...', 'success');
        setTimeout(() => {
            window.location.href = '/dashboard.html';
        }, 1500);

    } catch (error) {
        showAlert(error.message);
    }
}

// Check if user is already logged in
function checkAuthStatus() {
    const token = localStorage.getItem('token');
    if (token) {
        window.location.href = '/dashboard.html';
    }
}

// Run on page load
checkAuthStatus();

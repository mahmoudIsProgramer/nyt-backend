document.addEventListener('DOMContentLoaded', () => {
    // DOM Elements
    const elements = {
        loginForm: document.getElementById('loginForm'),
        registerForm: document.getElementById('registerForm'),
        alertMessage: document.getElementById('alertMessage'),
        loginTab: document.querySelector('[data-form="login"]'),
        registerTab: document.querySelector('[data-form="register"]')
    };

    // API endpoints
    const API_ENDPOINTS = {
        login: '/api/auth/login',
        register: '/api/auth/register'
    };

    // Add click handlers to tabs
    elements.loginTab.addEventListener('click', () => toggleForms('login'));
    elements.registerTab.addEventListener('click', () => toggleForms('register'));

    // Add submit handlers to forms
    elements.loginForm.addEventListener('submit', handleLogin);
    elements.registerForm.addEventListener('submit', handleRegister);

    // Toggle between forms
    function toggleForms(formType = 'login') {
        // Update tab states
        elements.loginTab.classList.toggle('active', formType === 'login');
        elements.registerTab.classList.toggle('active', formType === 'register');
        
        // Update form visibility
        elements.loginForm.classList.toggle('hidden', formType !== 'login');
        elements.registerForm.classList.toggle('hidden', formType === 'login');
        
        // Clear any existing alerts
        hideAlert();
    }

    // Show alert message
    function showAlert(message, type = 'error') {
        elements.alertMessage.textContent = message;
        elements.alertMessage.className = `alert ${type}`;
        elements.alertMessage.style.display = 'block';
    }

    // Hide alert message
    function hideAlert() {
        elements.alertMessage.style.display = 'none';
        elements.alertMessage.textContent = '';
    }

    // Handle login form submission
    async function handleLogin(event) {
        event.preventDefault();
        hideAlert();
        
        try {
            const response = await fetch(API_ENDPOINTS.login, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email: document.getElementById('loginEmail').value,
                    password: document.getElementById('loginPassword').value
                })
            });

            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Login failed');
            }

            showAlert('Login successful! Redirecting...', 'success');
            
            // Store user data
            localStorage.setItem('token', data.token);
            if (data.user) {
                localStorage.setItem('user', JSON.stringify(data.user));
            }
            
            // Redirect after a short delay
            setTimeout(() => {
                window.location.href = '/dashboard';
            }, 1000);
            
        } catch (error) {
            showAlert(error.message);
        }
    }

    // Handle register form submission
    async function handleRegister(event) {
        event.preventDefault();
        hideAlert();
        
        try {
            const response = await fetch(API_ENDPOINTS.register, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name: document.getElementById('registerName').value,
                    email: document.getElementById('registerEmail').value,
                    password: document.getElementById('registerPassword').value
                })
            });

            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Registration failed');
            }

            showAlert('Registration successful! Redirecting...', 'success');
            
            // Store user data
            localStorage.setItem('token', data.token);
            if (data.user) {
                localStorage.setItem('user', JSON.stringify(data.user));
            }
            
            // Redirect after a short delay
            setTimeout(() => {
                window.location.href = '/dashboard';
            }, 1000);
            
        } catch (error) {
            showAlert(error.message);
        }
    }
});

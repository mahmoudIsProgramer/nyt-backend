<?php

use App\Core\App;
use Dotenv\Dotenv;

try {
    // Initialize the application if not already initialized
    $app = App::getInstance();
    
    // Set content type for HTML
    header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication - NYT</title>
    <link rel="stylesheet" href="/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-tabs">
                <button class="auth-tab active" data-form="login">Login</button>
                <button class="auth-tab" data-form="register">Register</button>
            </div>

            <!-- Login Form -->
            <form id="login-form" class="auth-form active">
                <div class="form-group">
                    <label for="login-email">Email</label>
                    <input type="email" id="login-email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" name="password" required>
                </div>
                <button type="submit" class="auth-button">Login</button>
                <div class="error-message"></div>
            </form>

            <!-- Register Form -->
            <form id="register-form" class="auth-form">
                <div class="form-group">
                    <label for="register-name">Name</label>
                    <input type="text" id="register-name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="register-email">Email</label>
                    <input type="email" id="register-email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="register-password">Password</label>
                    <input type="password" id="register-password" name="password" required>
                </div>
                <button type="submit" class="auth-button">Register</button>
                <div class="error-message"></div>
            </form>
        </div>
    </div>
    <script>
        // API endpoints
        const API_ENDPOINTS = {
            login: '/api/auth/login',
            register: '/api/auth/register'
        };

        // Get form elements
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');
        const authTabs = document.querySelectorAll('.auth-tab');

        // Tab switching
        authTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const formType = tab.dataset.form;
                
                // Update tab states
                authTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                // Update form visibility
                loginForm.classList.toggle('active', formType === 'login');
                registerForm.classList.toggle('active', formType === 'register');
            });
        });

        // Handle login form submission
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const errorDiv = loginForm.querySelector('.error-message');
            
            try {
                const response = await fetch(API_ENDPOINTS.login, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        email: loginForm.querySelector('[name="email"]').value,
                        password: loginForm.querySelector('[name="password"]').value
                    })
                });

                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Login failed');
                }

                // Store token in cookie (httpOnly cookie should be set by server)
                document.cookie = `token=${data.token}; path=/`;
                
                // Redirect to dashboard
                window.location.href = '/dashboard';
                
            } catch (error) {
                errorDiv.textContent = error.message;
                errorDiv.style.display = 'block';
            }
        });

        // Handle register form submission
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const errorDiv = registerForm.querySelector('.error-message');
            
            try {
                const response = await fetch(API_ENDPOINTS.register, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: registerForm.querySelector('[name="name"]').value,
                        email: registerForm.querySelector('[name="email"]').value,
                        password: registerForm.querySelector('[name="password"]').value
                    })
                });

                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Registration failed');
                }

                // Store token in cookie (httpOnly cookie should be set by server)
                document.cookie = `token=${data.token}; path=/`;
                
                // Redirect to dashboard
                window.location.href = '/dashboard';
                
            } catch (error) {
                errorDiv.textContent = error.message;
                errorDiv.style.display = 'block';
            }
        });
    </script>
</body>
</html>
<?php
} catch (\Throwable $e) {
    $isProduction = getenv('APP_ENV') === 'production';
    http_response_code(500);
    
    if ($isProduction) {
        echo 'An error occurred. Please try again later.';
    } else {
        echo 'Error: ' . $e->getMessage();
    }
}
?>

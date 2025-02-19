<?php
// Content for the auth page
?>
<div class="auth-container">
    <div class="auth-box">
        <!-- Alert Message -->
        <div id="alertMessage" class="alert hidden"></div>

        <!-- Auth Tabs -->
        <div class="auth-tabs">
            <button class="auth-tab active" data-form="login">Login</button>
            <button class="auth-tab" data-form="register">Register</button>
        </div>

        <!-- Login Form -->
        <form id="loginForm" class="auth-form">
            <div class="form-group">
                <input type="email" id="loginEmail" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <input type="password" id="loginPassword" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="auth-button">
                <span>Login</span>
            </button>
            <div class="form-footer">
                <a href="#" class="forgot-password">Forgot Password?</a>
            </div>
        </form>

        <!-- Register Form -->
        <form id="registerForm" class="auth-form hidden">
            <div class="form-group">
                <input type="text" id="registerName" name="name" placeholder="Enter your full name" required>
            </div>
            <div class="form-group">
                <input type="email" id="registerEmail" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <input type="password" id="registerPassword" name="password" placeholder="Choose a password" required>
            </div>
            <button type="submit" class="auth-button">
                <span>Create Account</span>
            </button>
        </form>
    </div>
</div>

<style>
/* Modern Authentication Styles */
.auth-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    padding: 20px;
}

.auth-box {
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 420px;
    padding: 2rem;
    transition: all 0.3s ease;
}

.auth-tabs {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 1rem;
}

.auth-tab {
    background: none;
    border: none;
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    font-weight: 600;
    color: #666;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.auth-tab.active {
    color: #4A90E2;
}

.auth-tab.active::after {
    content: '';
    position: absolute;
    bottom: -1rem;
    left: 0;
    width: 100%;
    height: 2px;
    background: #4A90E2;
}

.auth-form {
    transition: all 0.3s ease;
}

.form-group {
    margin-bottom: 1.5rem;
    width: 100%;
}

input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #eee;
    border-radius: 8px;
    font-size: 16px;
    line-height: 1.5;
    transition: all 0.3s ease;
}

input:focus {
    border-color: #4A90E2;
    outline: none;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
}

.auth-button {
    width: 100%;
    padding: 0.875rem;
    background: #4A90E2;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.auth-button:hover {
    background: #357ABD;
    transform: translateY(-1px);
}

.auth-button:active {
    transform: translateY(1px);
}

.form-footer {
    margin-top: 1rem;
    text-align: center;
}

.forgot-password {
    color: #666;
    text-decoration: none;
    font-size: 0.875rem;
    transition: all 0.3s ease;
}

.forgot-password:hover {
    color: #4A90E2;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.hidden {
    display: none;
}

/* Responsive adjustments */
@media (max-width: 480px) {
    .auth-box {
        padding: 1.5rem;
    }
    
    .auth-tab {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
}
</style>

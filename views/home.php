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
 
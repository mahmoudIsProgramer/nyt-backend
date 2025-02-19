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
                <label for="loginEmail">Email</label>
                <input type="email" id="loginEmail" name="email" required>
            </div>
            <div class="form-group">
                <label for="loginPassword">Password</label>
                <input type="password" id="loginPassword" name="password" required>
            </div>
            <button type="submit" class="auth-button">Login</button>
        </form>

        <!-- Register Form -->
        <form id="registerForm" class="auth-form hidden">
            <div class="form-group">
                <label for="registerName">Name</label>
                <input type="text" id="registerName" name="name" required>
            </div>
            <div class="form-group">
                <label for="registerEmail">Email</label>
                <input type="email" id="registerEmail" name="email" required>
            </div>
            <div class="form-group">
                <label for="registerPassword">Password</label>
                <input type="password" id="registerPassword" name="password" required>
            </div>
            <button type="submit" class="auth-button">Register</button>
        </form>
    </div>
</div>

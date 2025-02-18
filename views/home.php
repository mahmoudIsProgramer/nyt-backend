<?php

use App\Core\App;

try {
    $app = App::getInstance();
    header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="NYT Authentication System">
    <title>Authentication - NYT</title>
    <link rel="stylesheet" href="/css/auth.css">
</head>
<body>
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

    <!-- External JavaScript -->
    <script src="/js/auth.js"></script>
</body>
</html>
<?php
} catch (\Throwable $e) {
    http_response_code(500);
    $isProduction = getenv('APP_ENV') === 'production';
    echo $isProduction ? 'An error occurred. Please try again later.' : 'Error: ' . $e->getMessage();
}
?>

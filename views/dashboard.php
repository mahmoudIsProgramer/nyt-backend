<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NYT</title>
    <link rel="stylesheet" href="/css/dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <nav class="dashboard-nav">
            <div class="user-info">
                <span id="user-name">Loading...</span>
                <button id="logout-btn">Logout</button>
            </div>
        </nav>
        
        <main class="dashboard-content">
            <h1>Welcome to Your Dashboard</h1>
            <div id="user-data">
                <!-- User data will be populated here -->
            </div>
        </main>
    </div>

    <script>
        // Check authentication on page load
        const token = document.cookie.split('; ').find(row => row.startsWith('token='));
        if (!token) {
            window.location.href = '/';
        }

        // Logout functionality
        document.getElementById('logout-btn').addEventListener('click', () => {
            document.cookie = 'token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            window.location.href = '/';
        });

        // Load user data
        fetch('/api/user', {
            headers: {
                'Authorization': `Bearer ${token.split('=')[1]}`
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.user) {
                document.getElementById('user-name').textContent = data.user.name;
                document.getElementById('user-data').innerHTML = `
                    <p><strong>Email:</strong> ${data.user.email}</p>
                    <p><strong>Member since:</strong> ${new Date(data.user.created_at).toLocaleDateString()}</p>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            window.location.href = '/';
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Employee Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <div class="card">
            <div class="card-header bg-white text-center py-3">
                <h3 class="mb-0">Employee Management System</h3>
                <p class="text-muted small mb-0">Login to access your dashboard</p>
            </div>
            <div class="card-body p-4">
                <form id="loginForm">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" placeholder="your@email.com" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
            <div class="card-footer bg-white text-center py-3">
                <p class="text-muted small mb-0">Demo credentials:</p>
                <p class="text-muted small mb-0">Admin: admin@example.com / password</p>
                <p class="text-muted small mb-0">Employee: employee@example.com / password</p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            // Simple demo login logic
            if (email === 'admin@example.com' && password === 'password') {
                window.location.href = 'admin/dashboard';
            } else if (email === 'employee@example.com' && password === 'password') {
                window.location.href = 'admin/dashboard';
            } else {
                alert('Invalid credentials');
            }
        });
    </script>
</body>
</html>
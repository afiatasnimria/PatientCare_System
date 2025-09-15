<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Register - AmraAchi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a5276;
            --secondary-color: #2980b9;
            --accent-color: #27ae60;
            --light-bg: #ecf0f1;
            --dark-text: #2c3e50;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .auth-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .auth-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            display: flex;
            min-height: 500px;
        }
        .auth-left {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex: 1;
        }
        .auth-right {
            padding: 40px;
            flex: 1;
        }
        .auth-logo {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .auth-tagline {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        .auth-features {
            list-style: none;
            padding: 0;
        }
        .auth-features li {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .auth-features i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        .nav-tabs {
            border-bottom: none;
            margin-bottom: 30px;
        }
        .nav-tabs .nav-link {
            border: none;
            color: var(--dark-text);
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 30px;
            margin-right: 10px;
        }
        .nav-tabs .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }
        .form-control {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 12px 15px;
            margin-bottom: 20px;
        }
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(26, 82, 118, 0.25);
        }
        .btn-auth {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 30px;
            padding: 12px 30px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-auth:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        .form-check {
            margin-bottom: 20px;
        }
        .social-login {
            margin-top: 30px;
            text-align: center;
        }
        .social-login p {
            color: #666;
            margin-bottom: 15px;
        }
        .social-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 5px;
            border: 1px solid #ddd;
            background: white;
            color: #333;
            transition: all 0.3s;
        }
        .social-btn:hover {
            transform: translateY(-3px);
        }
        .social-btn.facebook:hover {
            background-color: #3b5998;
            color: white;
            border-color: #3b5998;
        }
        .social-btn.google:hover {
            background-color: #dd4b39;
            color: white;
            border-color: #dd4b39;
        }
        .role-selector {
            margin-bottom: 20px;
        }
        .role-selector label {
            font-weight: 600;
            margin-bottom: 10px;
            display: block;
        }
        .role-options {
            display: flex;
            gap: 10px;
        }
        .role-option {
            flex: 1;
            text-align: center;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .role-option:hover {
            border-color: var(--primary-color);
        }
        .role-option.selected {
            border-color: var(--primary-color);
            background-color: rgba(26, 82, 118, 0.1);
        }
        .role-option i {
            font-size: 1.5rem;
            margin-bottom: 5px;
            display: block;
        }
        /* Alert styles */
        .alert-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            max-width: 350px;
        }
        .custom-alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #27ae60;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #e74c3c;
        }
        .alert-icon {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        .alert-close {
            margin-left: auto;
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            opacity: 0.7;
        }
        .alert-close:hover {
            opacity: 1;
        }
        /* Header styles */
        .main-header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        /* Footer styles */
        footer {
            background-color: var(--primary-color);
            color: white;
            padding: 20px 0;
            text-align: center;
        }
        @media (max-width: 768px) {
            .auth-card {
                flex-direction: column;
                max-width: 400px;
            }
            .auth-left {
                padding: 30px;
            }
            .auth-right {
                padding: 30px;
            }
            .role-options {
                flex-wrap: wrap;
            }
            .role-option {
                flex-basis: calc(50% - 5px);
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a class="navbar-brand" href="landing.php">AmraAchi</a>
            </nav>
        </div>
    </header>
    
    <!-- Alert Container -->
    <div class="alert-container" id="alertContainer"></div>
    
    <!-- Auth Container -->
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-left">
                <div class="auth-logo">AmraAchi</div>
                <div class="auth-tagline">Your Complete Digital Healthcare Solution</div>
                <ul class="auth-features">
                    <li><i class="fas fa-check-circle"></i> Book appointments with top doctors</li>
                    <li><i class="fas fa-check-circle"></i> Access your health records anytime</li>
                    <li><i class="fas fa-check-circle"></i> Get emergency assistance instantly</li>
                    <li><i class="fas fa-check-circle"></i> Connect with healthcare professionals</li>
                </ul>
            </div>
            <div class="auth-right">
                <ul class="nav nav-tabs" id="authTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">Login</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab">Register</button>
                    </li>
                </ul>
                
                <div class="tab-content" id="authTabsContent">
                    <!-- Login Form -->
                    <div class="tab-pane fade show active" id="login" role="tabpanel">
                        <form id="loginForm">
                            <div class="role-selector">
                                <label>Login as:</label>
                                <div class="role-options">
                                    <div class="role-option selected" data-role="patient">
                                        <i class="fas fa-user-injured"></i>
                                        <span>Patient</span>
                                    </div>
                                    <div class="role-option" data-role="doctor">
                                        <i class="fas fa-user-md"></i>
                                        <span>Doctor</span>
                                    </div>
                                    <div class="role-option" data-role="nurse">
                                        <i class="fas fa-user-nurse"></i>
                                        <span>Nurse</span>
                                    </div>
                                    <div class="role-option" data-role="driver">
                                        <i class="fas fa-ambulance"></i>
                                        <span>Driver</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <input type="email" class="form-control" id="loginEmail" placeholder="Email" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control" id="loginPassword" placeholder="Password" required>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">
                                    Remember me
                                </label>
                            </div>
                            <button type="submit" class="btn btn-auth">Login</button>
                        </form>
                        
                        <div class="social-login">
                            <p>Or login with</p>
                            <a href="#" class="social-btn facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-btn google"><i class="fab fa-google"></i></a>
                        </div>
                    </div>
                    
                    <!-- Register Form -->
                    <div class="tab-pane fade" id="register" role="tabpanel">
                        <form id="registerForm">
                            <div class="role-selector">
                                <label>Register as:</label>
                                <div class="role-options">
                                    <div class="role-option selected" data-role="patient">
                                        <i class="fas fa-user-injured"></i>
                                        <span>Patient</span>
                                    </div>
                                    <div class="role-option" data-role="doctor">
                                        <i class="fas fa-user-md"></i>
                                        <span>Doctor</span>
                                    </div>
                                    <div class="role-option" data-role="nurse">
                                        <i class="fas fa-user-nurse"></i>
                                        <span>Nurse</span>
                                    </div>
                                    <div class="role-option" data-role="driver">
                                        <i class="fas fa-ambulance"></i>
                                        <span>Driver</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <input type="text" class="form-control" id="registerName" placeholder="Full Name" required>
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" id="registerEmail" placeholder="Email" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control" id="registerPassword" placeholder="Password" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm Password" required>
                            </div>
                            <div class="mb-3">
                                <input type="tel" class="form-control" id="registerPhone" placeholder="Phone Number" required>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                                <label class="form-check-label" for="agreeTerms">
                                    I agree to the Terms and Conditions
                                </label>
                            </div>
                            <button type="submit" class="btn btn-auth">Register</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="copyright">
                <p>&copy; 2023 AmraAchi. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Role selection functionality
        document.querySelectorAll('.role-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove selected class from all options in the same container
                this.parentElement.querySelectorAll('.role-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                // Add selected class to clicked option
                this.classList.add('selected');
            });
        });

        // Alert function
        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            const alert = document.createElement('div');
            alert.className = `custom-alert alert-${type}`;

            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

            alert.innerHTML = `
                <i class="fas ${icon} alert-icon"></i>
                <span>${message}</span>
                <button class="alert-close">&times;</button>
            `;

            alertContainer.appendChild(alert);

            // Add event listener to close button
            alert.querySelector('.alert-close').addEventListener('click', () => {
                alert.remove();
            });

            // Auto remove after 5 seconds
            setTimeout(() => {
                alert.remove();
            }, 5000);
        }

        // NOTE: The client-side hardcoded users were removed and replaced with server-backed authentication.
        // This page now sends AJAX requests to `authenticate.php` and `register_user.php` which use `db.php`.
        // Expected DB schema (example):
        // CREATE TABLE users (
        //   id INT AUTO_INCREMENT PRIMARY KEY,
        //   role VARCHAR(32) NOT NULL,
        //   name VARCHAR(255) NOT NULL,
        //   username VARCHAR(100) NOT NULL UNIQUE,
        //   password_hash VARCHAR(255) NOT NULL,
        //   phone VARCHAR(32)
        // );

        // Helper to post form data and parse JSON
        async function postJSON(url, data) {
            const resp = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
                // Ensure cookies (PHPSESSID) are sent so server-side sessions work
                credentials: 'include'
            });
            if (!resp.ok) {
                // Throw to be caught by callers so they can show a proper error
                throw new Error('Network response was not ok: ' + resp.status);
            }
            return resp.json();
        }

        // Login form submission -> server-side authentication
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const selectedRole = document.querySelector('#login .role-option.selected').dataset.role;
            const username = document.getElementById('loginEmail').value.trim();
            const password = document.getElementById('loginPassword').value;

            if (!username || !password) {
                showAlert('Please enter username and password.', 'danger');
                return;
            }

            try {
                const res = await postJSON('authenticate.php', { role: selectedRole, username, password });
                if (res.success) {
                    showAlert(`Login successful! Welcome, ${res.name}.`, 'success');
                    // Store minimal info in localStorage
                    localStorage.setItem('isLoggedIn', 'true');
                    localStorage.setItem('userRole', selectedRole);
                    localStorage.setItem('userName', res.name);

                    setTimeout(() => {
                        switch (selectedRole) {
                            case 'patient': window.location.href = 'patient.php'; break;
                            case 'doctor':  window.location.href = 'doctor.php'; break;
                            case 'nurse':   window.location.href = 'nurse.php'; break;
                            case 'driver':  window.location.href = 'driver.php'; break;
                            default: window.location.href = 'index.php';
                        }
                    }, 1200);
                } else {
                    showAlert(res.message || 'Invalid username or password.', 'danger');
                }
            } catch (err) {
                console.error(err);
                showAlert('Server error. Please try again later.', 'danger');
            }
        });

        // Register form submission -> server-side registration
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const selectedRole = document.querySelector('#register .role-option.selected').dataset.role;
            const name = document.getElementById('registerName').value.trim();
            const username = document.getElementById('registerEmail').value.trim();
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const phone = document.getElementById('registerPhone').value.trim();

            if (!name || !username || !password || !confirmPassword) {
                showAlert('Please fill out all required fields.', 'danger');
                return;
            }

            if (password !== confirmPassword) {
                showAlert('Passwords do not match. Please try again.', 'danger');
                return;
            }

            try {
                const res = await postJSON('register_user.php', { role: selectedRole, name, username, password, phone });
                if (res.success) {
                    showAlert('Registration successful! You can now login with your credentials.', 'success');
                    this.reset();
                    const loginTab = new bootstrap.Tab(document.getElementById('login-tab'));
                    loginTab.show();
                } else {
                    showAlert(res.message || 'Registration failed.', 'danger');
                }
            } catch (err) {
                console.error(err);
                showAlert('Server error. Please try again later.', 'danger');
            }
        });
    </script>
</body>
</html>
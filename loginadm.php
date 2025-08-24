<?php
session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Initialize CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$action = $_GET['action'] ?? 'login_form';

// Check if admin is already logged in
if (isset($_SESSION['admin'])) {
    header('Location: admin_dashboard.php');
    exit;
}

// Handle login process
if ($_POST && $action === 'login') {
    // Validate CSRF token
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token keamanan tidak valid');
        header('Location: loginadm.php');
        exit;
    }
    
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        set_flash('error', 'Username dan password wajib diisi');
        header('Location: loginadm.php');
        exit;
    }
    
    try {
        $db = (new Database())->getConnection();
        
        // Check admin credentials
        $stmt = $db->prepare("SELECT * FROM admins WHERE username = :username AND status = 'active' LIMIT 1");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin && password_verify($password, $admin['password'])) {
            // Update last login
            $update_stmt = $db->prepare("UPDATE admins SET last_login = NOW(), login_attempts = 0 WHERE id = :id");
            $update_stmt->bindParam(':id', $admin['id']);
            $update_stmt->execute();
            
            // Set session
            unset($admin['password']); // Don't store password in session
            $_SESSION['admin'] = $admin;
            
            // Log login
            log_message("Admin login successful: {$username}", 'INFO', 'admin.log');
            
            set_flash('success', 'Login berhasil! Selamat datang, ' . $admin['full_name']);
            header('Location: admin_dashboard.php');
            exit;
        } else {
            // Handle failed login attempts
            if ($admin) {
                $attempts = $admin['login_attempts'] + 1;
                $update_stmt = $db->prepare("UPDATE admins SET login_attempts = :attempts WHERE username = :username");
                $update_stmt->bindParam(':attempts', $attempts);
                $update_stmt->bindParam(':username', $username);
                $update_stmt->execute();
                
                if ($attempts >= MAX_LOGIN_ATTEMPTS) {
                    $update_stmt = $db->prepare("UPDATE admins SET status = 'locked', locked_until = DATE_ADD(NOW(), INTERVAL :lockout SECOND) WHERE username = :username");
                    $update_stmt->bindParam(':lockout', LOGIN_LOCKOUT_TIME);
                    $update_stmt->bindParam(':username', $username);
                    $update_stmt->execute();
                    
                    set_flash('error', 'Akun dikunci karena terlalu banyak percobaan login gagal');
                } else {
                    $remaining = MAX_LOGIN_ATTEMPTS - $attempts;
                    set_flash('error', "Username atau password salah. Sisa percobaan: {$remaining}");
                }
            } else {
                set_flash('error', 'Username atau password salah');
            }
            
            log_message("Admin login failed: {$username}", 'WARNING', 'admin.log');
            header('Location: loginadm.php');
            exit;
        }
    } catch (PDOException $e) {
        log_message("Admin login error: " . $e->getMessage(), 'ERROR', 'admin.log');
        set_flash('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        header('Location: loginadm.php');
        exit;
    }
}

// Get flash message
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Portal Jemari 5.0 PaskerID</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 500px;
        }

        .login-info {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            padding: 60px 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-info::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: rotate(45deg);
            animation: shine 4s infinite;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            50% { transform: translateX(100%) translateY(100%) rotate(45deg); }
            100% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        }

        .info-content {
            position: relative;
            z-index: 2;
        }

        .admin-icon {
            font-size: 80px;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .login-info h1 {
            font-size: 2.2em;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .login-info p {
            font-size: 1.1em;
            opacity: 0.9;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .features-list {
            list-style: none;
            text-align: left;
        }

        .features-list li {
            padding: 10px 0;
            font-size: 14px;
            opacity: 0.8;
        }

        .features-list i {
            margin-right: 10px;
            width: 20px;
        }

        .login-form {
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .form-header h2 {
            color: #2c3e50;
            font-size: 2em;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .form-header p {
            color: #7f8c8d;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 15px 50px 15px 20px;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3498db;
            background: white;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
            font-size: 16px;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            font-size: 14px;
            color: #7f8c8d;
        }

        .remember-me input {
            margin-right: 8px;
        }

        .forgot-password {
            color: #3498db;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .login-button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .login-button:hover {
            background: linear-gradient(45deg, #2980b9, #1c5a85);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .back-link {
            text-align: center;
            padding: 20px 0;
        }

        .back-link a {
            color: #7f8c8d;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
            color: #3498db;
        }

        .flash-message {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-weight: 500;
            animation: slideIn 0.3s ease;
        }

        .flash-success {
            background: #d5edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .flash-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
                max-width: 400px;
            }
            
            .login-info {
                display: none;
            }
            
            .login-form {
                padding: 40px 30px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Info -->
        <div class="login-info">
            <div class="info-content">
                <div class="admin-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h1>Admin Portal</h1>
                <p>Akses khusus untuk administrator sistem Portal Jemari 5.0 PaskerID</p>
                
                <ul class="features-list">
                    <li><i class="fas fa-users"></i> Kelola pengguna sistem</li>
                    <li><i class="fas fa-database"></i> Manajemen data master</li>
                    <li><i class="fas fa-chart-bar"></i> Dashboard analitik</li>
                    <li><i class="fas fa-cog"></i> Pengaturan sistem</li>
                    <li><i class="fas fa-shield-alt"></i> Keamanan & audit</li>
                </ul>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-form">
            <div class="form-header">
                <h2>Masuk Admin</h2>
                <p>Silakan masuk dengan kredensial administrator Anda</p>
            </div>

            <?php if ($flash): ?>
                <div class="flash-message flash-<?= htmlspecialchars($flash['type']) ?>">
                    <i class="fas fa-<?= $flash['type'] === 'error' ? 'exclamation-circle' : 'check-circle' ?>"></i>
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="loginadm.php?action=login">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                
                <div class="form-group">
                    <label for="username">Username Administrator</label>
                    <div class="input-wrapper">
                        <input type="text" id="username" name="username" required 
                               placeholder="Masukkan username admin" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" required 
                               placeholder="Masukkan password admin">
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" value="1">
                        Ingat saya
                    </label>
                    <a href="#" class="forgot-password">Lupa password?</a>
                </div>

                <button type="submit" class="login-button">
                    <i class="fas fa-sign-in-alt"></i> Masuk ke Admin Panel
                </button>
            </form>

            <div class="back-link">
                <a href="index.php">
                    <i class="fas fa-arrow-left"></i> Kembali ke Halaman Utama
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add form validation
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input[required]');

            inputs.forEach(input => {
                input.addEventListener('blur', validateInput);
                input.addEventListener('input', clearErrors);
            });

            form.addEventListener('submit', function(e) {
                let isValid = true;
                inputs.forEach(input => {
                    if (!validateInput.call(input)) {
                        isValid = false;
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                }
            });

            function validateInput() {
                const input = this;
                const value = input.value.trim();
                
                // Clear previous errors
                clearInputError(input);
                
                if (value === '') {
                    showInputError(input, 'Field ini wajib diisi');
                    return false;
                }
                
                if (input.name === 'username' && value.length < 3) {
                    showInputError(input, 'Username minimal 3 karakter');
                    return false;
                }
                
                if (input.name === 'password' && value.length < 6) {
                    showInputError(input, 'Password minimal 6 karakter');
                    return false;
                }
                
                return true;
            }

            function clearErrors() {
                clearInputError(this);
            }

            function showInputError(input, message) {
                input.style.borderColor = '#e74c3c';
                input.style.background = '#fdeaea';
                
                // Remove existing error message
                const existingError = input.parentNode.querySelector('.error-message');
                if (existingError) {
                    existingError.remove();
                }
                
                // Add new error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.style.color = '#e74c3c';
                errorDiv.style.fontSize = '12px';
                errorDiv.style.marginTop = '5px';
                errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + message;
                
                input.parentNode.appendChild(errorDiv);
            }

            function clearInputError(input) {
                input.style.borderColor = '#e1e8ed';
                input.style.background = '#f8f9fa';
                
                const errorMessage = input.parentNode.querySelector('.error-message');
                if (errorMessage) {
                    errorMessage.remove();
                }
            }

            // Password visibility toggle
            const passwordInput = document.getElementById('password');
            const passwordIcon = passwordInput.parentNode.querySelector('.input-icon');
            
            passwordIcon.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    this.className = 'fas fa-eye-slash input-icon';
                } else {
                    passwordInput.type = 'password';
                    this.className = 'fas fa-lock input-icon';
                }
            });

            // Add loading state to login button
            const loginButton = document.querySelector('.login-button');
            const originalText = loginButton.innerHTML;
            
            form.addEventListener('submit', function() {
                loginButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                loginButton.disabled = true;
                
                // Reset button if form validation fails
                setTimeout(() => {
                    if (!form.checkValidity()) {
                        loginButton.innerHTML = originalText;
                        loginButton.disabled = false;
                    }
                }, 100);
            });

            // Auto focus on username field
            document.getElementById('username').focus();

            // Clear flash messages after 5 seconds
            const flashMessage = document.querySelector('.flash-message');
            if (flashMessage) {
                setTimeout(() => {
                    flashMessage.style.opacity = '0';
                    flashMessage.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        flashMessage.remove();
                    }, 300);
                }, 5000);
            }
        });
    </script>
</body>
</html>
<?php
session_start();

// Handle login
if ($_POST && isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Hardcoded admin credentials (simple without database)
    $admin_users = [
        'admin@pasker.id' => 'admin123',
        'superadmin@pasker.id' => 'super123',
        'manager@pasker.id' => 'manager123'
    ];
    
    if (isset($admin_users[$email]) && $admin_users[$email] === $password) {
        $_SESSION['admin'] = [
            'email' => $email,
            'username' => explode('@', $email)[0],
            'role' => 'admin'
        ];
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Login berhasil! Selamat datang ' . explode('@', $email)[0]];
        header('Location: views/dashboard.php');
        exit;
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Email atau password salah!'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Portal Jemari 5.0 PaskerID</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            text-align: center;
            padding: 30px 20px;
            position: relative;
        }

        .back-btn {
            position: absolute;
            top: 15px;
            right: 20px;
            padding: 5px 12px;
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            text-decoration: none;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 8px rgba(0,0,0,0.2);
        }

        .title-container h1 {
            margin: 0 0 8px 0;
            font-size: 2.2em;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .title-container p {
            margin: 0;
            font-size: 1.1em;
            opacity: 0.9;
            font-weight: 300;
        }

        .login-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-card h2 {
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 1.8em;
            font-weight: 600;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
            z-index: 1;
        }

        .input-group input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #ecf0f1;
            border-radius: 25px;
            font-size: 16px;
            transition: all 0.3s ease;
            outline: none;
            background: #f8f9fa;
        }

        .input-group input:focus {
            border-color: #3498db;
            background: white;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .login-btn:hover {
            background: linear-gradient(135deg, #e55a2b, #d66d1a);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 107, 53, 0.3);
        }

        .back-link {
            margin-top: 20px;
            text-align: center;
        }

        .back-link a {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
            color: #2980b9;
            text-decoration: underline;
        }

        .flash-message {
            margin: 20px 0;
            padding: 15px 20px;
            border-radius: 10px;
            font-weight: 500;
            text-align: center;
        }

        .success {
            background: linear-gradient(45deg, #27ae60, #2ecc71);
            color: white;
        }

        .error {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            color: white;
        }

        .admin-info {
            background: rgba(52, 152, 219, 0.1);
            border: 1px solid #3498db;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            font-size: 14px;
            color: #2c3e50;
        }

        .admin-info h4 {
            margin: 0 0 10px 0;
            color: #3498db;
        }

        .admin-info ul {
            margin: 5px 0;
            padding-left: 20px;
            text-align: left;
        }

        @media (max-width: 768px) {
            .title-container h1 { font-size: 1.8em; }
            .title-container p { font-size: 0.9em; }
            .login-card { padding: 30px 20px; }
            .main-header { padding: 20px; }
            .back-btn { top: 10px; right: 15px; }
        }
    </style>
</head>
<body>
    <header class="main-header">
        <a href="views/dashboard.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        
        <div class="title-container">
            <h1><i class="fas fa-user-shield"></i> Admin Login</h1>
            <p>Portal Jemari 5.0 PaskerID - Panel Administrasi</p>
        </div>
    </header>

    <div class="login-container">
        <div class="login-card">
            <h2><i class="fas fa-lock"></i> Masuk Admin</h2>

            <?php if (!empty($_SESSION['flash'])): ?>
                <div class="flash-message <?= htmlspecialchars($_SESSION['flash']['type']) ?>">
                    <i class="fas fa-<?= $_SESSION['flash']['type'] == 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                    <?= htmlspecialchars($_SESSION['flash']['message']) ?>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <form method="POST">
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email Admin" required>
                </div>

                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>
            </form>

            <div class="back-link">
                <a href="index.php">
                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>

            <div class="admin-info">
                <h4><i class="fas fa-info-circle"></i> Demo Akun Admin:</h4>
                <ul>
                    <li><strong>admin@pasker.id</strong> / admin123</li>
                    <li><strong>superadmin@pasker.id</strong> / super123</li>
                    <li><strong>manager@pasker.id</strong> / manager123</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
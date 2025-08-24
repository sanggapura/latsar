<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit;
}

// Handle logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin']);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Logout berhasil!'];
    header('Location: admin_login.php');
    exit;
}

$admin = $_SESSION['admin'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Portal Jemari 5.0 PaskerID</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .main-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            position: relative;
        }

        .logout-btn {
            position: absolute;
            top: 15px;
            right: 20px;
            padding: 5px 12px;
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            color: white;
            text-decoration: none;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 8px rgba(0,0,0,0.2);
        }

        .header-content {
            padding: 50px 20px 20px;
            text-align: center;
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

        .admin-welcome {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            margin-top: 15px;
            border-radius: 10px;
        }

        .dashboard-content {
            margin: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 40px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .menu-card {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 30px 20px;
            border-radius: 15px;
            text-align: center;
            text-decoration: none;
            transition: transform 0.3s ease;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        .menu-card i {
            font-size: 2.5em;
            margin-bottom: 15px;
            display: block;
        }

        .menu-card h3 {
            margin: 0;
            font-size: 1.2em;
            color: white;
        }

        .menu-card.orange { background: linear-gradient(135deg, #ff6b35, #f7931e); }
        .menu-card.green { background: linear-gradient(135deg, #27ae60, #2ecc71); }
        .menu-card.purple { background: linear-gradient(135deg, #9b59b6, #8e44ad); }
        .menu-card.red { background: linear-gradient(135deg, #e74c3c, #c0392b); }

        @media (max-width: 768px) {
            .title-container h1 { font-size: 1.8em; }
            .header-content { padding: 40px 20px 20px; }
            .logout-btn { top: 10px; right: 15px; }
            .menu-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <header class="main-header">
        <a href="?logout=1" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>

        <div class="header-content">
            <div class="title-container">
                <h1><i class="fas fa-tachometer-alt"></i> Dashboard Admin</h1>
                <p>Portal Jemari 5.0 PaskerID - Panel Administrasi</p>
            </div>
            
            <div class="admin-welcome">
                <i class="fas fa-user-shield"></i>
                Selamat Datang, <?= htmlspecialchars($admin['username']) ?>!
            </div>
        </div>
    </header>

    <div class="dashboard-content">
        <div class="menu-grid">
            <a href="admin_add_user.php" class="menu-card">
                <i class="fas fa-user-plus"></i>
                <h3>Tambah User</h3>
            </a>
            
            <a href="admin_tahapan.php" class="menu-card orange">
                <i class="fas fa-tasks"></i>
                <h3>Tahapan</h3>
            </a>
            
            <a href="admin_add_file.php" class="menu-card green">
                <i class="fas fa-folder-plus"></i>
                <h3>Tambah File</h3>
            </a>
            
            <a href="admin_schedule.php" class="menu-card purple">
                <i class="fas fa-calendar-plus"></i>
                <h3>Tambah Schedule</h3>
            </a>
            
            <a href="index.php" class="menu-card red">
                <i class="fas fa-home"></i>
                <h3>Portal Utama</h3>
            </a>
        </div>
    </div>
</body>
</html>
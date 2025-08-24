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
        }

        .top-bar {
            background: rgba(0,0,0,0.1);
            padding: 8px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            font-weight: 500;
        }

        .auth-buttons a {
            padding: 6px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 500;
            font-size: 13px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
            color: white;
        }

        .btn-logout { 
            background: linear-gradient(45deg, #e74c3c, #c0392b); 
        }

        .auth-buttons a:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .header-content {
            padding: 20px;
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

        .dashboard-content {
            margin: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .welcome-banner {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .welcome-banner h2 {
            margin: 0 0 10px 0;
            font-size: 1.8em;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 2.5em;
            margin-bottom: 15px;
        }

        .stat-card h3 {
            margin: 0 0 5px 0;
            font-size: 2em;
        }

        .stat-card p {
            margin: 0;
            opacity: 0.9;
            font-weight: 500;
        }

        .stat-card.orange {
            background: linear-gradient(135deg, #ff6b35, #f7931e);
        }

        .stat-card.green {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
        }

        .stat-card.purple {
            background: linear-gradient(135deg, #9b59b6, #8e44ad);
        }

        .quick-actions {
            padding: 30px;
            border-top: 1px solid #ecf0f1;
        }

        .quick-actions h3 {
            margin: 0 0 20px 0;
            color: #2c3e50;
            font-size: 1.5em;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 20px;
            background: #f8f9fa;
            border: 2px solid #ecf0f1;
            border-radius: 10px;
            text-decoration: none;
            color: #2c3e50;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            background: white;
            border-color: #3498db;
            color: #3498db;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            .title-container h1 { font-size: 1.8em; }
            .top-bar { 
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
            .stats-grid { 
                grid-template-columns: 1fr;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="top-bar">
            <div class="admin-info">
                <i class="fas fa-user-shield"></i>
                <span>Admin: <?= htmlspecialchars($admin['username']) ?></span>
            </div>
            <div class="auth-buttons">
                <a href="?logout=1" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <div class="header-content">
            <div class="title-container">
                <h1><i class="fas fa-tachometer-alt"></i> Dashboard Admin</h1>
                <p>Portal Jemari 5.0 PaskerID - Panel Administrasi</p>
            </div>
        </div>
    </header>

    <div class="dashboard-content">
        <div class="welcome-banner">
            <h2><i class="fas fa-hands"></i> Selamat Datang, <?= htmlspecialchars($admin['username']) ?>!</h2>
            <p>Kelola sistem Portal Jemari 5.0 dengan mudah dari dashboard ini</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <h3>125</h3>
                <p>Total Pengguna</p>
            </div>
            
            <div class="stat-card orange">
                <i class="fas fa-handshake"></i>
                <h3>48</h3>
                <p>Kemitraan Aktif</p>
            </div>
            
            <div class="stat-card green">
                <i class="fas fa-file-alt"></i>
                <h3>89</h3>
                <p>Dokumen</p>
            </div>
            
            <div class="stat-card purple">
                <i class="fas fa-calendar-check"></i>
                <h3>23</h3>
                <p>Jadwal Bulan Ini</p>
            </div>
        </div>

        <div class="quick-actions">
            <h3><i class="fas fa-bolt"></i> Aksi Cepat</h3>
            <div class="actions-grid">
                <a href="#" class="action-btn">
                    <i class="fas fa-user-plus"></i>
                    <span>Tambah Pengguna</span>
                </a>
                <a href="#" class="action-btn">
                    <i class="fas fa-handshake"></i>
                    <span>Kelola Kemitraan</span>
                </a>
                <a href="#" class="action-btn">
                    <i class="fas fa-folder-plus"></i>
                    <span>Upload Dokumen</span>
                </a>
                <a href="#" class="action-btn">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Buat Jadwal</span>
                </a>
                <a href="#" class="action-btn">
                    <i class="fas fa-chart-bar"></i>
                    <span>Lihat Laporan</span>
                </a>
                <a href="index.php" class="action-btn">
                    <i class="fas fa-home"></i>
                    <span>Ke Portal Utama</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
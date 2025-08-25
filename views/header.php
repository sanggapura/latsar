<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Jemari 5.0 PaskerID</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .main-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            position: relative;
        }

        /* Top bar untuk auth buttons */
        .top-bar {
            background: rgba(0,0,0,0.1);
            padding: 8px 20px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
        }

        .auth-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
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
        }

        .btn-logout { 
            background: linear-gradient(45deg, #e74c3c, #c0392b); 
            color: white;
        }
        .btn-admin { 
            background: linear-gradient(45deg, #27ae60, #2ecc71); 
            color: white;
        }
        .btn-login { 
            background: linear-gradient(45deg, #3498db, #2980b9); 
            color: white;
        }

        .auth-buttons a:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        /* Header content */
        .header-content {
            padding: 20px;
            text-align: center;
        }

        .title-container h1 {
            margin: 0 0 8px 0;
            font-size: 2.2em;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            background: linear-gradient(45deg, #fff, #ecf0f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .title-container p {
            margin: 0;
            font-size: 1.1em;
            opacity: 0.9;
            font-weight: 300;
        }

        /* Modern Navigation */
        .main-nav {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 12px 20px;
            margin-top: 15px;
        }

        .main-nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .main-nav ul li {
            position: relative;
            flex: none;
        }

        .main-nav ul li > a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            text-decoration: none;
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            color: white;
            border-radius: 25px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(255, 107, 53, 0.3);
            white-space: nowrap;
            width: 280px;
            height: 44px;
            justify-content: center;
            text-align: center;
        }

        .main-nav ul li > a:hover {
            background: linear-gradient(135deg, #e55a2b, #d66d1a);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
            color: white;
        }

        /* Modern Dropdown - Improved hover behavior */
        .main-nav ul li {
            position: relative;
        }

        /* Invisible hover area untuk mencegah dropdown hilang */
        .main-nav ul li::after {
            content: '';
            position: absolute;
            top: 100%;
            left: -10px;
            right: -10px;
            height: 15px;
            z-index: 999;
        }

        .main-nav ul li ul {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            left: 50%;
            transform: translateX(-50%);
            background: white;
            border-radius: 12px;
            min-width: 220px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            z-index: 1000;
            overflow: hidden;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            /* Padding area untuk hover yang lebih mudah */
            padding: 8px 0;
        }

        .main-nav ul li ul::before {
            content: '';
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-bottom: 8px solid white;
        }

        .main-nav ul li ul li {
            display: block;
            width: 100%;
        }

        .main-nav ul li ul li a {
            padding: 14px 20px;
            background: white;
            color: #2c3e50;
            border-radius: 0;
            font-weight: 500;
            text-align: left;
            transition: all 0.2s ease;
            border-bottom: 1px solid #ecf0f1;
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 8px;
            border-radius: 6px;
        }

        .main-nav ul li ul li:last-child a {
            border-bottom: none;
        }

        .main-nav ul li ul li a:hover {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            transform: none;
            box-shadow: none;
            padding-left: 25px;
        }

        /* Improved hover dengan delay */
        .main-nav ul li:hover ul {
            display: block;
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
            animation: dropdownFadeIn 0.3s ease;
        }

        /* Dropdown tetap terbuka saat hover pada dropdown item */
        .main-nav ul li ul:hover {
            display: block;
            opacity: 1;
            visibility: visible;
        }

        @keyframes dropdownFadeIn {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        /* Flash Messages */
        .flash-message {
            margin: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            font-weight: 500;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success {
            background: linear-gradient(45deg, #27ae60, #2ecc71);
            color: white;
            border-left: 4px solid #1e8449;
        }

        .error {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            color: white;
            border-left: 4px solid #a93226;
        }

        .warning {
            background: linear-gradient(45deg, #f39c12, #e67e22);
            color: white;
            border-left: 4px solid #d35400;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .title-container h1 {
                font-size: 1.8em;
            }

            .title-container p {
                font-size: 0.9em;
            }

            .main-nav ul {
                justify-content: center;
                gap: 10px;
            }

            .main-nav ul li > a {
                padding: 10px 16px;
                font-size: 13px;
                min-width: 120px;
            }

            .main-nav ul li ul {
                position: fixed;
                left: 10px;
                right: 10px;
                transform: none;
                min-width: auto;
            }

            .main-nav ul li ul::before {
                display: none;
            }

            .top-bar {
                flex-direction: column;
                gap: 8px;
                text-align: center;
            }

            .auth-buttons {
                flex-wrap: wrap;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .main-nav ul {
                flex-direction: column;
                align-items: center;
                gap: 8px;
            }

            .main-nav ul li > a {
                min-width: 200px;
                justify-content: center;
            }
        }

        /* Main content area */
        main {
            background: white;
            min-height: calc(100vh - 200px);
            margin: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <?php if (isset($_SESSION['user'])): ?>
        <div class="top-bar">
            <div class="auth-buttons">
                <a href="admin/admin_login.php?action=admin_login" class="btn-admin">
                    <i class="fas fa-user-shield"></i> Admin Panel
                </a>
                <a href="auth.php?action=logout" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> 
                    Logout (<?= htmlspecialchars($_SESSION['user']['username'] ?? $_SESSION['user']['email'] ?? 'user') ?>)
                </a>
            </div>
        </div>
        <?php endif; ?>

        <div class="header-content">
            <div class="title-container">
                <h1><i class="fas fa-handshake"></i> Portal Jemari 5.0 PaskerID</h1>
                <p>Sistem Informasi Substansi Jejaring Kemitraan Pusat Pasar Kerja</p>
            </div>

            <?php if (isset($_SESSION['user'])): ?>
            <nav class="main-nav">
                <ul>
                    <li>
                        <a href="index.php?action=stages">
                            <i class="fas fa-tasks"></i> Tahapan Kerjasama <i class="fas fa-chevron-down"></i>
                        </a>
                        <ul>
                            <li><a href="index.php?action=stages&type=kementerian">
                                <i class="fas fa-building"></i> Kementerian/Lembaga
                            </a></li>
                            <li><a href="index.php?action=stages&type=daerah">
                                <i class="fas fa-map-marker-alt"></i> Pemerintah Daerah
                            </a></li>
                            <li><a href="index.php?action=stages&type=mitra">
                                <i class="fas fa-users"></i> Mitra Pembangunan
                            </a></li>
                            <li><a href="index.php?action=stages&type=swasta">
                                <i class="fas fa-industry"></i> Swasta/Perusahaan
                            </a></li>
                        </ul>
                    </li>
                    <li><a href="index.php?action=contacts">
                        <i class="fas fa-address-book"></i> Kontak Mitra
                    </a></li>
                    <li><a href="index.php?action=documents">
                        <i class="fas fa-folder-open"></i> File Dokumen
                    </a></li>
                    <li><a href="index.php?action=schedule">
                        <i class="fas fa-calendar-alt"></i> Schedule
                    </a></li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <?php if (!empty($_SESSION['flash'])): ?>
            <div class="flash-message <?= htmlspecialchars($_SESSION['flash']['type']) ?>">
                <i class="fas fa-<?= $_SESSION['flash']['type'] == 'success' ? 'check-circle' : ($_SESSION['flash']['type'] == 'error' ? 'exclamation-circle' : 'exclamation-triangle') ?>"></i>
                <?= htmlspecialchars($_SESSION['flash']['message']) ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>
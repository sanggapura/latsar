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
            /* Background gambar dengan overlay gradient */
            background: 
                linear-gradient(135deg, rgba(44, 62, 80, 0.8) 0%, rgba(52, 152, 219, 0.8) 100%),
                url('assets/images/header-bg.jpg') center center / cover no-repeat;
            
            /* Fallback jika gambar tidak tersedia */
            background-color: #2c3e50;
            
            color: white;
            padding: 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            position: relative;
            min-height: 300px; /* Minimum height untuk memastikan tampilan bagus */
        }

        /* Alternatif background dengan pattern jika tidak ada gambar */
        .main-header.no-image {
            background: 
                linear-gradient(135deg, rgba(44, 62, 80, 0.9) 0%, rgba(52, 152, 219, 0.9) 100%),
                radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.08) 0%, transparent 50%),
                linear-gradient(45deg, #2c3e50 0%, #3498db 100%);
        }

        /* Overlay pattern untuk tekstur tambahan */
        .main-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                repeating-linear-gradient(
                    45deg,
                    transparent,
                    transparent 2px,
                    rgba(255,255,255,0.03) 2px,
                    rgba(255,255,255,0.03) 4px
                );
            pointer-events: none;
        }

        /* Top bar untuk auth buttons */
        .top-bar {
            background: rgba(0,0,0,0.2);
            backdrop-filter: blur(5px);
            padding: 8px 20px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
            position: relative;
            z-index: 2;
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
            backdrop-filter: blur(10px);
        }

        .btn-logout { 
            background: linear-gradient(45deg, rgba(231, 76, 60, 0.9), rgba(192, 57, 43, 0.9)); 
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .btn-admin { 
            background: linear-gradient(45deg, rgba(39, 174, 96, 0.9), rgba(46, 204, 113, 0.9)); 
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .btn-login { 
            background: linear-gradient(45deg, rgba(52, 152, 219, 0.9), rgba(41, 128, 185, 0.9)); 
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .auth-buttons a:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            backdrop-filter: blur(15px);
        }

        /* Header content */
        .header-content {
            padding: 20px;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .title-container h1 {
            margin: 0 0 8px 0;
            font-size: 2.2em;
            font-weight: 700;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
            background: linear-gradient(45deg, #fff, #ecf0f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
        }

        .title-container p {
            margin: 0;
            font-size: 1.1em;
            opacity: 0.95;
            font-weight: 400;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.5);
            background: rgba(255,255,255,0.1);
            padding: 10px 20px;
            border-radius: 25px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            display: inline-block;
            margin-top: 10px;
        }

        /* Modern Navigation */
        .main-nav {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(15px);
            padding: 12px 20px;
            margin-top: 15px;
            border-top: 1px solid rgba(255,255,255,0.2);
            position: relative;
            z-index: 2;
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
            background: linear-gradient(135deg, rgba(255, 107, 53, 0.9), rgba(247, 147, 30, 0.9));
            color: white;
            border-radius: 25px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            box-shadow: 0 3px 15px rgba(255, 107, 53, 0.4);
            white-space: nowrap;
            width: 200px;
            height: 44px;
            justify-content: center;
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .main-nav ul li > a:hover {
            background: linear-gradient(135deg, rgba(229, 90, 43, 0.95), rgba(214, 109, 26, 0.95));
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(255, 107, 53, 0.5);
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
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(15px);
            border-radius: 12px;
            min-width: 220px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            z-index: 1000;
            overflow: hidden;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            padding: 8px 0;
            border: 1px solid rgba(255,255,255,0.3);
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
            border-bottom: 8px solid rgba(255,255,255,0.95);
        }

        .main-nav ul li ul li {
            display: block;
            width: 100%;
        }

        .main-nav ul li ul li a {
            padding: 14px 20px;
            background: transparent;
            color: #2c3e50;
            border-radius: 0;
            font-weight: 500;
            text-align: left;
            transition: all 0.2s ease;
            border-bottom: 1px solid rgba(44, 62, 80, 0.1);
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 8px;
            border-radius: 6px;
            backdrop-filter: none;
            border: none;
            box-shadow: none;
            width: auto;
            height: auto;
            justify-content: flex-start;
        }

        .main-nav ul li ul li:last-child a {
            border-bottom: none;
        }

        .main-nav ul li ul li a:hover {
            background: linear-gradient(45deg, rgba(52, 152, 219, 0.9), rgba(41, 128, 185, 0.9));
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
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
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
            background: linear-gradient(45deg, rgba(39, 174, 96, 0.9), rgba(46, 204, 113, 0.9));
            color: white;
            border-left: 4px solid #1e8449;
        }

        .error {
            background: linear-gradient(45deg, rgba(231, 76, 60, 0.9), rgba(192, 57, 43, 0.9));
            color: white;
            border-left: 4px solid #a93226;
        }

        .warning {
            background: linear-gradient(45deg, rgba(243, 156, 18, 0.9), rgba(230, 126, 34, 0.9));
            color: white;
            border-left: 4px solid #d35400;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .main-header {
                min-height: 250px;
            }

            .title-container h1 {
                font-size: 1.8em;
            }

            .title-container p {
                font-size: 0.9em;
                padding: 8px 16px;
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
            .main-header {
                min-height: 200px;
            }

            .main-nav ul {
                flex-direction: column;
                align-items: center;
                gap: 8px;
            }

            .main-nav ul li > a {
                min-width: 200px;
                justify-content: center;
            }

            .title-container h1 {
                font-size: 1.5em;
            }

            .title-container p {
                font-size: 0.8em;
                padding: 6px 12px;
            }
        }

        /* Responsive background image */
        @media (max-width: 1200px) {
            .main-header {
                background-size: cover;
                background-position: center center;
            }
        }

        @media (max-width: 768px) {
            .main-header {
                background-size: cover;
                background-position: center top;
            }
        }

        @media (max-width: 480px) {
            .main-header {
                background-size: cover;
                background-position: center center;
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

        /* Loading placeholder untuk background image */
        .header-loading {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            transition: all 0.5s ease;
        }

        /* Animasi ketika background image dimuat */
        .header-loaded {
            animation: headerFadeIn 0.8s ease;
        }

        @keyframes headerFadeIn {
            from {
                opacity: 0.8;
            }
            to {
                opacity: 1;
            }
        }
    </style>

    <script>
        // Script untuk menangani loading background image
        document.addEventListener('DOMContentLoaded', function() {
            const header = document.querySelector('.main-header');
            const bgImage = new Image();
            
            // Daftar gambar yang bisa digunakan sebagai background
            const backgroundImages = [
                'assets/images/header-bg.jpg',
                'assets/images/background.jpg', 
                'assets/images/bg-header.png',
                'https://images.unsplash.com/photo-1557804506-669a67965ba0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1974&q=80' // Fallback dari Unsplash
            ];
            
            let currentImageIndex = 0;
            
            function loadBackgroundImage() {
                if (currentImageIndex >= backgroundImages.length) {
                    // Jika semua gambar gagal dimuat, gunakan pattern
                    header.classList.add('no-image');
                    return;
                }
                
                bgImage.src = backgroundImages[currentImageIndex];
                
                bgImage.onload = function() {
                    // Gambar berhasil dimuat
                    const imageUrl = backgroundImages[currentImageIndex];
                    header.style.backgroundImage = `
                        linear-gradient(135deg, rgba(44, 62, 80, 0.8) 0%, rgba(52, 152, 219, 0.8) 100%), 
                        url('${imageUrl}')
                    `;
                    header.classList.add('header-loaded');
                };
                
                bgImage.onerror = function() {
                    // Gambar gagal dimuat, coba gambar berikutnya
                    currentImageIndex++;
                    loadBackgroundImage();
                };
            }
            
            // Mulai loading background image
            header.classList.add('header-loading');
            loadBackgroundImage();
        });
    </script>
</head>
<body>
    <header class="main-header">
        <?php if (isset($_SESSION['user'])): ?>
        <div class="top-bar">
            <div class="auth-buttons">
                <a href="loginadm.php?action=admin_login" class="btn-admin">
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
                    <li><a href="index.php?action=partners">
                        <i class="fas fa-handshake"></i> Partner
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
    </main>
</body>
</html>
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

        /* Header content */
        .header-content {
            padding: 40px 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .title-container h1 {
            margin: 0 0 8px 0;
            font-size: 2.4em;
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
            margin-top: 20px;
            border-radius: 12px;
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
    min-width: 200px;          /* semua tombol minimal 150px */
    justify-content: center;   /* teks selalu center */
}


        .main-nav ul li > a:hover {
            background: linear-gradient(135deg, #e55a2b, #d66d1a);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
            color: white;
        }

        /* Main content area */
        main {
            background: white;
            min-height: calc(100vh - 250px);
            margin: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="header-content">
            <div class="title-container">
                <h1><i class="fas fa-handshake"></i> Portal Jemari 5.0 PaskerID</h1>
                <p>Sistem Informasi Substansi Jejaring Kemitraan Pusat Pasar Kerja</p>
            </div>

            <!-- Menu selalu muncul -->
            <nav class="main-nav">
                <ul>
                    <li>
                        <a href="\latsar\admin\tahapan\index.php?action=stages">
                            <i class="fas fa-tasks"></i> Tahapan Kerjasama 
                        </a>
                    </li>
                    <li>
                        <a href="\latsar\admin\kontak\daftar_kontak.php?action=contacts">
                            <i class="fas fa-address-book"></i> Kontak Mitra
                        </a>
                    </li>
                    <li>
                        <a href="\latsar\admin\dokumen\dokumen_index.php?action=documents">
                            <i class="fas fa-folder-open"></i> File Dokumen
                        </a>
                    </li>
                    <li>
                        <a href="\latsar\admin\schedule\schedule.php?action=schedule">
                            <i class="fas fa-calendar-alt"></i> Schedule
                        </a>
                    </li>
                </ul>
            </nav>
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

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Portal Jemari 5.0 PaskerID</title>
    <style>
        /* === NAVBAR === */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }

        .main-header {
            background: #1f2a38;
            color: white;
            padding: 15px 30px;
            position: relative;
        }

        .title-container {
            text-align: center;
            margin-bottom: 10px;
        }

        .title-container h1 {
            margin: 0;
        }

        .title-container p {
            margin: 0;
            font-size: 14px;
            color: #d1d1d1;
        }

        .main-nav {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
        }

        /* tombol navigasi */
        .main-nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 12px;
        }

        .main-nav ul li a {
            display: inline-block;
            padding: 8px 15px;
            background: #4da6ff;       /* biru muda */
            color: white;
            font-weight: bold;
            border-radius: 6px;
            text-decoration: none;
            transition: 0.2s;
        }

        .main-nav ul li a:hover {
            background: #3399ff;       /* hover lebih gelap */
        }

        /* === LOGIN LOGOUT BUTTONS === */
        .auth-buttons {
            position: absolute;
            top: 15px;
            right: 20px;
            display: flex;
            gap: 8px;
        }

        .auth-buttons a {
            padding: 5px 10px;       /* perkecil tombol */
            font-size: 13px;
            border-radius: 5px;
            font-weight: bold;
            text-decoration: none;
        }

        .btn-admin {
            background: #f1c40f; 
            color: #222;
        }

        .btn-logout {
            background: #e74c3c; 
            color: white;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="title-container">
            <h1>Portal Jemari 5.0 PaskerID</h1>
            <p>Sistem Informasi Kemitraan Strategis Indonesia</p>
        </div>
        <nav class="main-nav">
            <?php if (isset($_SESSION['user'])): ?>
                <ul>
                    <li><a href="index.php?action=tahapan">🤝 Tahapan Kerjasama</a></li>
                    <li><a href="index.php?action=kontak">📞 Kontak Mitra</a></li>
                    <li><a href="index.php?action=file_kerjasama">📄 File Kerjasama</a></li>
                    <li><a href="index.php?action=file_lainnya">📂 File Lainnya</a></li>
                    <li><a href="index.php?action=jadwal">📅 Jadwal Kegiatan</a></li>
                </ul>
                <div class="auth-buttons">
                    <a href="auth.php?action=admin_login" class="btn-admin">Login</a>
                    <a href="auth.php?action=logout" class="btn-logout">
                        Logout (<?= htmlspecialchars($_SESSION['user']['username']) ?>)
                    </a>
                </div>
            <?php else: ?>
                <div class="auth-buttons">
                    <a href="auth.php?action=admin_login" class="btn-admin">Login</a>
                </div>
            <?php endif; ?>
        </nav>
    </header>
    <main>
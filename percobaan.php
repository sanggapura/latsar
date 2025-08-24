<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// kalau belum login admin, redirect
if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: loginadm.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin — Jemari 5.0 PaskerID</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f9fc;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        header {
            background: #1d71b8;
            color: #fff;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header h1 {
            margin: 0;
        }
        header a.logout {
            background: #ff4d4d;
            color: #fff;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 6px;
        }
        main {
            margin: 40px auto;
            max-width: 900px;
        }
        .marquee-container {
            width: 100%;
            overflow: hidden;
            white-space: nowrap;
            box-sizing: border-box;
        }
        .marquee-text {
            display: inline-block;
            padding-left: 100%;
            animation: marquee 12s linear infinite;
            font-size: 22px;
            font-weight: bold;
            color: #1d71b8;
        }
        @keyframes marquee {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-100%); }
        }
    </style>
</head>
<body>
    <header>
        <h1>Portal Jemari 5.0 PaskerID</h1>
        <a href="auth.php?action=logout" class="logout">Logout</a>
    </header>

    <main>
        <div class="marquee-container">
            <div class="marquee-text">
                Selamat Datang di Dasboard Admin Jemari 5.0 PaskerID
            </div>
        </div>
    </main>

    <footer>
        &copy; <?= date('Y'); ?> Jemari 5.0 PaskerID
    </footer>
</body>
</html>

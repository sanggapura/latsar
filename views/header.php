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
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="main-header">
        <div class="title-container">
            <h1>Portal Jemari 5.0 PaskerID</h1>
            <p>Sistem Informasi Kemitraan Strategis Indonesia</p>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php?action=tahapan">🤝 Tahapan Kerjasama</a></li>
                <li><a href="index.php?action=kontak">📞 Kontak Mitra</a></li>
                <li><a href="index.php?action=file_kerjasama">📄 File Kerjasama</a></li>
                <li><a href="index.php?action=file_lainnya">📂 File Lainnya</a></li>
            </ul>
            <div class="auth-buttons">
                <?php if (isset($_SESSION['user'])): ?>
                    <a href="auth.php?action=logout" class="btn-logout">Logout (<?= $_SESSION['user']['username'] ?>)</a>
                <?php else: ?>
                    <a href="auth.php?action=login" class="btn-login">Login</a>
                <?php endif; ?>
                <a href="auth.php?action=admin_login" class="btn-admin">Login Admin</a>
            </div>
        </nav>
    </header>
    <main>

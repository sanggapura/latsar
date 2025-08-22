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
            <?php if (isset($_SESSION['user'])): ?>
                <ul>
                    <li><a href="index.php">🏠 Dashboard</a></li>
                    <li><a href="index.php?action=partners">🤝 Partner</a></li>
                    <li><a href="index.php?action=create">➕ Tambah Partner</a></li>
                </ul>
                <div class="auth-buttons">
                    <a href="auth.php?action=logout" class="btn-logout">Logout (<?= htmlspecialchars($_SESSION['user']['username'] ?? $_SESSION['user']['email'] ?? 'user') ?>)</a>
                </div>
            <?php else: ?>
                <div class="auth-buttons">
                    <a href="auth.php?action=login_form" class="btn-login">Login</a>
                </div>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <?php if (!empty($_SESSION['flash'])): ?>
            <p class="<?= htmlspecialchars($_SESSION['flash']['type']) ?>" style="margin: 15px;">
                <?= htmlspecialchars($_SESSION['flash']['message']) ?>
            </p>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>
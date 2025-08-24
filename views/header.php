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
    <style>
        .auth-buttons {
            position: absolute;
            top: 15px;
            right: 20px;
        }
        .auth-buttons a {
            margin-left: 10px;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            color: #fff;
            font-weight: bold;
        }
        .btn-logout { background: #e74c3c; }
        .btn-admin { background: #2ecc71; }
        .btn-login { background: #3498db; }
        .main-header { position: relative; padding: 15px; }

        /* Menu utama */
        .main-nav ul { list-style: none; margin: 0; padding: 0; }
        .main-nav ul li { position: relative; display: inline-block; margin-right: 5px; }
        .main-nav ul li a {
            display: block;
            padding: 8px 14px;
            text-decoration: none;
            background: orange;        /* kotak orange */
            color: #003366;            /* teks biru tua */
            border-radius: 4px;
            font-weight: bold;
        }
        /* Hover menu utama */
        .main-nav ul li a:hover {
            background: #3399ff;       /* biru muda */
            color: #fff;               /* teks putih */
        }

        /* Dropdown */
        .main-nav ul li ul {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: #f9f9f9;
            border: 1px solid #ddd;
            min-width: 200px;
            z-index: 1000;
            text-align: left;          /* rata kiri */
        }
        .main-nav ul li ul li {
            display: block;
        }
        .main-nav ul li ul li a {
            padding: 10px 16px;        /* padding lebih besar */
            background: #f9f9f9;
            color: #000;               /* teks hitam */
            border-radius: 0;
            text-align: left;          /* teks rata kiri */
        }
        .main-nav ul li ul li a:hover {
            background: #ddd;          /* abu-abu */
            color: #000;               /* teks tetap hitam */
        }
        .main-nav ul li:hover ul {
            display: block;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="title-container">
            <h1>Portal Jemari 5.0 PaskerID</h1>
            <p>Sistem Informasi Substansi Jejaring Kemitraan Pusat Pasar Kerja</p>
        </div>
        <nav class="main-nav">
            <?php if (isset($_SESSION['user'])): ?>
                <ul>
                    <li>
                        <a href="index.php?action=stages">📑 Tahapan Kerjasama ▾</a>
                        <ul>
                            <li><a href="index.php?action=stages&type=kementerian">Kementerian/Lembaga</a></li>
                            <li><a href="index.php?action=stages&type=daerah">Pemerintah Daerah</a></li>
                            <li><a href="index.php?action=stages&type=mitra">Mitra Pembangunan</a></li>
                            <li><a href="index.php?action=stages&type=swasta">Swasta/Perusahaan</a></li>
                        </ul>
                    </li>
                    <li><a href="kontak.php?action=contacts">📞 Kontak Mitra</a></li>
                    <li><a href="index.php?action=documents">📂 File Dokumen</a></li>
                    <li><a href="index.php?action=partners">🤝 Partner</a></li>
                    <li><a href="index.php?action=create">➕ Tambah Partner</a></li>
                    <li><a href="index.php?action=schedule">🗓️ Schedule</a></li> <!-- ✅ Tambahan menu baru -->
                </ul>
                <div class="auth-buttons">
                    <a href="loginadm.php?action=admin_login" class="btn-admin">Login Admin</a>
                    <a href="auth.php?action=logout" class="btn-logout">
                        Logout (<?= htmlspecialchars($_SESSION['user']['username'] ?? $_SESSION['user']['email'] ?? 'user') ?>)
                    </a>
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

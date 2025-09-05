<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale-1.0">
    <title>Portal Jemari 5.0 PaskerID</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --bs-blue-dark: #0a3d62;
            --bs-blue-light: #3c6382;
            --bs-orange: #f39c12;
            --bs-gray: #f5f7fa;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bs-gray);
        }
        .main-header {
            background: linear-gradient(135deg, var(--bs-blue-dark) 0%, var(--bs-blue-light) 100%);
            color: white;
            padding: 2rem 1rem;
            text-align: center;
        }
        .main-header h1 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .main-header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }
        .main-nav {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 0.75rem;
            margin-top: 1.5rem;
            border-radius: 50px;
            display: inline-block;
        }
        .main-nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 0.5rem;
        }
        .main-nav a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            color: white;
            background-color: transparent;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .main-nav a:hover, .main-nav a.active {
            background-color: var(--bs-orange);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        /* Style untuk halaman selain yang memiliki form card */
        .content-outside-form {
             background-color: white;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            margin: -2rem 1rem 2rem 1rem; /* Margin negatif untuk efek tumpang tindih */
            position: relative;
            z-index: 2;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <h1><i class="bi bi-handshake"></i> Portal Jejaring Kemitraan PaskerID</h1>
        <p>Sistem Informasi Substansi Jejaring Kemitraan Pusat Pasar Kerja</p>
        <nav class="main-nav">
            <ul>
                <li><a href="/latsar/admin/tahapan/index.php"><i class="bi bi-diagram-3-fill"></i> Tahapan Kerjasama</a></li>
                <li><a href="/latsar/admin/kontak/daftar_kontak.php"><i class="bi bi-person-rolodex"></i> Kontak Mitra</a></li>
                <li><a href="/latsar/admin/dokumen/dokumen_index.php"><i class="bi bi-folder-fill"></i> File Dokumen</a></li>
                <li><a href="/latsar/admin/schedule/schedule.php"><i class="bi bi-calendar-event-fill"></i> Schedule</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php if (!empty($_SESSION['flash'])): ?>
            <div class="container pt-3">
                 <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?> alert-dismissible fade show">
                    <?= htmlspecialchars($_SESSION['flash']['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>
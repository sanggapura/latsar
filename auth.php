<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Selamat Datang</title>
    <link rel="stylesheet" href="assets/style.css"> <!-- kalau ada CSS -->
</head>
<body>
    <?php include __DIR__ . '/views/header.php'; ?>

    <main style="padding:20px;text-align:center;">
        <h1>Halo, Selamat Datang!</h1>
        <p>Terima kasih sudah berkunjung ke website kami.</p>
    </main>
</body>
</html>

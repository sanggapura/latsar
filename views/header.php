<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Partner Cooperation</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
    <h1>Partner Cooperation Website</h1>
    <nav>
        <a href="index.php">Home</a> |
        <a href="index.php?action=create">Tambah Partner</a> |
        <?php if (isset($_SESSION['user'])): ?>
            <a href="auth.php?action=logout">Logout (<?= $_SESSION['user']['username'] ?>)</a>
        <?php else: ?>
            <a href="auth.php?action=login">Login</a>
        <?php endif; ?>
    </nav>
</header>
    <main>

<?php include __DIR__ . "/header.php"; ?>
<h2>Dashboard</h2>
<p>Selamat datang, <b><?= $_SESSION['user']['username'] ?></b>!</p>

<div class="menu-dashboard">
    <ul>
        <li><a href="index.php?action=partners">📌 Daftar Partner</a></li>
        <li><a href="index.php?action=create">➕ Tambah Partner</a></li>
    </ul>
</div>
<?php include __DIR__ . "/footer.php"; ?>

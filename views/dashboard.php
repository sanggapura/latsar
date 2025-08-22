<?php include __DIR__ . "/header.php"; ?>
<h2>Dashboard</h2>
<p>Selamat datang, <b><?= $_SESSION['user']['username'] ?></b>!</p>

<div class="menu-dashboard">
    <ul>
        <li><a href="index.php?action=tahapan">📌 Tahapan Kerja Sama</a></li>
        <li><a href="index.php?action=kontak">📞 Kontak Mitra</a></li>
        <li><a href="index.php?action=file_kerjasama">📂 File Kerja Sama</a></li>
        <li><a href="index.php?action=file_lainnya">📑 File Lainnya</a></li>
    </ul>
</div>
<?php include __DIR__ . "/footer.php"; ?>

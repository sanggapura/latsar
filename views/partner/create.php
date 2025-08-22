<?php include __DIR__ . "/../header.php"; ?>
<h2>Tambah Partner Baru</h2>
<form action="index.php?action=store" method="POST">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <label>Nama:</label><br>
    <input type="text" name="name" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Phone:</label><br>
    <input type="text" name="phone" required><br><br>

    <button type="submit">Simpan</button>
</form>
<?php include __DIR__ . "/../footer.php"; ?>

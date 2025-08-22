<?php include __DIR__ . "/../header.php"; ?>
<h2>Daftar Partner</h2>
<?php // flash is already shown in header when included ?>
<p><a href="index.php?action=create">+ Tambah Partner</a></p>
<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>ID</th><th>Nama</th><th>Email</th><th>Phone</th><th>Aksi</th>
    </tr>
    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td>
                <a href="index.php?action=edit&id=<?= (int)$row['id'] ?>">Edit</a> |
                <form action="index.php?action=delete" method="POST" style="display:inline" onsubmit="return confirm('Hapus data ini?')">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                    <button type="submit" style="background:none;border:none;color:#c00;cursor:pointer">Hapus</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
<?php include __DIR__ . "/../footer.php"; ?>

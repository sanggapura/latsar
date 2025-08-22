<?php include __DIR__ . "/../header.php"; ?>
<h2>Daftar Partner</h2>
<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>ID</th><th>Nama</th><th>Email</th><th>Phone</th><th>Aksi</th>
    </tr>
    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['name'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= $row['phone'] ?></td>
            <td>
                <a href="index.php?action=edit&id=<?= $row['id'] ?>">Edit</a> |
                <a href="index.php?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Hapus data ini?')">Hapus</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
<?php include __DIR__ . "/../footer.php"; ?>

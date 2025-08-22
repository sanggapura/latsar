<?php include __DIR__ . "/../header.php"; ?>
<h2>Edit Partner</h2>
<form action="index.php?action=update" method="POST">
    <input type="hidden" name="id" value="<?= $partner['id'] ?>">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

    <label>Nama:</label><br>
    <input type="text" name="name" value="<?= $partner['name'] ?>" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?= $partner['email'] ?>" required><br><br>

    <label>Phone:</label><br>
    <input type="text" name="phone" value="<?= $partner['phone'] ?>" required><br><br>

    <button type="submit">Update</button>
</form>
<?php include __DIR__ . "/../footer.php"; ?>

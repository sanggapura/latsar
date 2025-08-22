<?php include __DIR__ . "/../header.php"; ?>
<h2>Login</h2>
<?php // flash is already shown in header when included ?>
<form action="auth.php?action=login" method="POST">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>
</form>
<p>Belum punya akun? <a href="auth.php?action=register_form">Daftar di sini</a></p>
<?php include __DIR__ . "/../footer.php"; ?>

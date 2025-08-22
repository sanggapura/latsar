<?php include __DIR__ . "/../header.php"; ?>
<h2>Register</h2>
<?php // flash is already shown in header when included ?>
<form action="auth.php?action=register" method="POST">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Register</button>
</form>
<p>Sudah punya akun? <a href="auth.php?action=login_form">Login disini</a></p>
<?php include __DIR__ . "/../footer.php"; ?>

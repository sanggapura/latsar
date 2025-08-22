<?php include __DIR__ . "/../header.php"; ?>
<h2>Register</h2>
<form action="auth.php?action=register" method="POST">
    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Register</button>
</form>
<p>Sudah punya akun? <a href="auth.php?action=login">Login disini</a></p>
<?php include __DIR__ . "/../footer.php"; ?>

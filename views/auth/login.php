<?php include __DIR__ . "/../header.php"; ?>
<h2>Login</h2>
<form action="auth.php?action=login" method="POST">
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>
</form>
<?php include __DIR__ . "/../footer.php"; ?>

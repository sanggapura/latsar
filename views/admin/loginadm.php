<?php include __DIR__ . "/../header.php"; ?>

<div class="login-page">
    <div class="login-container">
        <!-- Form login -->
        <div class="login-form">
            <h2>Log in Admin</h2>

            <form action="auth.php?action=login_admin" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                <!-- Input Email -->
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email Admin" required>
                </div>

                <!-- Input Password -->
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <!-- Tombol login -->
                <button type="submit">Log in</button>
            </form>

            <!-- Link kembali -->
            <div class="signup-link">
                <a href="auth.php?action=login_form">← Kembali ke Login User</a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../footer.php"; ?>

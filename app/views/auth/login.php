<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>

<div class="container">
    <h2>Login</h2>

    <?php if (!empty($success)): ?>
        <div class="success-msg"><?= $success; ?></div>
    <?php endif; ?>

    <?php if (!empty($errors['form'])): ?>
        <div class="error-box"><?= $errors['form']; ?></div>
    <?php endif; ?>

    <form id="loginForm" method="POST" action="/login">

        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="Enter your email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            <?php if (!empty($errors['email'])): ?><small><?= $errors['email']; ?></small><?php endif; ?>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter your password" required>
            <?php if (!empty($errors['password'])): ?><small><?= $errors['password']; ?></small><?php endif; ?>
        </div>

        <button type="submit" class="btn">Login</button>

        <div class="link">
            <a href="/forgot">Forgot Password?</a>
        </div>

        <div class="link">
            Don't have an account? <a href="/register">Register</a>
        </div>

    </form>
</div>

<script src="/assets/script.js"></script>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>

<div class="container">
    <h2>Register</h2>

    <form id="registerForm" method="POST" action="/register">

        <div class="input-group">
            <label>Name</label>
            <input type="text" name="name" placeholder="Enter your name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
            <?php if (!empty($errors['name'])): ?><small><?= $errors['name']; ?></small><?php endif; ?>
        </div>

        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="Enter your email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            <?php if (!empty($errors['email'])): ?><small><?= $errors['email']; ?></small><?php endif; ?>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter your password" required minlength="6">
            <?php if (!empty($errors['password'])): ?><small><?= $errors['password']; ?></small><?php endif; ?>
        </div>

        <div class="input-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" placeholder="Confirm password" required minlength="6">
            <?php if (!empty($errors['confirm_password'])): ?><small><?= $errors['confirm_password']; ?></small><?php endif; ?>
        </div>

        <button type="submit" class="btn">Register</button>

        <div class="link">
            Already have an account? <a href="/login">Login</a>
        </div>

    </form>
</div>

<script src="/assets/script.js"></script>

</body>
</html>

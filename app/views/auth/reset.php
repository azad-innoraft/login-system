<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>

<body>

    <div class="container">
        <h2>Reset Password</h2>

        <?php if (!empty($message['error'])): ?>
            <div class="error-box"><?= $message['error']; ?></div>
            <div class="link"><a href="/forgot">Request a new reset link</a></div>
        <?php else: ?>
            <form id="resetForm" method="POST" action="/reset?token=<?= urlencode($token); ?>">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token); ?>">

                <div class="input-group">
                    <label>New Password</label>
                    <input type="password" name="password" placeholder="Enter new password" required minlength="6">
                    <?php if (!empty($errors['password'])): ?><small><?= $errors['password']; ?></small><?php endif; ?>
                </div>

                <div class="input-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" placeholder="Confirm new password" required minlength="6">
                    <?php if (!empty($errors['confirm_password'])): ?><small><?= $errors['confirm_password']; ?></small><?php endif; ?>
                </div>

                <button type="submit" class="btn">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>

    <script src="/assets/script.js"></script>

</body>

</html>

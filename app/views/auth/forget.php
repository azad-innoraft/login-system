<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>

<div class="container">
    <h2>Forgot Password</h2>

    <!-- Success / Error Messages -->
    <?php if (!empty($message['success'])): ?>
        <div class="success-msg">
            <?= $message['success']; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($message['error'])): ?>
        <div class="error-box">
            <?= $message['error']; ?>
        </div>
    <?php endif; ?>

    <form id="forgotForm" method="POST" action="/forgot">

        <div class="input-group">
            <label>Email Address</label>
            <input 
                type="email" 
                name="email" 
                placeholder="Enter your registered email"
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                required
            >
        </div>

        <button type="submit" class="btn">Send Reset Link</button>

        <div class="link">
            Remember your password? <a href="/login">Login</a>
        </div>

    </form>
</div>

<script src="/assets/script.js"></script>

</body>
</html>

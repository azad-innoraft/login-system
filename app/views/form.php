<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Information Form</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>

<div class="container form-container">
    <div class="topbar">
        <h2>User Information</h2>
        <a href="/logout">Logout</a>
    </div>

    <form id="detailsForm" method="POST" action="/pdf" enctype="multipart/form-data">
        <div class="input-row">
            <div class="input-group">
                <label>First Name</label>
                <input type="text" name="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
                <?php if (!empty($errors['first_name'])): ?><small><?= $errors['first_name']; ?></small><?php endif; ?>
            </div>

            <div class="input-group">
                <label>Last Name</label>
                <input type="text" name="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
                <?php if (!empty($errors['last_name'])): ?><small><?= $errors['last_name']; ?></small><?php endif; ?>
            </div>
        </div>

        <div class="input-row">
            <div class="input-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                <?php if (!empty($errors['phone'])): ?><small><?= $errors['phone']; ?></small><?php endif; ?>
            </div>

            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                <?php if (!empty($errors['email'])): ?><small><?= $errors['email']; ?></small><?php endif; ?>
            </div>
        </div>

        <div class="input-group">
            <label>Marks Details</label>
            <textarea name="marks" placeholder="English|80&#10;Math|90" required><?= htmlspecialchars($_POST['marks'] ?? '') ?></textarea>
            <?php if (!empty($errors['marks'])): ?><small><?= $errors['marks']; ?></small><?php endif; ?>
        </div>

        <div class="input-group">
            <label>Upload Image</label>
            <input type="file" name="image" accept="image/png,image/jpeg,image/jpg" required>
            <?php if (!empty($errors['image'])): ?><small><?= $errors['image']; ?></small><?php endif; ?>
        </div>

        <button type="submit" class="btn">Download DOCX</button>
    </form>
</div>

<script src="/assets/script.js"></script>

</body>
</html>

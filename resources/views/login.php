<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if(session('error')): ?>
        <p style="color:red"><?= session('error') ?></p>
    <?php endif; ?>
    <form method="POST" action="/login">
        <?= csrf_field() ?>
        <label>Email:</label>
        <input type="email" name="email" required><br><br>

        <label>Password:</label>
        <input type="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>

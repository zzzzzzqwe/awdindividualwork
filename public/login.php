<?php
session_start();
require_once '../config/config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = 'Email и пароль обязательны.';
    } else {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT id, username, email, role, password_hash FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = 'Неверный логин или пароль.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

<div class="login-container">
    <h2>Вход в систему</h2>

    <?php if ($errors): ?>
        <div class="error-box">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" class="login-form">
        <input type="email" name="email" placeholder="Email" required>
        <div class="password-wrapper">
            <input type="password" name="password" id="password" placeholder="Пароль" required>
            <button type="button" id="togglePassword">🙈</button>
        </div>
        <button type="submit">Войти</button>
        <a href="register.php">Нет аккаунта? Зарегистрироваться</a>

    </form>
</div>

<script src="js/login.js"></script>
</body>
</html>

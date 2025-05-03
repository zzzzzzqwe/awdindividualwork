<?php
session_start();
require_once '../config/config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        $errors[] = 'Все поля обязательны для заполнения.';
    }

    if (strlen($username) < 3) {
        $errors[] = 'Имя пользователя должно быть от 3 символов.';
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Некорректный email.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Пароль должен быть от 6 символов.';
    }
    
    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $pdo = getPDO();
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        try {
            $stmt->execute([$username, $email, $hashed]);

            $_SESSION['user'] = $username;
            $_SESSION['role'] = 'user';

            header('Location: dashboard.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Ошибка при регистрации: возможно, email уже используется.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/register.css">
</head>
<body>

<div class="register-container">
    <h2>Регистрация</h2>

    <?php if ($errors): ?>
        <div class="error-box">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" class="register-form">
        <input type="text" name="username" placeholder="Имя пользователя" required>
        <input type="email" name="email" placeholder="Email" required>
        <div class="password-wrapper">
            <input type="password" name="password" id="reg-password" placeholder="Пароль" required>
            <button type="button" id="toggleRegPassword">🙉</button>
        </div>
        <button type="submit">Зарегистрироваться</button>
    </form>
</div>

<script src="js/register.js"></script>
</body>
</html>

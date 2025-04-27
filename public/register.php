<?php
session_start();
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $errors = [];

    if ($username === '' || $email === '' || $password === '') {
        $errors[] = 'Все поля обязательны для заполнения.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Некорректный email.';
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
            $errors[] = 'Ошибка: возможно, такой email уже зарегистрирован.';
        }
    }
}
?>

<form method="post">
    <input type="text" name="username" placeholder="Имя пользователя" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Пароль" required><br>
    <button type="submit">Зарегистрироваться</button>
</form>
<br>
<form action="login.php" method="get">
    <button type="submit">Войти</button>
</form>


<?php
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p style='color:red;'>$error</p>";
    }
}
?>

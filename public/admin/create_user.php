<?php
require_once '../../auth/check_admin.php';
require_once '../../config/config.php';

$pdo = getPDO();
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    
    if (strlen($username) < 3) $errors[] = 'Имя пользователя должно быть от 3 символов.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Некорректный email.';
    if (strlen($password) < 4) $errors[] = 'Пароль должен быть от 4 символов.';
    if (!in_array($role, ['user', 'admin'])) $errors[] = 'Недопустимая роль.';

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) $errors[] = 'Такой пользователь уже существует.';

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $hash, $role]);
        // $success = true;
        header('Location: users.php');
        exit;

    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Создание пользователя</title>
</head>
<body>

<h2>Создать нового пользователя</h2>
<a href="users.php">← Назад к списку</a><br><br>

<?php if (!empty($errors)): ?>
    <div style="color:red;">
        <?php foreach ($errors as $e): ?>
            <p><?= htmlspecialchars($e) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div style="color:green;">Пользователь успешно создан.</div>
<?php endif; ?>

<form method="post">
    <label>Имя пользователя:<br>
        <input type="text" name="username" required>
    </label><br><br>

    <label>Email:<br>
        <input type="email" name="email" required>
    </label><br><br>

    <label>Пароль:<br>
        <input type="password" name="password" required>
    </label><br><br>

    <label>Роль:
        <select name="role">
            <option value="user">Обычный пользователь</option>
            <option value="admin">Администратор</option>
        </select>
    </label><br><br>

    <button type="submit">Создать</button>
</form>

</body>
</html>

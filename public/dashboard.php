<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['user'] ?? 'Гость';
$role = $_SESSION['role'] ?? 'user';
$isAdmin = $role === 'admin';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель пользователя</title>
</head>
<body>

<h1>Добро пожаловать, <?= htmlspecialchars($username) ?>!</h1>
<p>Ваша роль: <?= htmlspecialchars($role) ?></p>

<a href="logout.php">Выйти</a><br><br>

<a href="content.php">Перейти к записям</a><br><br>

<?php if ($isAdmin): ?>
    <hr>
    <h2>Админ-панель</h2>
    <ul>
        <li><a href="admin/users.php">Управление пользователями</a></li>
        <li><a href="content.php">Управление контентом</a></li>
    </ul>
<?php endif; ?>

</body>
</html>

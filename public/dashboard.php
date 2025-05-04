<?php
/**
 * Панель пользователя (dashboard).
 *
 * Показывает персонализированное приветствие, роль пользователя, 
 * а также предоставляет доступ к административным функциям (если пользователь — админ).
 *
 * Использует сессии для контроля доступа и отображения персонального контента.
 *
 * PHP version 8.4.4
 * 
 * @author Dmitrii
 * @author Stanislav
 */
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<header>
    <h1>Личный кабинет</h1>
    <nav>
        <a href="logout.php">Выйти</a>
    </nav>
</header>

<div class="container">
    <p><strong>Добро пожаловать, <?= htmlspecialchars($username) ?>!</strong></p>
    <p>Ваша роль: <em><?= htmlspecialchars($role) ?></em></p>

    <a class="button" href="content.php">Перейти к записям</a>

    <?php if ($isAdmin): ?>
    <div class="admin-panel">
        <h2>Админ-панель</h2>
        <a class="button" href="admin/users.php">Управление пользователями</a>
        <a class="button" href="content.php">Управление контентом</a>
    </div>
    <?php endif; ?>
</div>

</body>
</html>

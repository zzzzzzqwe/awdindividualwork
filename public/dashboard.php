<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

echo "<h1>Добро пожаловать, {$_SESSION['user']}!</h1>";
echo "<p>Ваша роль: {$_SESSION['role']}</p>";
echo "<a href='logout.php'>Выйти</a>";

if ($_SESSION['role'] === 'admin') {
    echo "<hr><h2>Админ-панель</h2>";
    echo "<ul>
            <li><a href='admin/users.php'>Управление пользователями</a></li>
            <li><a href='admin/content.php'>Управление контентом</a></li>
          </ul>";
}
?>

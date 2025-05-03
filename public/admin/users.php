<?php

require_once '../../auth/check_admin.php';
require_once '../../config/config.php';

$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = (int)($_POST['user_id'] ?? 0);

    if ($userId > 0) {
        if ($action === 'make_admin') {
            $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
            $stmt->execute([$userId]);
        } elseif ($action === 'make_user') {
            $stmt = $pdo->prepare("UPDATE users SET role = 'user' WHERE id = ?");
            $stmt->execute([$userId]);
        } elseif ($action === 'delete') {
            if ($_SESSION['user'] !== getUsernameById($userId, $pdo)) {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$userId]);
            }
        }
    }
    header('Location: users.php');
    exit;
}


$stmt = $pdo->query("SELECT id, username, email, role, created_at FROM users ORDER BY id ASC");
$users = $stmt->fetchAll();

function getUsernameById($id, $pdo) {
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row['username'] ?? null;
}
?>

<h2>Управление пользователями</h2>
<link rel="stylesheet" href="../css/users.css">
<script src="../js/users.js"></script>
<a href="../dashboard.php">← Назад</a><br><br>
<a href="create_user.php">+ Добавить пользователя</a><br><br>

<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Имя пользователя</th>
        <th>Email</th>
        <th>Роль</th>
        <th>Дата регистрации</th>
        <th>Действия</th>
    </tr>
    <?php foreach ($users as $user): ?>
    <tr>
        <td><?= htmlspecialchars($user['id']) ?></td>
        <td><?= htmlspecialchars($user['username']) ?></td>
        <td><?= htmlspecialchars($user['email']) ?></td>
        <td><?= htmlspecialchars($user['role']) ?></td>
        <td><?= date('Y-m-d H:i:s', strtotime($user['created_at'])) ?></td>
        <td>
            <?php if ($user['role'] !== 'admin'): ?>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <input type="hidden" name="action" value="make_admin">
                    <button type="submit">Сделать админом</button>
                </form>
            <?php else: ?>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <input type="hidden" name="action" value="make_user">
                    <button type="submit">Сделать пользователем</button>
                </form>
            <?php endif; ?>
            <form method="post" style="display:inline;" onsubmit="return confirm('Удалить пользователя?');">
                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                <input type="hidden" name="action" value="delete">
                <button type="submit">Удалить</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>


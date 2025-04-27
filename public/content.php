<?php
require_once '../auth/check_auth.php';
require_once '../config/config.php';

$pdo = getPDO();


$isAdmin = ($_SESSION['role'] ?? '') === 'admin';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAdmin) {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $title = trim($_POST['title'] ?? '');
        $body = trim($_POST['body'] ?? '');

        if ($title !== '' && $body !== '') {
            $stmt = $pdo->prepare("INSERT INTO content (title, body) VALUES (?, ?)");
            $stmt->execute([$title, $body]);
        }
    } elseif ($action === 'delete') {
        $contentId = (int)($_POST['content_id'] ?? 0);

        if ($contentId > 0) {
            $stmt = $pdo->prepare("DELETE FROM content WHERE id = ?");
            $stmt->execute([$contentId]);
        }
    }
    header('Location: content.php');
    exit;
}

$stmt = $pdo->query("SELECT id, title, body, created_at FROM content ORDER BY created_at DESC");
$contents = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Просмотр контента</title>
</head>
<body>

<h2>Просмотр контента</h2>
<a href="../dashboard.php">← Назад в панель</a><br><br>

<?php if ($isAdmin): ?>
    <h3>Добавить новую запись</h3>
    <form method="post">
        <input type="hidden" name="action" value="add">
        <input type="text" name="title" placeholder="Заголовок" required><br><br>
        <textarea name="body" placeholder="Текст" rows="5" cols="50" required></textarea><br><br>
        <button type="submit">Добавить</button>
    </form>

    <hr>
<?php endif; ?>

<h3>Список записей</h3>

<?php if (empty($contents)): ?>
    <p>Нет записей в базе данных.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Заголовок</th>
                <th>Текст</th>
                <th>Дата создания</th>
                <?php if ($isAdmin): ?>
                    <th>Действия</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($contents as $content): ?>
            <tr>
                <td><?= htmlspecialchars($content['id']) ?></td>
                <td><?= htmlspecialchars($content['title']) ?></td>
                <td><?= nl2br(htmlspecialchars($content['body'])) ?></td>
                <td><?= htmlspecialchars($content['created_at']) ?></td>
                <?php if ($isAdmin): ?>
                    <td>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Удалить запись?');">
                            <input type="hidden" name="content_id" value="<?= $content['id'] ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit">Удалить</button>
                        </form>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>

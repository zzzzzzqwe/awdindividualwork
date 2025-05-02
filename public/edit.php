<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../auth/check_admin.php';
require_once '../config/config.php';

$pdo = getPDO();
$errors = [];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Неверный ID записи.";
    exit;
}

$id = (int)$_GET['id'];

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $body = trim($_POST['body']);
    $category = $_POST['category'] ?? '';
    $is_public = ($_POST['is_public'] ?? '') === 'yes';
    $author = trim($_POST['author']);

    if ($title === '' || $body === '' || $category === '' || $author === '') {
        $errors[] = 'Все поля обязательны.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE content SET title = ?, body = ?, category = ?, is_public = ?, author = ? WHERE id = ?");
        $stmt->execute([$title, $body, $category, $is_public ? 'true' : 'false', $author, $id]);
        header('Location: content.php');
        exit;
    }
}

// Загрузка данных записи
$stmt = $pdo->prepare("SELECT * FROM content WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    echo "Запись не найдена.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать запись</title>
</head>
<body>

<h2>Редактировать запись ID <?= $id ?></h2>
<a href="content.php">← Назад к записям</a><br><br>

<?php if (!empty($errors)): ?>
    <div style="color:red;">
        <?php foreach ($errors as $err): ?>
            <p><?= htmlspecialchars($err) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post">
    <label>Заголовок:<br>
        <input type="text" name="title" value="<?= htmlspecialchars($data['title']) ?>" required>
    </label><br><br>

    <label>Содержимое:<br>
        <textarea name="body" rows="5" cols="60" required><?= htmlspecialchars($data['body']) ?></textarea>
    </label><br><br>

    <label>Категория:
        <select name="category" required>
            <?php foreach (['Новости', 'Объявления', 'Статьи'] as $cat): ?>
                <option value="<?= $cat ?>" <?= $data['category'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
            <?php endforeach; ?>
        </select>
    </label><br><br>

    <label>Публично?<br>
        <input type="radio" name="is_public" value="yes" <?= $data['is_public'] ? 'checked' : '' ?>> Да
        <input type="radio" name="is_public" value="no" <?= !$data['is_public'] ? 'checked' : '' ?>> Нет
    </label><br><br>

    <label>Автор:<br>
        <input type="text" name="author" value="<?= htmlspecialchars($data['author']) ?>" required>
    </label><br><br>

    <button type="submit">Сохранить</button>
</form>

</body>
</html>

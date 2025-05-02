<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../auth/check_admin.php';
require_once '../config/config.php';

$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
$pdo = getPDO();
$errors = [];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Неверный ID записи.";
    exit;
}

$id = (int)$_GET['id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update' && $isAdmin) {
    $title = trim($_POST['title']);
    $body = trim($_POST['body']);
    $category = $_POST['category'];
    $is_public = ($_POST['is_public'] ?? '') === 'yes';
    $author = trim($_POST['author']);

    if (strlen($title) < 5 || strlen($title) > 100) {
        $errors[] = 'Заголовок должен быть от 5 до 100 символов.';
    }

    if (strlen($body) < 10) {
        $errors[] = 'Содержимое должно быть не короче 10 символов.';
    }

    $allowedCategories = ['Новости', 'Объявления', 'Статьи'];
    if (!in_array($category, $allowedCategories)) {
        $errors[] = 'Выберите допустимую категорию.';
    }

    if (!preg_match('/^[А-Яа-яA-Za-zЁё\s\-]{3,50}$/u', $author)) {
        $errors[] = 'Имя автора должно быть от 3 до 50 букв.';
    }

    if (!in_array($_POST['is_public'] ?? '', ['yes', 'no'])) {
        $errors[] = 'Некорректный флаг публичности.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE content SET title = ?, body = ?, category = ?, is_public = ?, author = ? WHERE id = ?");
        $stmt->execute([$title, $body, $category, $is_public ? 'true' : 'false', $author, $id]);
        header('Location: content.php');
        exit;
    } else {
        // повторно заполняем форму при ошибке
        $editData = [
            'id' => $id,
            'title' => $title,
            'body' => $body,
            'category' => $category,
            'is_public' => $is_public,
            'author' => $author,
        ];
    }
}

if (!isset($editData)) {
    $stmt = $pdo->prepare("SELECT * FROM content WHERE id = ?");
    $stmt->execute([$id]);
    $editData = $stmt->fetch();

    if (!$editData) {
        echo "Запись не найдена.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать запись</title>
</head>
<body>

<h2>Редактировать запись ID <?= $editData['id'] ?></h2>
<a href="content.php">← Назад к записям</a><br><br>

<?php if (!empty($errors)): ?>
    <div style="color:red;">
        <strong>Обнаружены ошибки:</strong>
        <ul>
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="id" value="<?= $editData['id'] ?>">

    <label>Заголовок:<br>
        <input type="text" name="title" value="<?= htmlspecialchars($editData['title']) ?>" required>
    </label><br><br>

    <label>Содержимое:<br>
        <textarea name="body" rows="5" cols="60" required><?= htmlspecialchars($editData['body']) ?></textarea>
    </label><br><br>

    <label>Категория:
        <select name="category" required>
            <?php foreach (['Новости', 'Объявления', 'Статьи'] as $cat): ?>
                <option value="<?= $cat ?>" <?= $editData['category'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
            <?php endforeach; ?>
        </select>
    </label><br><br>

    <label>Публично?<br>
        <input type="radio" name="is_public" value="yes" <?= $editData['is_public'] ? 'checked' : '' ?>> Да
        <input type="radio" name="is_public" value="no" <?= !$editData['is_public'] ? 'checked' : '' ?>> Нет
    </label><br><br>

    <label>Автор:<br>
        <input type="text" name="author" value="<?= htmlspecialchars($editData['author']) ?>" required>
    </label><br><br>

    <button type="submit">Сохранить</button>
</form>

</body>
</html>

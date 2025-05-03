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

// Получение текущей записи
$stmt = $pdo->prepare("SELECT * FROM content WHERE id = ?");
$stmt->execute([$id]);
$content = $stmt->fetch();

if (!$content) {
    echo "Запись не найдена.";
    exit;
}

// Обновление
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
        $errors[] = 'Имя автора должно содержать только буквы и быть от 3 до 50 символов.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE content SET title = ?, body = ?, category = ?, is_public = ?, author = ? WHERE id = ?");
        $stmt->execute([$title, $body, $category, $is_public ? 'true' : 'false', $author, $id]);
        header("Location: content.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать запись</title>
    <link rel="stylesheet" href="css/edit.css">
</head>
<body>

<a href="content.php">← Назад к списку</a>
<h2>Редактирование записи ID <?= $content['id'] ?></h2>

<?php if (!empty($errors)): ?>
    <div class="error">
        <ul>
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="id" value="<?= $content['id'] ?>">

    <label for="title">Заголовок:</label>
    <input type="text" name="title" id="title" value="<?= htmlspecialchars($content['title']) ?>" required>

    <label for="body">Содержимое:</label>
    <textarea name="body" id="body" rows="6" required><?= htmlspecialchars($content['body']) ?></textarea>

    <label for="category">Категория:</label>
    <select name="category" id="category" required>
        <?php foreach (['Новости', 'Объявления', 'Статьи'] as $cat): ?>
            <option value="<?= $cat ?>" <?= $content['category'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
        <?php endforeach; ?>
    </select>

    <label>Публично?</label>
    <br>
    <input type="radio" name="is_public" value="yes" <?= $content['is_public'] ? 'checked' : '' ?>> Да
    <input type="radio" name="is_public" value="no" <?= !$content['is_public'] ? 'checked' : '' ?>> Нет
    <label for="author">Автор:</label>
    <input type="text" name="author" id="author" value="<?= htmlspecialchars($content['author']) ?>" required>

    <button type="submit">Сохранить</button>
</form>

</body>
</html>

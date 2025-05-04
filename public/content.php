<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../auth/check_auth.php';
require_once '../config/config.php';

$pdo = getPDO();
$errors = [];

$role = $_SESSION['role'] ?? 'user';
$isAdmin = $role === 'admin';

$editData = null;

// service: create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add' && $isAdmin) {
    $title = trim($_POST['title'] ?? '');
    $body = trim($_POST['body'] ?? '');
    $category = $_POST['category'] ?? '';
    $is_public = ($_POST['is_public'] ?? '') === 'yes';
    $author = trim($_POST['author'] ?? '');


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

// только буквы, пробелы, дефис, от 3 до 50 символов
if (!preg_match('/^[А-Яа-яA-Za-zЁё\s\-]{3,50}$/u', $author)) {
    $errors[] = 'Имя автора должно содержать только буквы и быть от 3 до 50 символов.';
}

if (!in_array($_POST['is_public'] ?? '', ['yes', 'no'])) {
    $errors[] = 'Некорректный флаг публичности.';
}

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO content (title, body, category, is_public, author) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $body, $category, $is_public ? 'true' : 'false', $author]);
        header('Location: content.php');
        exit;
    }
}

// service: delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete' && $isAdmin) {
    $id = (int)$_POST['content_id'];
    $pdo->prepare("DELETE FROM content WHERE id = ?")->execute([$id]);
    header('Location: content.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit' && $isAdmin) {
    $editId = (int)$_POST['content_id'];
    $stmt = $pdo->prepare("SELECT * FROM content WHERE id = ?");
    $stmt->execute([$editId]);
    $editData = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update' && $isAdmin) {
    $id = (int)$_POST['id'];
    $title = trim($_POST['title']);
    $body = trim($_POST['body']);
    $category = $_POST['category'];
    $is_public = ($_POST['is_public'] ?? '') === 'yes';
    $author = trim($_POST['author']);

    $stmt = $pdo->prepare("UPDATE content SET title = ?, body = ?, category = ?, is_public = ?, author = ? WHERE id = ?");
    $stmt->execute([$title, $body, $category, $is_public ? 'true' : 'false', $author, $id]);
    header('Location: content.php');
    exit;
}

// Поиск
$where = [];
$params = [];

if (!empty($_GET['search_title'])) {
    $where[] = 'title ILIKE ?';
    $params[] = '%' . $_GET['search_title'] . '%';
}
if (!empty($_GET['search_category'])) {
    $where[] = 'category = ?';
    $params[] = $_GET['search_category'];
}
if (!empty($_GET['search_author'])) {
    $where[] = 'author ILIKE ?';
    $params[] = '%' . $_GET['search_author'] . '%';
}
if (!$isAdmin) {
    $where[] = 'is_public = true';
}

$sql = 'SELECT * FROM content';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY created_at DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$contents = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<link rel="stylesheet" href="css/content.css">
    <meta charset="UTF-8">
    <title>Контент</title>
</head>
<body>

<h2>Просмотр записей</h2>
<a href="dashboard.php">← Назад</a><br><br>

<!-- error display -->
<?php if (!empty($errors)): ?>
    <div style="color: red;">
        <strong>Ошибка:</strong>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- search form -->
<h3>Поиск</h3>
<form method="get">
    <input type="text" name="search_title" placeholder="Заголовок" value="<?= htmlspecialchars($_GET['search_title'] ?? '') ?>">
    <input type="text" name="search_author" placeholder="Автор" value="<?= htmlspecialchars($_GET['search_author'] ?? '') ?>">
    <select name="search_category">
        <option value="">-- Категория --</option>
        <option value="Новости" <?= ($_GET['search_category'] ?? '') === 'Новости' ? 'selected' : '' ?>>Новости</option>
        <option value="Объявления" <?= ($_GET['search_category'] ?? '') === 'Объявления' ? 'selected' : '' ?>>Объявления</option>
        <option value="Статьи" <?= ($_GET['search_category'] ?? '') === 'Статьи' ? 'selected' : '' ?>>Статьи</option>
    </select>
    <button type="submit">Найти</button>
</form>
<!-- create form -->
<h3>Список записей</h3>
<?php if (empty($contents)): ?>
    <p>Ничего не найдено.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <!-- <th>ID</th>  !-->
                <th>Заголовок</th>
                <th>Содержимое</th>
                <th>Категория</th>
                <th>Публично</th>
                <th>Автор</th>
                <th>Дата</th>
                <?php if ($isAdmin): ?>
                    <th>Действия</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($contents as $c): ?>
            <tr>
               <!-- <td><?= $c['id'] ?></td> !-->
                <td><?= htmlspecialchars($c['title']) ?></td>
                <td><?= nl2br(htmlspecialchars($c['body'])) ?></td>
                <td><?= htmlspecialchars($c['category']) ?></td>
                <td><?= $c['is_public'] ? 'Да' : 'Нет' ?></td>
                <td><?= htmlspecialchars($c['author']) ?></td>
                <td><?= date('Y-m-d H:i:s', strtotime($c['created_at'])) ?></td>
                <?php if ($isAdmin): ?>
                    <td>
                        <!-- delete -->
                        <form method="post" style="display:inline;" onsubmit="return confirm('Удалить?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="content_id" value="<?= $c['id'] ?>">
                            <button type="submit">Удалить</button>
                        </form>
                        <!-- update link -->
                        <form action="edit.php" method="get" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                         <button type="submit">Редактировать</button>
                            </form>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php if ($isAdmin): ?>
    <h3>Добавить новую запись</h3>
    <form method="post">
        <input type="hidden" name="action" value="add">
        <input type="text" name="title" placeholder="Заголовок" required><br><br>
        <textarea name="body" placeholder="Содержимое" rows="5" cols="50" required></textarea><br><br>
        <label>Категория:
            <select name="category" required>
                <option value="">-- Выберите --</option>
                <option value="Новости">Новости</option>
                <option value="Объявления">Объявления</option>
                <option value="Статьи">Статьи</option>
            </select>
        </label><br><br>
        <label>Публично?
            <input type="radio" name="is_public" value="yes" checked> Да
            <input type="radio" name="is_public" value="no"> Нет
        </label><br><br>
        <input type="text" name="author" placeholder="Автор" required><br><br>
        <button type="submit">Добавить</button>
    </form>
    <hr>
<?php endif; ?>

<hr>


<?php if ($editData): ?>
    <hr>
    <h3>Редактировать запись ID <?= $editData['id'] ?></h3>
    <form method="post">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?= $editData['id'] ?>">
        <input type="text" name="title" value="<?= htmlspecialchars($editData['title']) ?>" required><br><br>
        <textarea name="body" rows="5" cols="50" required><?= htmlspecialchars($editData['body']) ?></textarea><br><br>
        <label>Категория:
            <select name="category" required>
                <?php foreach (['Новости', 'Объявления', 'Статьи'] as $cat): ?>
                    <option value="<?= $cat ?>" <?= $editData['category'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                <?php endforeach; ?>
            </select>
        </label><br><br>
        <label>Публично?
            <input type="radio" name="is_public" value="yes" <?= $editData['is_public'] ? 'checked' : '' ?>> Да
            <input type="radio" name="is_public" value="no" <?= !$editData['is_public'] ? 'checked' : '' ?>> Нет
        </label><br><br>
        <input type="text" name="author" value="<?= htmlspecialchars($editData['author']) ?>" required><br><br>
        <button type="submit">Сохранить</button>
    </form>
<?php endif; ?>

</body>
</html>

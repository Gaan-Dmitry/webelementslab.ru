<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['id'])) {
    header('Location: /pages/login.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $tag = trim($_POST['tag'] ?? '');
    $html = $_POST['html'] ?? '';
    $css = $_POST['css'] ?? '';
    $js = $_POST['js'] ?? '';
    $user_id = $_SESSION['id'];

    if ($name && $html) {
        $stmt = $pdo->prepare('INSERT INTO snippets (name, description, tag, html, css, js, user_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$name, $description, $tag, $html, $css, $js, $user_id]);
        $snippet_id = $pdo->lastInsertId();
        header('Location: /pages/card.php?id=' . $snippet_id);
        exit;
    } else {
        $error = 'Название и HTML обязательны';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/assets/img/favicon.ico" >
    <meta name="theme-color" content="#000000" >
    <meta name="robots" content="noindex, nofollow">
    <title>Создать карточку | WebElementsLab</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<?php require_once __DIR__ . '/../templates/header.php'; ?>
<div class="wrapper">
<main class="block-main">
    <h1>Создать карточку</h1>
    <?php if ($error): ?>
        <div class="form-error" style="color:red;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" class="form-card-create d-flex f-d-column gap1">
        <label>Название*:<br>
            <input type="text" name="name" required maxlength="100" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </label>
        <label>Описание:<br>
            <textarea name="description" maxlength="255"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </label>
        <label>Теги (через запятую):<br>
            <input type="text" name="tag" maxlength="100" value="<?= htmlspecialchars($_POST['tag'] ?? '') ?>">
        </label>
        <label>HTML*:<br>
            <textarea name="html" required rows="8" style="font-family:monospace;"><?= htmlspecialchars($_POST['html'] ?? '') ?></textarea>
        </label>
        <label>CSS:<br>
            <textarea name="css" rows="6" style="font-family:monospace;"><?= htmlspecialchars($_POST['css'] ?? '') ?></textarea>
        </label>
        <label>JS:<br>
            <textarea name="js" rows="6" style="font-family:monospace;"><?= htmlspecialchars($_POST['js'] ?? '') ?></textarea>
        </label>
        <button type="submit" class="reg-btn anim-hover-box-shadow">Создать</button>
    </form>
</main>
</div>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>
</body>
</html> 
<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['id'])) {
    header('Location: /pages/login.php');
    exit;
}

$user_id = $_SESSION['id'];
$upload_dir = __DIR__ . '/../uploads/';

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0775, true);
}

$success = false;

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bio = trim($_POST['bio'] ?? '');
    $vk = trim($_POST['vk'] ?? '');
    $tg = trim($_POST['tg'] ?? '');
    $github = trim($_POST['github'] ?? '');

    $avatar = null;
    $bg_img = null;

    // Аватар
    if (!empty($_FILES['avatar']['tmp_name'])) {
        $avatar_ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $avatar_path = '/uploads/avatar_' . $user_id . '.' . $avatar_ext;
        move_uploaded_file($_FILES['avatar']['tmp_name'], __DIR__ . '/../' . $avatar_path);
        $avatar = $avatar_path;
    }

    // Фон
    if (!empty($_FILES['bg_img']['tmp_name'])) {
        $bg_ext = pathinfo($_FILES['bg_img']['name'], PATHINFO_EXTENSION);
        $bg_path = '/uploads/bg_' . $user_id . '.' . $bg_ext;
        move_uploaded_file($_FILES['bg_img']['tmp_name'], __DIR__ . '/../' . $bg_path);
        $bg_img = $bg_path;
    }

    // Проверка существования профиля
    $stmt = $pdo->prepare("SELECT id FROM user_profiles WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $exists = $stmt->fetch();

    if ($exists) {
        // Обновление
        $query = "UPDATE user_profiles SET ";
        $params = [];

        if ($avatar) {
            $query .= "avatar = ?, ";
            $params[] = $avatar;
        }

        if ($bg_img) {
            $query .= "bg_img = ?, ";
            $params[] = $bg_img;
        }

        $query .= "bio = ?, vk = ?, tg = ?, github = ? WHERE user_id = ?";
        $params[] = $bio;
        $params[] = $vk;
        $params[] = $tg;
        $params[] = $github;
        $params[] = $user_id;

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
    } else {
        // Вставка
        $stmt = $pdo->prepare("INSERT INTO user_profiles (user_id, avatar, bg_img, bio, vk, tg, github) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $user_id,
            $avatar,
            $bg_img,
            $bio,
            $vk,
            $tg,
            $github
        ]);
    }

    $success = true;
}

// Получаем текущие данные профиля
$stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch() ?: [];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Настройки профиля — WebElementsLab</title>
    <link rel="apple-touch-icon" href="/assets/img/logo192.png" >
    <link rel="icon" href="/assets/img/favicon.ico" >
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/pages/settings.css">
</head>
<body>
    <?php require_once __DIR__ . '/../templates/header.php'; ?>
    <div class="wrapper">
        <main>
            <section class="block-main">
                <h1>Настройки профиля</h1>
                <?php if (!empty($success)): ?>
                    <p class="success">Профиль успешно обновлён ✅</p>
                <?php endif; ?>

                <form method="POST" class="profile-settings-form" enctype="multipart/form-data">
                    <label>
                        Аватар:
                        <input type="file" name="avatar" accept="image/*">
                    </label>

                        <?php if (!empty($profile['avatar'])): ?>
                            <img src="<?= htmlspecialchars($profile['avatar']) ?>" alt="avatar" style="max-height:100px">
                        <?php endif; ?>

                    <label>
                        Фон:
                        <input type="file" name="bg_img" accept="image/*">
                    </label>

                    <?php if (!empty($profile['bg_img'])): ?>
                        <img src="<?= htmlspecialchars($profile['bg_img']) ?>" alt="bg" style="max-height:100px">
                    <?php endif; ?>

                    <label>
                        О себе:
                        <textarea name="bio" rows="4"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
                    </label>

                    <label>
                        VK:
                        <input type="text" name="vk" placeholder="username" value="<?= htmlspecialchars($profile['vk'] ?? '') ?>">
                    </label>

                    <label>
                        Telegram:
                        <input type="text" name="tg" placeholder="username" value="<?= htmlspecialchars($profile['tg'] ?? '') ?>">
                    </label>

                    <label>
                        GitHub:
                        <input type="text" name="github" placeholder="username" value="<?= htmlspecialchars($profile['github'] ?? '') ?>">
                    </label>

                    <button type="submit" class="reg-btn anim-hover-box-shadow">Сохранить</button>
                </form>

            </section>
        </main>
    </div>
    <?php require_once __DIR__ . '/../templates/footer.php'; ?>
</body>
</html>

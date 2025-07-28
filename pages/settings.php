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
$error = '';

// Получаем текущие данные профиля
$stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch() ?: [];

$remove_avatar = $_POST['remove_avatar'] ?? '0';
$remove_bg = $_POST['remove_bg'] ?? '0';

$existingAvatar = $profile['avatar'] ?? null;
$existingBg = $profile['bg_img'] ?? null;

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bio = trim($_POST['bio'] ?? '');
    $vk = trim($_POST['vk'] ?? '');
    $tg = trim($_POST['tg'] ?? '');
    $github = trim($_POST['github'] ?? '');

    $avatar = $existingAvatar;
    $bg_img = $existingBg;

    if ($remove_avatar === '1' && $existingAvatar && file_exists(__DIR__ . '/../' . $existingAvatar)) {
        unlink(__DIR__ . '/../' . $existingAvatar);
        $avatar = null;
    }

    if ($remove_bg === '1' && $existingBg && file_exists(__DIR__ . '/../' . $existingBg)) {
        unlink(__DIR__ . '/../' . $existingBg);
        $bg_img = null;
    }

    if (!empty($_FILES['avatar']['tmp_name'])) {
        if ($_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $avatar_ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $avatar_path = '/uploads/avatar_' . $user_id . '.' . $avatar_ext;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], __DIR__ . '/../' . $avatar_path)) {
                if ($existingAvatar && file_exists(__DIR__ . '/../' . $existingAvatar)) {
                    unlink(__DIR__ . '/../' . $existingAvatar);
                }
                $avatar = $avatar_path;
            } else {
                $error .= 'Ошибка загрузки аватара. ';
            }
        } else {
            $error .= 'Ошибка загрузки аватара. ';
        }
    }

    if (!empty($_FILES['bg_img']['tmp_name'])) {
        if ($_FILES['bg_img']['error'] === UPLOAD_ERR_OK) {
            $bg_ext = pathinfo($_FILES['bg_img']['name'], PATHINFO_EXTENSION);
            $bg_path = '/uploads/bg_' . $user_id . '.' . $bg_ext;
            if (move_uploaded_file($_FILES['bg_img']['tmp_name'], __DIR__ . '/../' . $bg_path)) {
                if ($existingBg && file_exists(__DIR__ . '/../' . $existingBg)) {
                    unlink(__DIR__ . '/../' . $existingBg);
                }
                $bg_img = $bg_path;
            } else {
                $error .= 'Ошибка загрузки фона. ';
            }
        } else {
            $error .= 'Ошибка загрузки фона. ';
        }
    }

    $stmt = $pdo->prepare("SELECT id FROM user_profiles WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $exists = $stmt->fetch();

    if ($exists) {
        $stmt = $pdo->prepare("UPDATE user_profiles SET avatar = ?, bg_img = ?, bio = ?, vk = ?, tg = ?, github = ? WHERE user_id = ?");
        $stmt->execute([$avatar, $bg_img, $bio, $vk, $tg, $github, $user_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO user_profiles (user_id, avatar, bg_img, bio, vk, tg, github) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $avatar, $bg_img, $bio, $vk, $tg, $github]);
    }

    if (empty($error)) {
        $success = true;
    }

    // Перезагрузим данные
    $stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch() ?: [];
}
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.5.13/dist/cropper.min.css">
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
    <?php if (!empty($error)): ?>
        <p class="error" style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

<form method="POST" class="profile-settings-form a-i-center" enctype="multipart/form-data" id="profileForm">
    <input type="hidden" name="remove_avatar" id="remove_avatar" value="0">
    <input type="hidden" name="remove_bg" id="remove_bg" value="0">

    <div class="wrapper-profile-editor">
        <div class="bg-cover">
            <img id="bgImage" src="<?= $profile['bg_img'] ?? '' ?>" alt="Фон">
            <div class="bg-actions">
                <button type="button" onclick="document.getElementById('bgInput').click()">Обновить</button>
                <button type="button" onclick="removeImage('bg')">Удалить</button>
                <button type="button" onclick="openCropper('bg')">Обрезать</button>
            </div>
        </div>

        <div class="avatar-block">
            <img id="avatarImage" src="<?= $profile['avatar'] ?? '' ?>" alt="Аватар">
            <div class="avatar-actions">
                <button type="button" onclick="document.getElementById('avatarInput').click()">Обновить</button>
                <button type="button" onclick="removeImage('avatar')">Удалить</button>
                <button type="button" onclick="openCropper('avatar')">Обрезать</button>
            </div>
        </div>
    </div>

    <input type="file" name="avatar" id="avatarInput" accept="image/*" hidden>
    <input type="file" name="bg_img" id="bgInput" accept="image/*" hidden>

    <!-- Cropper Modal -->
    <div id="cropModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); justify-content:center; align-items:center;">
        <div style="background:#fff; padding:1rem; max-width:90vw; max-height:90vh;">
            <h3>Обрезка изображения</h3>
            <div><img id="cropperImage" style="max-width:100%; max-height:70vh;"></div>
            <button type="button" onclick="applyCrop()">Сохранить</button>
            <button type="button" onclick="closeCropper()">Отмена</button>
        </div>
    </div>

    <span class="file-name" id="avatarFileName"></span>
    <div id="avatarPreview">
        <?php if (!empty($profile['avatar'])): ?>
            <img src="<?= $profile['avatar'] ?>?t=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . $profile['avatar']) ?>" alt="avatar">
        <?php endif; ?>
    </div>

    <span class="file-name" id="bgFileName"></span>
    <div id="bgPreview">
        <?php if (!empty($profile['bg_img'])): ?>
            <img src="<?= $profile['bg_img'] ?>?t=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . $profile['bg_img']) ?>" alt="bg">
        <?php endif; ?>
    </div>

    <div class="predit_form">
        <div class="d-flex f-d-column gap1">
            <div class="edit_block">
                <div class="edit_label">О себе:</div>
                <div class="edit_input">
                    <textarea name="bio" rows="4"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="edit_block">
                <div class="edit_label">VK:</div>
                <div class="edit_input">
                    <input type="text" name="vk" placeholder="username" value="<?= htmlspecialchars($profile['vk'] ?? '') ?>">
                </div>
            </div>
            <div class="edit_block">
                <div class="edit_label">Telegram:</div>
                <div class="edit_input">
                    <input type="text" name="tg" placeholder="username" value="<?= htmlspecialchars($profile['tg'] ?? '') ?>">
                </div>
            </div>
            <div class="edit_block">
                <div class="edit_label">GitHub:</div>
                <div class="edit_input">
                    <input type="text" name="github" placeholder="username" value="<?= htmlspecialchars($profile['github'] ?? '') ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="button-with-spinner">
        <button type="submit" class="error-btn" id="save-btn">Сохранить</button>
        <div class="spinner" id="upload-spinner" style="display:none;"></div>
    </div>
</form>
</section>
</main>
</div>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.5.13/dist/cropper.min.js"></script>
<script src="/assets/js/profile-settings.js"></script>
</body>
</html>

<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['id'])) {
    header('Location: /pages/login.php');
    exit;
}

$user_id = $_SESSION['id'];
$upload_dir = __DIR__ . '/../uploads/';

// Ensure upload directory exists
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0775, true);
}

$success = false;
$error = '';

// Fetch current profile
$stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch() ?: [];

$remove_avatar = $_POST['remove_avatar'] ?? '0';
$remove_bg = $_POST['remove_bg'] ?? '0';

$existingAvatar = $profile['avatar'] ?? null;
$existingBg = $profile['bg_img'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bio = trim($_POST['bio'] ?? '');
    $vk = trim($_POST['vk'] ?? '');
    $tg = trim($_POST['tg'] ?? '');
    $github = trim($_POST['github'] ?? '');

    $avatar = $existingAvatar;
    $bg_img = $existingBg;

    // Remove avatar if requested
    if ($remove_avatar === '1' && $existingAvatar) {
        $path = $upload_dir . basename($existingAvatar);
        if (file_exists($path)) {
            unlink($path);
        }
        $avatar = null;
    }
    // Remove background if requested
    if ($remove_bg === '1' && $existingBg) {
        $path = $upload_dir . basename($existingBg);
        if (file_exists($path)) {
            unlink($path);
        }
        $bg_img = null;
    }

    // Handle avatar upload
    if (!empty($_FILES['avatar']['tmp_name']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif'])) {
            $filename = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
            $fullPath = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $fullPath)) {
                // Delete old file
                if ($existingAvatar) {
                    $oldPath = $upload_dir . basename($existingAvatar);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $avatar = '/uploads/' . $filename;
            } else {
                $error .= 'Ошибка сохранения аватара. ';
            }
        } else {
            $error .= 'Недопустимый тип файла для аватара. ';
        }
    }

    // Handle background upload
    if (!empty($_FILES['bg_img']['tmp_name']) && $_FILES['bg_img']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['bg_img']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif'])) {
            $filename = 'bg_' . $user_id . '_' . time() . '.' . $ext;
            $fullPath = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['bg_img']['tmp_name'], $fullPath)) {
                // Delete old file
                if ($existingBg) {
                    $oldPath = $upload_dir . basename($existingBg);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $bg_img = '/uploads/' . $filename;
            } else {
                $error .= 'Ошибка сохранения фона. ';
            }
        } else {
            $error .= 'Недопустимый тип файла для фона. ';
        }
    }

    // Insert or update profile
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

    // Refresh profile data
    $stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch() ?: [];
}

// Set URLs for display
$avatar_url = !empty($profile['avatar']) ? $profile['avatar'] : '/uploads/default-avatar.png';
$bg_url = !empty($profile['bg_img']) ? $profile['bg_img'] : '/uploads/default-bg.jpg';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Настройки профиля — WebElementsLab</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/pages/settings.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.5.13/dist/cropper.min.css">
</head>
<body>
<?php require_once __DIR__ . '/../templates/header.php'; ?>
<div class="wrapper">
    <main>
        <section class="block-main">
            <form method="POST" class="profile-settings-form" enctype="multipart/form-data" id="profileForm">
                <input type="hidden" name="remove_avatar" id="remove_avatar" value="0">
                <input type="hidden" name="remove_bg" id="remove_bg" value="0">
                <div class="wrapper-profile-editor">
                    <div class="bg-cover">
                        <img id="bgImage" src="<?= $bg_url ?>" alt="Фон">
                        <div class="bg-actions">
                            <button type="button" onclick="document.getElementById('bgInput').click()">Загрузить</button>
                            <button type="button" onclick="openCropper('bg', true)">Обрезать</button>
                            <button type="button" onclick="removeImage('bg')">Удалить</button>
                        </div>
                    </div>
                    <div class="avatar-block">
                        <img id="avatarImage" src="<?= $avatar_url ?>" alt="Аватар">
                        <div class="avatar-actions">
                            <button type="button" onclick="document.getElementById('avatarInput').click()">Загрузить</button>
                            <button type="button" onclick="openCropper('avatar', true)">Обрезать</button>
                            <button type="button" onclick="removeImage('avatar')">Удалить</button>
                        </div>
                    </div>
                </div>
                <input type="file" name="avatar" id="avatarInput" accept="image/*" hidden>
                <input type="file" name="bg_img" id="bgInput" accept="image/*" hidden>
                <div id="cropModal" style="display:none;">
                    <img id="cropperImage" style="max-width:100%; max-height:70vh;">
                    <button type="button" onclick="applyCrop()">Сохранить</button>
                    <button type="button" onclick="closeCropper()">Отмена</button>
                </div>
                <?php if ($success): ?><p>Профиль обновлён!</p><?php endif; ?>
                <?php if ($error): ?><p><?= htmlspecialchars($error) ?></p><?php endif; ?>
                <textarea name="bio" rows="4"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
                <input type="text" name="vk" value="<?= htmlspecialchars($profile['vk'] ?? '') ?>" placeholder="VK">
                <input type="text" name="tg" value="<?= htmlspecialchars($profile['tg'] ?? '') ?>" placeholder="Telegram">
                <input type="text" name="github" value="<?= htmlspecialchars($profile['github'] ?? '') ?>" placeholder="GitHub">
                <button type="submit" id="save-btn">Сохранить</button>
                <div id="upload-spinner" style="display:none;"></div>
            </form>
        </section>
    </main>
</div>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.5.13/dist/cropper.min.js"></script>
<script src="/assets/js/profile-settings.js"></script>
</body>
</html>

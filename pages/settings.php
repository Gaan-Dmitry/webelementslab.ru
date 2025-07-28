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
        if ($_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $avatar_ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $avatar_path = '/uploads/avatar_' . $user_id . '.' . $avatar_ext;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], __DIR__ . '/../' . $avatar_path)) {
                $avatar = $avatar_path;
            } else {
                $error .= 'Ошибка загрузки аватара. ';
            }
        } else {
            $error .= 'Ошибка загрузки аватара. ';
        }
    }

    // Фон
    if (!empty($_FILES['bg_img']['tmp_name'])) {
        if ($_FILES['bg_img']['error'] === UPLOAD_ERR_OK) {
            $bg_ext = pathinfo($_FILES['bg_img']['name'], PATHINFO_EXTENSION);
            $bg_path = '/uploads/bg_' . $user_id . '.' . $bg_ext;
            if (move_uploaded_file($_FILES['bg_img']['tmp_name'], __DIR__ . '/../' . $bg_path)) {
                $bg_img = $bg_path;
            } else {
                $error .= 'Ошибка загрузки фона. ';
            }
        } else {
            $error .= 'Ошибка загрузки фона. ';
        }
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

    if (empty($error)) {
        $success = true;
    }
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
                <?php if (!empty($error)): ?>
                    <p class="error" style="color:red;"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>
                <form method="POST" class="profile-settings-form" enctype="multipart/form-data" id="profileForm">
                    <label>
                        Аватар:
                        <input type="file" name="avatar" accept="image/*" id="avatarInput">
                    </label>
                    <div id="avatarPreview">
                        <?php if (!empty($profile['avatar'])): ?>
                            <?php
                            $avatarPath = htmlspecialchars($profile['avatar']);
                            $avatarFile = $_SERVER['DOCUMENT_ROOT'] . $avatarPath;
                            $avatarTime = file_exists($avatarFile) ? filemtime($avatarFile) : time();
                            ?>
                            <img src="<?= $avatarPath ?>?t=<?= $avatarTime ?>" alt="avatar" style="max-height:100px">
                        <?php endif; ?>
                    </div>
                    <label>
                        Фон:
                        <input type="file" name="bg_img" accept="image/*" id="bgInput">
                    </label>
                    <div id="bgPreview">
                        <?php if (!empty($profile['bg_img'])): ?>
                            <?php
                            $bgPath = htmlspecialchars($profile['bg_img']);
                            $bgFile = $_SERVER['DOCUMENT_ROOT'] . $bgPath;
                            $bgTime = file_exists($bgFile) ? filemtime($bgFile) : time();
                            ?>
                            <img src="<?= $bgPath ?>?t=<?= $bgTime ?>" alt="bg" style="max-height:100px">
                        <?php endif; ?>
                    </div>
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
                    <div class="button-with-spinner">
                        <button type="submit" class="reg-btn anim-hover-box-shadow" id="save-btn">Сохранить</button>
                        <div class="spinner" id="upload-spinner" style="display:none;"></div>
                    </div>
                </form>
            </section>
        </main>
    </div>
    <?php require_once __DIR__ . '/../templates/footer.php'; ?>
</body>
<script>
// Предпросмотр аватара
const avatarInput = document.getElementById('avatarInput');
const avatarPreview = document.getElementById('avatarPreview');
avatarInput.addEventListener('change', function() {
    avatarPreview.innerHTML = '';
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            avatarPreview.innerHTML = '<img src="' + e.target.result + '" alt="avatar" style="max-height:100px">';
        };
        reader.readAsDataURL(this.files[0]);
    }
    document.getElementById('upload-spinner').style.display = 'none';
    document.getElementById('save-btn').disabled = false;
});
// Предпросмотр фона
const bgInput = document.getElementById('bgInput');
const bgPreview = document.getElementById('bgPreview');
bgInput.addEventListener('change', function() {
    bgPreview.innerHTML = '';
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            bgPreview.innerHTML = '<img src="' + e.target.result + '" alt="bg" style="max-height:100px">';
        };
        reader.readAsDataURL(this.files[0]);
    }
    document.getElementById('upload-spinner').style.display = 'none';
    document.getElementById('save-btn').disabled = false;
});
// Loader-спиннер при отправке формы
const form = document.getElementById('profileForm');
const spinner = document.getElementById('upload-spinner');
const saveBtn = document.getElementById('save-btn');
form.addEventListener('submit', function() {
    spinner.style.display = 'block';
    saveBtn.disabled = true;
});
</script>
</html>

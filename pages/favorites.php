<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['id'])) {
	header('Location: /pages/login.php');
	exit;
}

$user_id = $_SESSION['id'];

// Получаем избранные сниппеты с дополнительными полями
$stmt = $pdo->prepare('
		SELECT s.id, s.name, s.tag, s.description, s.html, s.css, s.js
	FROM favorites f
	JOIN snippets s ON s.id = f.snippet_id
	WHERE f.user_id = ?
	ORDER BY f.created_at DESC
');
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Избранное — WebElementsLab</title>
	<link rel="apple-touch-icon" href="/assets/img/logo192.png" >
	<link rel="icon" href="/assets/img/favicon.ico" >
	<link rel="stylesheet" href="/assets/css/style.css">
	<link rel="stylesheet" href="/assets/css/pages/favorites.css">
</head>
<body>
<?php require_once __DIR__ . '/../templates/header.php'; ?>
<div class="wrapper">
	<main>
		<section class="block-main">
			<h1>Избранное</h1>
			<?php if (empty($favorites)): ?>
				<div class="empty-favorites">
					<h2 class="empty-favorites__title">Пусто</h2>
					<p class="empty-favorites__desc">Добавляйте сниппеты в избранное на странице карточки, чтобы видеть их здесь.</p>
                    <br>
					<a class="reg-btn anim-hover-box-shadow" href="/">Перейти к сниппетам</a>
				</div>
			<?php else: ?>
				<div class="favorites-list snippets-grid">
					<?php foreach ($favorites as $snippet): ?>
						<?php
							// Подготавливаем данные для data-атрибутов (без экранирования под HTML — snippet.js сам экранирует при вставке в srcdoc)
							$rawHtml = $snippet['html'] ?? '';
							$rawCss  = $snippet['css']  ?? '';
							$rawJs   = $snippet['js']   ?? '';

							// Экранируем только для безопасной вставки в HTML-атрибуты (data-*), используем ENT_QUOTES + ENT_SUBSTITUTE
							$dataHtml = htmlspecialchars($rawHtml, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
							$dataCss  = htmlspecialchars($rawCss,  ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
							$dataJs   = htmlspecialchars($rawJs,   ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
						?>
						<article class="snippet-card" data-snippet-id="<?= (int) $snippet['id'] ?>" data-href="/pages/card.php?id=<?= (int) $snippet['id'] ?>" tabindex="0" role="link">
							<div class="snippet-card__preview"
								data-html="<?= $dataHtml ?>"
								data-css="<?= $dataCss ?>"
								data-js="<?= $dataJs ?>">
								<iframe
									class="snippet-card__iframe"
									loading="lazy"
									sandbox="allow-scripts"
									referrerpolicy="no-referrer"
									aria-hidden="true"
									title="Предпросмотр сниппета «<?= htmlspecialchars($snippet['name']) ?>»"></iframe>
								<div class="pholder">
									<div class="snippet-card__favorite">
										<button
											type="button"
											class="btn-card snippet-card__favorite-btn fav"
											data-id="<?= (int) $snippet['id'] ?>"
											data-remove-on-unfav="true"
											aria-label="Убрать из избранного">💖</button>
									</div>
									<div class="snippet-card__share">
										<button
											type="button"
											class="btn-card snippet-card__share-btn"
											aria-label="Поделиться сниппетом"
											onclick="shareSnippet(<?= (int) $snippet['id'] ?>, <?= json_encode(htmlspecialchars($snippet['name'])) ?>)">🔗</button>
									</div>
								</div>
							</div>
							<h3 class="snippet-card__title"><?= htmlspecialchars($snippet['name']) ?></h3>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</section>
	</main>
</div>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>
<script>
function shareSnippet(id, name) {
	const shareUrl = `${window.location.origin}/pages/card.php?id=${id}`;
	if (navigator.share) {
		navigator.share({ title: name || 'Сниппет', url: shareUrl })
			.catch(err => { if (err.name !== 'AbortError') console.warn('Web Share error:', err); });
	} else {
		navigator.clipboard.writeText(shareUrl)
			.then(() => alert('Ссылка скопирована!'))
			.catch(() => {
				const tmp = document.createElement('input');
				tmp.value = shareUrl;
				document.body.appendChild(tmp);
				tmp.select();
				document.execCommand('copy');
				document.body.removeChild(tmp);
				alert('Ссылка скопирована!');
			});
	}
}
</script>
</body>
<script src="/assets/js/snippet.js"></script>
</html>

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
							$tags = array_values(array_filter(array_map('trim', explode(',', $snippet['tag'] ?? ''))));
							$primaryTag = $tags[0] ?? null;
							$extraTags = $primaryTag ? array_slice($tags, 1, 3) : array_slice($tags, 0, 3);
							$previewHtml = htmlspecialchars($snippet['html'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
							$previewCss = htmlspecialchars($snippet['css'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
							$previewJs = htmlspecialchars($snippet['js'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
						?>
						<article class="snippet-card" data-snippet-id="<?= (int) $snippet['id'] ?>">
							<div class="snippet-card__preview"
								data-html="<?= $previewHtml ?>"
								data-css="<?= $previewCss ?>"
								data-js="<?= $previewJs ?>">
								<iframe
									class="snippet-card__iframe"
									loading="lazy"
									aria-hidden="true"
									title="Предпросмотр сниппета «<?= htmlspecialchars($snippet['name']) ?>»"></iframe>
								<div class="snippet-card__favorite">
									<button
										type="button"
										class="btn-card snippet-card__favorite-btn fav"
										data-id="<?= $snippet['id'] ?>"
										data-remove-on-unfav="true"
										aria-label="Убрать из избранного">💖</button>
								</div>
							</div>
							<h3 class="snippet-card__title"><?= htmlspecialchars($snippet['name']) ?></h3>
							<?php if ($primaryTag || !empty($extraTags)): ?>
								<div class="snippet-card__meta">
									<?php if ($primaryTag): ?>
										<span class="snippet-card__badge"><?= htmlspecialchars($primaryTag) ?></span>
									<?php endif; ?>
									<?php if (!empty($extraTags)): ?>
										<div class="snippet-card__tags">
											<?php foreach ($extraTags as $tag): ?>
												<span class="tag-pill tag-pill--compact"><?= htmlspecialchars($tag) ?></span>
											<?php endforeach; ?>
										</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							<div class="snippet-card__actions">
								<a href="/pages/card.php?id=<?= $snippet['id'] ?>" class="snippet-card__btn">Открыть</a>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</section>
	</main>
</div>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>
</body>
<script src="/assets/js/snippet.js"></script>
</html>

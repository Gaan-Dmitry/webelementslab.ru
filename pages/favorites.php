<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['id'])) {
	header('Location: /pages/login.php');
	exit;
}

function buildSnippetPreview(?string $html, int $limit = 220): string
{
	$html = trim($html ?? '');

	if ($html === '') {
		return 'Нет HTML-кода для предпросмотра';
	}

	$normalized = preg_replace('/\s+/', ' ', $html);

	if (mb_strlen($normalized) <= $limit) {
		return $normalized;
	}

	return mb_substr($normalized, 0, $limit) . '…';
}

$user_id = $_SESSION['id'];

// Получаем избранные сниппеты с дополнительными полями
$stmt = $pdo->prepare('
	SELECT s.id, s.name, s.tag, s.description, s.html
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
					<div class="empty-favorites__icon">💤</div>
					<h2 class="empty-favorites__title">Пусто</h2>
					<p class="empty-favorites__desc">Добавляйте сниппеты в избранное на странице карточки, чтобы видеть их здесь.</p>
					<a class="reg-btn anim-hover-box-shadow" href="/">Перейти к сниппетам</a>
				</div>
			<?php else: ?>
				<div class="favorites-list snippets-grid">
					<?php foreach ($favorites as $snippet): ?>
						<?php
							$tags = array_values(array_filter(array_map('trim', explode(',', $snippet['tag'] ?? ''))));
							$preview = buildSnippetPreview($snippet['html'] ?? '');
						?>
						<article class="snippet-card" data-snippet-id="<?= (int) $snippet['id'] ?>">
							<div class="snippet-card__head">
								<?php if (!empty($tags)): ?>
									<span class="snippet-card__badge"><?= htmlspecialchars($tags[0]) ?></span>
								<?php endif; ?>
								<?php if (count($tags) > 1): ?>
									<div class="snippet-card__tags">
										<?php foreach (array_slice($tags, 1, 3) as $tag): ?>
											<span class="tag-pill tag-pill--compact"><?= htmlspecialchars($tag) ?></span>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
							</div>
							<h3 class="snippet-card__title"><?= htmlspecialchars($snippet['name']) ?></h3>
							<?php if (!empty($snippet['description'])): ?>
								<p class="snippet-card__desc"><?= htmlspecialchars($snippet['description']) ?></p>
							<?php endif; ?>
							<div class="snippet-card__preview">
								<pre><code><?= htmlspecialchars($preview) ?></code></pre>
							</div>
							<div class="snippet-card__actions">
								<a href="/pages/card.php?id=<?= $snippet['id'] ?>" class="reg-btn anim-hover-box-shadow">Открыть</a>
								<button class="btn-remove-fav" data-id="<?= $snippet['id'] ?>" aria-label="Удалить из избранного">Удалить</button>
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

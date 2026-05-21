<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

// Функция для обрезки HTML до нужной длины (для превью)
function buildSnippetPreview(?string $html, int $limit = 220): string
{
	$html = trim($html ?? '');

	if ($html === '') {
		return '';
	}

	// Заменяем все пробельные символы на один пробел
	$normalized = preg_replace('/\s+/', ' ', $html);

	// Если текст короче лимита - возвращаем как есть
	if (mb_strlen($normalized) <= $limit) {
		return $normalized;
	}

	// Иначе обрезаем и добавляем троеточие
	return mb_substr($normalized, 0, $limit) . '…';
}

// Получаем offset из запроса (для пагинации)
$offset = isset($_GET['offset']) ? max((int) $_GET['offset'], 0) : 0;
$limit = 10;
// Поиск по названию или тегу
$query = trim($_GET['q'] ?? '');
$like = '%' . $query . '%';

// Запрос к базе
$stmt = $pdo->prepare(
	"SELECT id, name, tag, description, html, css, js
	 FROM snippets
	 WHERE (? = '' OR name LIKE ? OR tag LIKE ?)
	 ORDER BY created_at DESC
	 LIMIT ? OFFSET ?"
);
$stmt->bindValue(1, $query, PDO::PARAM_STR);
$stmt->bindValue(2, $like, PDO::PARAM_STR);
$stmt->bindValue(3, $like, PDO::PARAM_STR);
$stmt->bindValue(4, $limit, PDO::PARAM_INT);
$stmt->bindValue(5, $offset, PDO::PARAM_INT);
$stmt->execute();
$snippets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Проверяем избранное если юзер залогинен
$userId = isset($_SESSION['id']) ? (int) $_SESSION['id'] : null;
$favoritesMap = [];

if ($userId && !empty($snippets)) {
	$ids = array_map('intval', array_column($snippets, 'id'));
	$placeholders = implode(',', array_fill(0, count($ids), '?'));

	$favStmt = $pdo->prepare("SELECT snippet_id FROM favorites WHERE user_id = ? AND snippet_id IN ($placeholders)");
	$favStmt->execute(array_merge([$userId], $ids));
	$favoritedIds = array_map('intval', $favStmt->fetchAll(PDO::FETCH_COLUMN));
	$favoritesMap = array_fill_keys($favoritedIds, true);
}

// Формируем ответ с данными сниппетов
$snippets = array_map(static function (array $snippet) use ($favoritesMap, $userId): array {
	$tagString = $snippet['tag'] ?? '';
	$tags = array_values(array_filter(array_map('trim', explode(',', $tagString))));

	return [
		'id' => (int) $snippet['id'],
		'name' => $snippet['name'],
		'description' => $snippet['description'] ?? '',
		'tag' => $tagString,
		'tags' => $tags,
		'primary_tag' => $tags[0] ?? null,
		'preview' => buildSnippetPreview($snippet['html'] ?? ''),
		'html' => $snippet['html'] ?? '',
		'css' => $snippet['css'] ?? '',
		'js' => $snippet['js'] ?? '',
		'is_favorite' => isset($favoritesMap[(int) $snippet['id']]),
		'can_favorite' => $userId ? true : false,
	];
}, $snippets);

header('Content-Type: application/json');
echo json_encode($snippets, JSON_UNESCAPED_UNICODE);

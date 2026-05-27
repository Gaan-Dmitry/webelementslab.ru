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
// Фильтр по конкретному тегу
$filterTag = trim($_GET['tag'] ?? '');

// Запрос к базе
if ($filterTag !== '') {
    // Фильтрация по точному тегу
    $stmt = $pdo->prepare(
        "SELECT id, name, tag, description, html, css, js
         FROM snippets
         WHERE tag LIKE ?
         ORDER BY created_at DESC
         LIMIT ? OFFSET ?"
    );
    $tagLike = '%' . $filterTag . '%';
    $stmt->bindValue(1, $tagLike, PDO::PARAM_STR);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->bindValue(3, $offset, PDO::PARAM_INT);
} else {
    // Умный поиск по названию или тегу: сначала забираем кандидатов, затем сортируем по релевантности в PHP
    $stmt = $pdo->prepare(
        "SELECT id, name, tag, description, html, css, js
         FROM snippets
         WHERE (? = '' OR name LIKE ? OR tag LIKE ?)
         ORDER BY created_at DESC
         LIMIT 120"
    );
    $stmt->bindValue(1, $query, PDO::PARAM_STR);
    $stmt->bindValue(2, $like, PDO::PARAM_STR);
    $stmt->bindValue(3, $like, PDO::PARAM_STR);
}
$stmt->execute();
$snippets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Если LIKE-выборка ничего не дала (опечатка/другая раскладка), пробуем fuzzy по более широкой выборке
if ($filterTag === '' && $query !== '' && empty($snippets)) {
	$fallbackStmt = $pdo->query(
		"SELECT id, name, tag, description, html, css, js
		 FROM snippets
		 ORDER BY created_at DESC
		 LIMIT 240"
	);
	$snippets = $fallbackStmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($filterTag === '' && $query !== '' && !empty($snippets)) {
	$normalizedQuery = mb_strtolower($query);
	$tokens = array_values(array_filter(array_map('trim', preg_split('/\s+/u', $normalizedQuery))));

	$scoreSnippet = static function (array $snippet) use ($normalizedQuery, $tokens): int {
		$name = mb_strtolower((string) ($snippet['name'] ?? ''));
		$tag = mb_strtolower((string) ($snippet['tag'] ?? ''));
		$score = 0;

		if ($name === $normalizedQuery) {
			$score += 1200;
		}

		if (str_starts_with($name, $normalizedQuery)) {
			$score += 700;
		}

		if (str_contains($name, $normalizedQuery)) {
			$score += 520;
		}

		foreach ($tokens as $token) {
			if ($token === '') {
				continue;
			}

			if (str_starts_with($name, $token)) {
				$score += 130;
			}

			if (str_contains($name, $token)) {
				$score += 100;
			}

			if (str_contains($tag, $token)) {
				$score += 45;
			}
		}

		// Небольшой fuzzy-буст за близость по Левенштейну (для опечаток)
		$nameAscii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name) ?: $name;
		$queryAscii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $normalizedQuery) ?: $normalizedQuery;
		if ($nameAscii !== '' && $queryAscii !== '') {
			$distance = levenshtein($queryAscii, mb_substr($nameAscii, 0, max(mb_strlen($queryAscii), 1) + 6));
			$score += max(0, 110 - $distance * 14);
		}

		return $score;
	};

	foreach ($snippets as &$snippet) {
		$snippet['_score'] = $scoreSnippet($snippet);
	}
	unset($snippet);

	usort(
		$snippets,
		static function (array $a, array $b): int {
			return $b['_score'] <=> $a['_score'];
		}
	);

	$snippets = array_values(array_slice($snippets, $offset, $limit));
} elseif ($filterTag === '') {
	$snippets = array_values(array_slice($snippets, $offset, $limit));
}

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

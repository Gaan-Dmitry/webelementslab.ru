<?php
require_once __DIR__ . '/../includes/db.php';

/**
 * Формирует короткий текстовый превью-фрагмент HTML-кода.
 */
function buildSnippetPreview(?string $html, int $limit = 220): string
{
	$html = trim($html ?? '');

	if ($html === '') {
		return '';
	}

	$normalized = preg_replace('/\s+/', ' ', $html);

	if (mb_strlen($normalized) <= $limit) {
		return $normalized;
	}

	return mb_substr($normalized, 0, $limit) . '…';
}

$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = 10;

$stmt = $pdo->prepare("SELECT id, name, tag, description, html FROM snippets ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$snippets = $stmt->fetchAll(PDO::FETCH_ASSOC);

$snippets = array_map(static function (array $snippet): array {
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
	];
}, $snippets);

header('Content-Type: application/json');
echo json_encode($snippets, JSON_UNESCAPED_UNICODE);
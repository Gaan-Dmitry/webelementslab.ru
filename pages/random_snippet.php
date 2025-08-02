<?php
require_once __DIR__ . '/../includes/db.php';

// Получаем случайный ID сниппета
$stmt = $pdo->query("SELECT id FROM snippets ORDER BY RAND() LIMIT 1");
$snippet = $stmt->fetch();

if ($snippet) {
    header("Location: /pages/card.php?id=" . $snippet['id']);
    exit;
} else {
    // Если сниппетов нет
    header("Location: /?empty=true");
    exit;
}
?>

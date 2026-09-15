<?php
/**
 * ====================================================================
 * api/categories.php — კატეგორიების სია (ფილტრის dropdown-ისთვის).
 * GET ?lang=ka|en|ru
 * პასუხი: [ { slug, name }, ... ]
 * ====================================================================
 */

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

$pdo  = getDb();
$lang = currentLang();
$nameCol = "name_{$lang}";

$stmt = $pdo->query("SELECT slug, {$nameCol} AS name FROM categories WHERE parent_id IS NULL ORDER BY sort_order ASC");
echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE);

<?php
/**
 * ====================================================================
 * api/sizes.php — ყველა ზომის სია (ფილტრის pill-ებისთვის).
 * GET ?type=clothing|shoe (optional — თუ არაა, ორივე ბრუნდება)
 * პასუხი: [ { label, type }, ... ]
 * ====================================================================
 */

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

$pdo = getDb();

$sql = "SELECT label, type FROM sizes";
$params = [];

if (!empty($_GET['type']) && in_array($_GET['type'], ['clothing', 'shoe'], true)) {
    $sql .= " WHERE type = :type";
    $params[':type'] = $_GET['type'];
}

$sql .= " ORDER BY type ASC, sort_order ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE);

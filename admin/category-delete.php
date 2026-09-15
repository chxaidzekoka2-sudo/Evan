<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    header('Location: ' . BASE_URL . '/admin/categories.php');
    exit;
}

$pdo = getDb();
// products.category_id-ზე FK-ს CASCADE არ აქვს დაყენებული განზრახ (რომ
// შემთხვევით არ წაიშალოს პროდუქტების დიდი რაოდენობა) — ჯერ ვამოწმებთ
$check = $pdo->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
$check->execute([(int)$_POST['id']]);

if ($check->fetchColumn() > 0) {
    header('Location: ' . BASE_URL . '/admin/categories.php?error=has_products');
    exit;
}

$stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
$stmt->execute([(int)$_POST['id']]);

header('Location: ' . BASE_URL . '/admin/categories.php');
exit;

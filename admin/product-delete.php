<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    header('Location: ' . BASE_URL . '/admin/products.php');
    exit;
}

$pdo = getDb();
// product_images და product_sizes ავტომატურად წაიშლება ON DELETE CASCADE-ით
$stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
$stmt->execute([(int)$_POST['id']]);

header('Location: ' . BASE_URL . '/admin/products.php');
exit;

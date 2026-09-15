<?php
/**
 * ====================================================================
 * api/product-detail.php — ერთი პროდუქტის სრული ინფორმაცია.
 * GET ?id=123&lang=ka|en|ru
 *
 * პასუხი: { id, name, description, price, category, images: [...],
 *           sizes: [{ label, stock_qty }] }
 * ან 404, თუ ვერ მოიძებნა/არააქტიურია.
 * ====================================================================
 */

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

$pdo  = getDb();
$lang = currentLang();
$id   = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid product id']);
    exit;
}

$nameCol = "p.name_{$lang}";
$descCol = "p.description_{$lang}";

$stmt = $pdo->prepare("
    SELECT p.id, {$nameCol} AS name, {$descCol} AS description, p.price, c.slug AS category
    FROM products p
    JOIN categories c ON c.id = p.category_id
    WHERE p.id = ? AND p.is_active = 1
");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    echo json_encode(['error' => 'Product not found']);
    exit;
}

$imgStmt = $pdo->prepare('SELECT image_path FROM product_images WHERE product_id = ? ORDER BY sort_order ASC');
$imgStmt->execute([$id]);
$images = array_map(fn($row) => BASE_URL . '/uploads/products/' . $row['image_path'], $imgStmt->fetchAll());
if (empty($images)) {
    $images = [BASE_URL . '/uploads/products/placeholder.jpg'];
}

$sizeStmt = $pdo->prepare('
    SELECT s.label, ps.stock_qty
    FROM product_sizes ps
    JOIN sizes s ON s.id = ps.size_id
    WHERE ps.product_id = ?
    ORDER BY s.sort_order ASC
');
$sizeStmt->execute([$id]);
$sizes = $sizeStmt->fetchAll();

echo json_encode([
    'id'          => (int)$product['id'],
    'name'        => $product['name'],
    'description' => $product['description'],
    'price'       => (float)$product['price'],
    'category'    => $product['category'],
    'images'      => $images,
    'sizes'       => array_map(fn($s) => ['label' => $s['label'], 'stock_qty' => (int)$s['stock_qty']], $sizes),
], JSON_UNESCAPED_UNICODE);

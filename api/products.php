<?php
/**
 * ====================================================================
 * api/products.php — პროდუქტების სია, ფილტრებით.
 *
 * GET პარამეტრები (ყველა optional):
 *   category   — slug: men | women | shoes | accessories
 *   size       — ზომის ლეიბლი, მაგ. M ან 42 (ფილტრავს მხოლოდ მარაგში
 *                მყოფ ზომებზე, product_sizes.stock_qty > 0)
 *   price_min  — მინიმალური ფასი
 *   price_max  — მაქსიმალური ფასი
 *   is_new     — 1 იმისთვის, რომ მხოლოდ "სიახლეები" დაბრუნდეს
 *   lang       — ka | en | ru (default: ka)
 *
 * მაგალითი: /api/products.php?category=women&size=M&price_min=100&price_max=400&lang=en
 *
 * პასუხი: JSON მასივი, თითო ელემენტი:
 *   { id, name, price, category, sizes: ["S","M","L"], image, is_new }
 * ====================================================================
 */

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

$pdo  = getDb();
$lang = currentLang(); // 'ka' | 'en' | 'ru' — ვალიდირებულია db.php-ში

$where  = ['p.is_active = 1'];
$params = [];

if (!empty($_GET['category'])) {
    $where[] = 'c.slug = :category';
    $params[':category'] = $_GET['category'];
}

if (!empty($_GET['price_min']) && is_numeric($_GET['price_min'])) {
    $where[] = 'p.price >= :price_min';
    $params[':price_min'] = (float)$_GET['price_min'];
}

if (!empty($_GET['price_max']) && is_numeric($_GET['price_max'])) {
    $where[] = 'p.price <= :price_max';
    $params[':price_max'] = (float)$_GET['price_max'];
}

if (!empty($_GET['is_new'])) {
    $where[] = 'p.is_new = 1';
}

// ზომის ფილტრი: მხოლოდ პროდუქტები, რომლებსაც მოცემულ ზომაში მარაგი აქვთ
if (!empty($_GET['size'])) {
    $where[] = 'p.id IN (
        SELECT ps.product_id FROM product_sizes ps
        JOIN sizes s ON s.id = ps.size_id
        WHERE s.label = :size AND ps.stock_qty > 0
    )';
    $params[':size'] = $_GET['size'];
}

$whereSql = implode(' AND ', $where);

// name_{$lang} column — $lang მხოლოდ ['ka','en','ru']-დან შეიძლება იყოს
// (იხ. currentLang() db.php-ში), ასე რომ უსაფრთხოა პირდაპირ ჩასმა SQL-ში.
$nameCol = "p.name_{$lang}";

$sql = "
    SELECT
        p.id,
        {$nameCol} AS name,
        p.price,
        p.is_new,
        c.slug AS category
    FROM products p
    JOIN categories c ON c.id = p.category_id
    WHERE {$whereSql}
    ORDER BY p.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

if (empty($products)) {
    echo json_encode([]);
    exit;
}

$ids = array_column($products, 'id');
$placeholders = implode(',', array_fill(0, count($ids), '?'));

// თითოეული პროდუქტის პირველი ფოტო
$imgStmt = $pdo->prepare("
    SELECT product_id, MIN(image_path) as image_path FROM (
        SELECT product_id, image_path
        FROM product_images
        ORDER BY sort_order ASC
    ) t
    WHERE product_id IN ($placeholders)
    GROUP BY product_id
");
$imgStmt->execute($ids);
$images = [];
foreach ($imgStmt->fetchAll() as $row) {
    $images[$row['product_id']] = $row['image_path'];
}

// თითოეული პროდუქტის ხელმისაწვდომი ზომები (მარაგი > 0)
$sizeStmt = $pdo->prepare("
    SELECT ps.product_id, s.label
    FROM product_sizes ps
    JOIN sizes s ON s.id = ps.size_id
    WHERE ps.product_id IN ($placeholders) AND ps.stock_qty > 0
    ORDER BY s.sort_order ASC
");
$sizeStmt->execute($ids);
$sizesByProduct = [];
foreach ($sizeStmt->fetchAll() as $row) {
    $sizesByProduct[$row['product_id']][] = $row['label'];
}

$result = array_map(function ($p) use ($images, $sizesByProduct) {
    return [
        'id'       => (int)$p['id'],
        'name'     => $p['name'],
        'price'    => (float)$p['price'],
        'category' => $p['category'],
        'is_new'   => (bool)$p['is_new'],
        'image'    => BASE_URL . '/uploads/products/' . ($images[$p['id']] ?? 'placeholder.jpg'),
        'sizes'    => $sizesByProduct[$p['id']] ?? [],
    ];
}, $products);

echo json_encode($result, JSON_UNESCAPED_UNICODE);

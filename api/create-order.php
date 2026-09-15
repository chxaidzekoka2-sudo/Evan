<?php
/**
 * ====================================================================
 * api/create-order.php — checkout.php-დან POST მოთხოვნის მიმღები.
 *
 * 1. ვქმნით ადგილობრივ order-ს (status=pending)
 * 2. ვითხოვთ BOG-ის access token-ს
 * 3. ვქმნით BOG-ის ecommerce order-ს, კალათის მონაცემებით
 * 4. ვაბრუნებთ redirect_url-ს, სადაც JS გადაამისამართებს მომხმარებელს
 *
 * Request body (JSON):
 *   { customer: { name, email, phone, address },
 *     cart: [ { id, name, price, qty, size } ] }
 * ====================================================================
 */

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/bog.php';

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['cart']) || !is_array($input['cart'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Cart is empty']);
    exit;
}

$customer = $input['customer'] ?? [];
$name  = trim($customer['name'] ?? '');
$email = trim($customer['email'] ?? '');
$phone = trim($customer['phone'] ?? '');
$address = trim($customer['address'] ?? '');

if ($name === '' || $email === '' || $address === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing customer details']);
    exit;
}

$pdo = getDb();

// --- პროდუქტების რეალური ფასების გადამოწმება ბაზიდან (არასდროს ვენდობით
//     frontend-ის მიერ გამოგზავნილ ფასს პირდაპირ) ---
$cart = $input['cart'];
$productIds = array_unique(array_map(fn($l) => (int)$l['id'], $cart));
$placeholders = implode(',', array_fill(0, count($productIds), '?'));
$stmt = $pdo->prepare("SELECT id, name_ka, price FROM products WHERE id IN ($placeholders) AND is_active = 1");
$stmt->execute($productIds);
$dbProducts = [];
foreach ($stmt->fetchAll() as $row) $dbProducts[$row['id']] = $row;

$orderItems = [];
$basket = [];
$total = 0.0;

foreach ($cart as $line) {
    $pid = (int)$line['id'];
    if (!isset($dbProducts[$pid])) continue; // არააქტიური/არარსებული პროდუქტი — გამოტოვება
    $qty = max(1, (int)($line['qty'] ?? 1));
    $unitPrice = (float)$dbProducts[$pid]['price']; // ბაზიდან, არა frontend-იდან
    $lineTotal = $unitPrice * $qty;
    $total += $lineTotal;

    $orderItems[] = [
        'product_id' => $pid,
        'size_label' => $line['size'] ?? null,
        'qty' => $qty,
        'unit_price' => $unitPrice,
    ];
    $basket[] = [
        'product_id' => (string)$pid,
        'description' => mb_substr($dbProducts[$pid]['name_ka'], 0, 60),
        'quantity' => $qty,
        'unit_price' => $unitPrice,
    ];
}

if (empty($orderItems)) {
    http_response_code(400);
    echo json_encode(['error' => 'No valid products in cart']);
    exit;
}

// --- ადგილობრივი შეკვეთის შექმნა ---
$pdo->beginTransaction();
$stmt = $pdo->prepare("
    INSERT INTO orders (customer_name, customer_email, customer_phone, shipping_address, total_amount, currency, payment_status, payment_provider)
    VALUES (?, ?, ?, ?, ?, 'GEL', 'pending', 'bog')
");
$stmt->execute([$name, $email, $phone, $address, $total]);
$orderId = (int)$pdo->lastInsertId();

$itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, size_label, qty, unit_price) VALUES (?,?,?,?,?)");
foreach ($orderItems as $item) {
    $itemStmt->execute([$orderId, $item['product_id'], $item['size_label'], $item['qty'], $item['unit_price']]);
}
$pdo->commit();

// --- BOG-თან შეკვეთის შექმნა (ან სატესტო იმიტაცია) ---
if (BOG_TEST_MODE) {
    // სატესტო რეჟიმი — რეალურ BOG-ს არ ვუკავშირდებით. პირდაპირ ვნიშნავთ
    // შეკვეთას "paid"-ად და ვასრულებთ იმავე ლოგიკას, რასაც production-ში
    // payment-callback.php გააკეთებდა (მარაგის კლება).
    $pdo->prepare("UPDATE orders SET payment_status = 'paid', payment_reference = 'TEST-MODE' WHERE id = ?")
        ->execute([$orderId]);

    $decrementStmt = $pdo->prepare("
        UPDATE product_sizes ps
        JOIN sizes s ON s.id = ps.size_id
        SET ps.stock_qty = GREATEST(ps.stock_qty - ?, 0)
        WHERE ps.product_id = ? AND s.label = ?
    ");
    foreach ($orderItems as $item) {
        if ($item['size_label']) {
            $decrementStmt->execute([$item['qty'], $item['product_id'], $item['size_label']]);
        }
    }

    echo json_encode([
        'order_id' => $orderId,
        'redirect_url' => BASE_URL . '/checkout-success.php?order=' . $orderId,
    ]);
    exit;
}

$accessToken = bogGetAccessToken();
if (!$accessToken) {
    http_response_code(502);
    echo json_encode(['error' => 'Payment provider authentication failed']);
    exit;
}

$bogResult = bogCreateOrder($accessToken, [
    'external_order_id' => $orderId,
    'total_amount' => $total,
    'currency' => 'GEL',
], $basket);

if (!$bogResult) {
    http_response_code(502);
    echo json_encode(['error' => 'Payment provider order creation failed']);
    exit;
}

$pdo->prepare('UPDATE orders SET payment_reference = ? WHERE id = ?')
    ->execute([$bogResult['bog_order_id'], $orderId]);

echo json_encode([
    'order_id' => $orderId,
    'redirect_url' => $bogResult['redirect_url'],
]);

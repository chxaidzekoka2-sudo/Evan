<?php
/**
 * ====================================================================
 * payment-callback.php — BOG server-to-server callback endpoint.
 *
 * ეს მისამართი (SITE_BASE_URL/payment-callback.php) გადაეცემა BOG-ს
 * callback_url-ად შეკვეთის შექმნისას (config/bog.php-ში). BOG აქ
 * გამოგზავნის POST-ს გადახდის დასრულებისას (წარმატებული ან წარუმატებელი).
 *
 * მნიშვნელოვანია: HTTP 200 დააბრუნო წარმატებული მიღების დასადასტურებლად,
 * თორემ BOG თავიდან სცდის callback-ის გაგზავნას.
 * ====================================================================
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/bog.php';

$rawBody = file_get_contents('php://input');
$signature = $_SERVER['HTTP_CALLBACK_SIGNATURE'] ?? '';

// --- ხელმოწერის შემოწმება, სანამ body-ს გავშლით ---
if ($signature === '' || !bogVerifyCallbackSignature($rawBody, $signature)) {
    error_log('BOG callback: invalid signature');
    http_response_code(400);
    exit('Invalid signature');
}

$data = json_decode($rawBody, true);
if (!$data || ($data['event'] ?? '') !== 'order_payment') {
    http_response_code(400);
    exit('Unexpected payload');
}

$body = $data['body'] ?? [];
$externalOrderId = (int)($body['external_order_id'] ?? 0); // ჩვენი ადგილობრივი orders.id
$bogOrderId = $body['order_id'] ?? null;
$statusKey = $body['order_status']['key'] ?? '';

if (!$externalOrderId) {
    http_response_code(400);
    exit('Missing external_order_id');
}

// BOG-ის სტატუსების ჩვენს ოთხ მდგომარეობაზე რუკება
$statusMap = [
    'completed' => 'paid',
    'partial_completed' => 'paid',
    'rejected' => 'failed',
    'refunded' => 'cancelled',
    'refunded_partially' => 'cancelled',
    // created / processing / auth_requested / blocked / refund_requested -> pending-ად ჩავთვალოთ
];
$newStatus = $statusMap[$statusKey] ?? 'pending';

$pdo = getDb();

// მიმდინარე სტატუსის წამოღება — მარაგი მხოლოდ იმ შემთხვევაში დავაკლოთ,
// თუ ეს არის pending -> paid პირველი გადასვლა (არა განმეორებითი callback)
$currentStmt = $pdo->prepare('SELECT payment_status FROM orders WHERE id = ?');
$currentStmt->execute([$externalOrderId]);
$currentOrder = $currentStmt->fetch();

if (!$currentOrder) {
    http_response_code(404);
    exit('Order not found');
}

$wasAlreadyPaid = ($currentOrder['payment_status'] === 'paid');

$stmt = $pdo->prepare('UPDATE orders SET payment_status = ?, payment_reference = ? WHERE id = ?');
$stmt->execute([$newStatus, $bogOrderId, $externalOrderId]);

// --- მარაგის ატომური კლება, მხოლოდ პირველივე დადასტურებაზე ---
if ($newStatus === 'paid' && !$wasAlreadyPaid) {
    $itemsStmt = $pdo->prepare('SELECT product_id, size_label, qty FROM order_items WHERE order_id = ?');
    $itemsStmt->execute([$externalOrderId]);

    $decrementStmt = $pdo->prepare("
        UPDATE product_sizes ps
        JOIN sizes s ON s.id = ps.size_id
        SET ps.stock_qty = GREATEST(ps.stock_qty - ?, 0)
        WHERE ps.product_id = ? AND s.label = ?
    ");

    foreach ($itemsStmt->fetchAll() as $item) {
        if ($item['size_label']) {
            $decrementStmt->execute([$item['qty'], $item['product_id'], $item['size_label']]);
        }
    }

    // TODO: აქ შეგიძლია დაამატო დადასტურების ემეილის გაგზავნა მომხმარებელზე
}

http_response_code(200);
echo 'OK';

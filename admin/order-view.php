<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getDb();
$orderId = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: ' . BASE_URL . '/admin/orders.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['payment_status'])) {
    $allowed = ['pending', 'paid', 'failed', 'cancelled'];
    if (in_array($_POST['payment_status'], $allowed, true)) {
        $pdo->prepare('UPDATE orders SET payment_status = ? WHERE id = ?')
            ->execute([$_POST['payment_status'], $orderId]);
        $order['payment_status'] = $_POST['payment_status'];
    }
}

$itemsStmt = $pdo->prepare("
    SELECT oi.*, p.name_ka FROM order_items oi
    JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id = ?
");
$itemsStmt->execute([$orderId]);
$items = $itemsStmt->fetchAll();

$activeNav = 'orders';
$pageTitle = 'Order #' . $orderId;
require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-topbar">
    <h1>შეკვეთა #<?php echo $orderId; ?></h1>
    <a href="<?php echo BASE_URL; ?>/admin/orders.php" class="btn btn--ghost">← სიაში დაბრუნება</a>
</div>

<div class="admin-form" style="margin-bottom:20px;">
    <p><strong>მომხმარებელი:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
    <p><strong>ელ-ფოსტა:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
    <p><strong>ტელეფონი:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
    <p><strong>მისამართი:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
    <p><strong>გადახდის მეთოდი:</strong> <?php echo htmlspecialchars($order['payment_provider'] ?? '—'); ?>
       (ref: <?php echo htmlspecialchars($order['payment_reference'] ?? '—'); ?>)</p>
    <p><strong>თარიღი:</strong> <?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></p>
</div>

<table style="margin-bottom:20px;">
    <thead><tr><th>პროდუქტი</th><th>ზომა</th><th>რაოდ.</th><th>ფასი</th><th>ჯამი</th></tr></thead>
    <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?php echo htmlspecialchars($item['name_ka']); ?></td>
            <td><?php echo htmlspecialchars($item['size_label'] ?? '—'); ?></td>
            <td><?php echo (int)$item['qty']; ?></td>
            <td><?php echo number_format($item['unit_price'], 2); ?> GEL</td>
            <td><?php echo number_format($item['unit_price'] * $item['qty'], 2); ?> GEL</td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="4" style="text-align:right;"><strong>სულ:</strong></td>
            <td><strong><?php echo number_format($order['total_amount'], 2); ?> GEL</strong></td>
        </tr>
    </tbody>
</table>

<form class="admin-form" method="post" style="max-width:340px;">
    <div class="form-group">
        <label>გადახდის სტატუსი (ხელით შეცვლა)</label>
        <select name="payment_status">
            <?php foreach (['pending', 'paid', 'failed', 'cancelled'] as $s): ?>
                <option value="<?php echo $s; ?>" <?php echo $order['payment_status'] === $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn--dark">განახლება</button>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>

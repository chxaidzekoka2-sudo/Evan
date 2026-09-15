<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getDb();
$statusFilter = $_GET['status'] ?? '';

$where = [];
$params = [];
if ($statusFilter !== '') {
    $where[] = 'payment_status = ?';
    $params[] = $statusFilter;
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$stmt = $pdo->prepare("SELECT * FROM orders $whereSql ORDER BY created_at DESC");
$stmt->execute($params);
$orders = $stmt->fetchAll();

$activeNav = 'orders';
$pageTitle = 'Orders';
require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-topbar">
    <h1>შეკვეთები</h1>
</div>

<form class="filter-bar" method="get">
    <select name="status" onchange="this.form.submit()">
        <option value="">ყველა სტატუსი</option>
        <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>pending</option>
        <option value="paid" <?php echo $statusFilter === 'paid' ? 'selected' : ''; ?>>paid</option>
        <option value="failed" <?php echo $statusFilter === 'failed' ? 'selected' : ''; ?>>failed</option>
        <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>cancelled</option>
    </select>
</form>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>მომხმარებელი</th>
            <th>თანხა</th>
            <th>სტატუსი</th>
            <th>თარიღი</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($orders)): ?>
            <tr><td colspan="6">შეკვეთები არ მოიძებნა.</td></tr>
        <?php endif; ?>
        <?php foreach ($orders as $o): ?>
        <tr>
            <td>#<?php echo $o['id']; ?></td>
            <td><?php echo htmlspecialchars($o['customer_name']); ?><br><span style="opacity:.6; font-size:12px;"><?php echo htmlspecialchars($o['customer_email']); ?></span></td>
            <td><?php echo number_format($o['total_amount'], 2); ?> <?php echo htmlspecialchars($o['currency']); ?></td>
            <td>
                <span class="badge <?php echo $o['payment_status'] === 'paid' ? 'badge--active' : 'badge--inactive'; ?>">
                    <?php echo htmlspecialchars($o['payment_status']); ?>
                </span>
            </td>
            <td><?php echo date('d.m.Y H:i', strtotime($o['created_at'])); ?></td>
            <td><a href="<?php echo BASE_URL; ?>/admin/order-view.php?id=<?php echo $o['id']; ?>" class="btn btn--ghost btn--sm">ნახვა</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>

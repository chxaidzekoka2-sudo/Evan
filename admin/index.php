<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getDb();
$productCount  = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$categoryCount = $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
$orderCount    = $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status = 'pending'")->fetchColumn();

$activeNav = 'dashboard';
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-topbar">
    <h1>დეშბორდი</h1>
</div>

<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px;">
    <div class="admin-form" style="text-align:center;">
        <div style="font-size:32px; font-family:'Cormorant Garamond',serif;"><?php echo $productCount; ?></div>
        <div style="font-size:12px; color:var(--a-accent); text-transform:uppercase;">პროდუქტი</div>
    </div>
    <div class="admin-form" style="text-align:center;">
        <div style="font-size:32px; font-family:'Cormorant Garamond',serif;"><?php echo $categoryCount; ?></div>
        <div style="font-size:12px; color:var(--a-accent); text-transform:uppercase;">კატეგორია</div>
    </div>
    <div class="admin-form" style="text-align:center;">
        <div style="font-size:32px; font-family:'Cormorant Garamond',serif;"><?php echo $orderCount; ?></div>
        <div style="font-size:12px; color:var(--a-accent); text-transform:uppercase;">შეკვეთა (სულ)</div>
    </div>
    <div class="admin-form" style="text-align:center;">
        <div style="font-size:32px; font-family:'Cormorant Garamond',serif;"><?php echo $pendingOrders; ?></div>
        <div style="font-size:12px; color:var(--a-accent); text-transform:uppercase;">დამუშავების მოლოდინში</div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>

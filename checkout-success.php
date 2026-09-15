<?php
$pageTitle = 'Order Confirmed';
require_once __DIR__ . '/config/db.php';

$orderId = (int)($_GET['order'] ?? 0);
$order = null;
if ($orderId) {
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT id, payment_status, total_amount FROM orders WHERE id = ?');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
}

$pageCss = 'pages.css';
include __DIR__ . '/includes/header.php';
?>
<main>
<section class="shop-section" style="max-width:600px; text-align:center; padding-top:80px;">
    <h1 class="section-title">მადლობა შენი შეკვეთისთვის!</h1>
    <?php if ($order): ?>
        <p style="margin-bottom:10px;">შეკვეთა #<?php echo $order['id']; ?> — <?php echo number_format($order['total_amount'], 2); ?> GEL</p>
        <p style="opacity:.7; font-size:13px;">
            გადახდის სტატუსი: <strong><?php echo htmlspecialchars($order['payment_status']); ?></strong><br>
            (თუ "pending"-ია, სტატუსი განახლდება ავტომატურად რამდენიმე წამში — გვერდის განახლება სცადეთ)
        </p>
    <?php else: ?>
        <p>შეკვეთა ვერ მოიძებნა.</p>
    <?php endif; ?>
    <a href="<?php echo BASE_URL; ?>/shop.php" class="btn btn--dark" style="margin-top:24px;">ყიდვის გაგრძელება</a>
</section>
</main>
<script>
// გადახდა წარმატებულია — ვასუფთავებთ ლოკალურ კალათას
localStorage.removeItem('evan_cart_v1');
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>

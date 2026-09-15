<?php
$pageTitle = 'Payment Failed';
$pageCss = 'pages.css';
include __DIR__ . '/includes/header.php';
?>
<main>
<section class="shop-section" style="max-width:600px; text-align:center; padding-top:80px;">
    <h1 class="section-title">გადახდა ვერ შესრულდა</h1>
    <p style="opacity:.75;">ბარათი არ დაიხასათა, ან გადახდა გაუქმდა. კალათა შენახულია — შეგიძლია ისევ სცადო.</p>
    <a href="<?php echo BASE_URL; ?>/checkout.php" class="btn btn--dark" style="margin-top:24px;">ხელახლა ცდა</a>
    <a href="<?php echo BASE_URL; ?>/shop.php" class="btn btn--ghost" style="margin-top:24px;">მაღაზიაში დაბრუნება</a>
</section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>

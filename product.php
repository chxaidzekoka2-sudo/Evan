<?php
$pageTitle = 'Product';
$pageCss = 'product.css';
include __DIR__ . '/includes/header.php';
$extraScripts = ['/assets/js/product.js'];

$productId = (int)($_GET['id'] ?? 0);
?>

<main>
<section class="product-detail" id="productDetail" data-product-id="<?php echo $productId; ?>">
    <p class="grid-loading">...</p>
</section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

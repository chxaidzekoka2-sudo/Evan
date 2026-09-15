<?php
$pageTitle = 'Shop';
$pageCss = 'shop.css';
include __DIR__ . '/includes/header.php';
$extraScripts = ['/assets/js/shop.js'];
?>

<main>
<section class="shop-section">
    <h1 class="section-title" data-i18n="categories_title">Shop</h1>

    <div class="shop-layout">
        <!-- ფილტრების პანელი -->
        <aside class="filter-panel" id="filterPanel">
            <div class="filter-group">
                <h4 data-i18n="filter_category">Category</h4>
                <div class="filter-options" id="filterCategory">
                    <!-- JS ავსებს api/categories.php-დან -->
                </div>
            </div>

            <div class="filter-group">
                <h4 data-i18n="filter_size">Size</h4>
                <div class="filter-options filter-options--pills" id="filterSize">
                    <!-- JS ავსებს api/sizes.php-დან -->
                </div>
            </div>

            <div class="filter-group">
                <h4 data-i18n="filter_price">Price</h4>
                <div class="filter-price-inputs">
                    <input type="number" id="filterPriceMin" placeholder="Min" min="0">
                    <span>—</span>
                    <input type="number" id="filterPriceMax" placeholder="Max" min="0">
                </div>
            </div>

            <div class="filter-actions">
                <button type="button" class="btn btn--dark btn--full" id="filterApply" data-i18n="filter_apply">Apply</button>
                <button type="button" class="btn btn--ghost btn--full" id="filterClear" data-i18n="filter_clear">Clear</button>
            </div>
        </aside>

        <!-- პროდუქტების ბადე -->
        <div class="shop-results">
            <div class="product-grid" id="shopGrid"></div>
        </div>
    </div>
</section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

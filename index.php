<?php
$pageTitle = null; // მთავარი გვერდი — <title> უბრალოდ "Evan" იყოს
$pageCss = 'home.css';
include __DIR__ . '/includes/header.php';
?>

<!-- Welcome / promo popup -->
<div class="promo-modal" id="promoModal">
    <div class="promo-modal__box">
        <button class="promo-modal__close" id="promoClose">&times;</button>
        <span class="eyebrow" data-i18n="site_name">Evan</span>
        <h2 data-i18n="promo_title">Welcome</h2>
        <p data-i18n="promo_text">Use the code below...</p>
        <div class="promo-code">
            <span>EVAN10</span>
            <button id="promoCopy" data-i18n="copy_code">Copy</button>
        </div>
        <a href="<?php echo BASE_URL; ?>/shop.php" class="btn btn--dark" data-i18n="shop_now">Shop Now</a>
    </div>
</div>

<main>

<!-- Hero -->
<section class="hero">
    <div class="hero__frame"><span class="hero__mono">E</span></div>
    <div class="hero__content">
        <span class="eyebrow" data-i18n="site_name">Evan</span>
        <h1 data-i18n="hero_title">Style That Defines Every Step</h1>
        <p data-i18n="hero_sub">The new season collection</p>
        <a href="<?php echo BASE_URL; ?>/shop.php" class="btn btn--outline" data-i18n="shop_now">Shop Now</a>
    </div>
</section>

<!-- Categories -->
<section class="categories">
    <h2 class="section-title" data-i18n="categories_title">Categories</h2>
    <div class="categories__grid">
        <a href="<?php echo BASE_URL; ?>/shop.php?category=men" class="category-card">
            <span class="category-card__arch"><span class="category-card__mono">M</span></span>
            <span class="category-card__name" data-i18n="cat_men">Men's Clothing</span>
            <span class="category-card__view" data-i18n="view_all">View All</span>
        </a>
        <a href="<?php echo BASE_URL; ?>/shop.php?category=women" class="category-card">
            <span class="category-card__arch"><span class="category-card__mono">W</span></span>
            <span class="category-card__name" data-i18n="cat_women">Women's Clothing</span>
            <span class="category-card__view" data-i18n="view_all">View All</span>
        </a>
        <a href="<?php echo BASE_URL; ?>/shop.php?category=shoes" class="category-card">
            <span class="category-card__arch"><span class="category-card__mono">S</span></span>
            <span class="category-card__name" data-i18n="cat_shoes">Shoes</span>
            <span class="category-card__view" data-i18n="view_all">View All</span>
        </a>
        <a href="<?php echo BASE_URL; ?>/shop.php?category=accessories" class="category-card">
            <span class="category-card__arch"><span class="category-card__mono">A</span></span>
            <span class="category-card__name" data-i18n="cat_accessories">Accessories</span>
            <span class="category-card__view" data-i18n="view_all">View All</span>
        </a>
    </div>
</section>

<!-- New In -->
<section class="product-section">
    <div class="product-section__head">
        <h2 class="section-title" data-i18n="new_in_title">New In</h2>
        <a href="<?php echo BASE_URL; ?>/shop.php" class="link-arrow" data-i18n="view_all">View All</a>
    </div>
    <div class="product-grid" id="newInGrid"></div>
</section>

<!-- Brand story -->
<section class="story">
    <div class="story__frame"><span class="story__mono">E</span></div>
    <div class="story__content">
        <span class="eyebrow" data-i18n="press_title">As Featured In</span>
        <p data-i18n="story_text">We curate quality clothing...</p>
        <a href="<?php echo BASE_URL; ?>/about.php" class="link-arrow" data-i18n="about_us">About Us</a>
    </div>
</section>

<!-- Shoes -->
<section class="product-section">
    <div class="product-section__head">
        <h2 class="section-title" data-i18n="nav_shoes">Shoes</h2>
        <a href="<?php echo BASE_URL; ?>/shop.php?category=shoes" class="link-arrow" data-i18n="view_all">View All</a>
    </div>
    <div class="product-grid" id="shoesGrid"></div>
</section>

<!-- Instagram banner -->
<section class="insta-banner">
    <a href="#" class="insta-banner__item">
        <span class="insta-banner__mono">M</span>
        <span data-i18n="instagram_title">Follow us on Instagram</span>
    </a>
    <a href="#" class="insta-banner__item insta-banner__item--center">
        <span class="insta-banner__mono">E</span>
        <span data-i18n="site_name">Evan</span>
    </a>
    <a href="#" class="insta-banner__item">
        <span class="insta-banner__mono">W</span>
        <span data-i18n="instagram_title">Follow us on Instagram</span>
    </a>
</section>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

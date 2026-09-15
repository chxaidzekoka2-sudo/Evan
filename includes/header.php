<?php
require_once __DIR__ . '/../config/db.php'; // BASE_URL-ისთვის (idempotent - safe თუ უკვე ჩართულია)
?>
<!DOCTYPE html>
<html lang="ka">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — Evan' : 'Evan'; ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/base.css">
<?php if (!empty($pageCss)): ?>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/<?php echo htmlspecialchars($pageCss); ?>">
<?php endif; ?>
</head>
<body>

<!-- Announcement bar -->
<div class="announce-bar">
    <p data-i18n="announcement">Free delivery...</p>
</div>

<!-- Header -->
<header class="site-header">
    <div class="site-header__inner">
        <button class="hamburger" id="menuToggle" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>

        <a href="<?php echo BASE_URL; ?>/index.php" class="logo" data-i18n="site_name">Evan</a>

        <nav class="main-nav">
            <a href="<?php echo BASE_URL; ?>/shop.php?category=men" data-i18n="nav_men">Men</a>
            <a href="<?php echo BASE_URL; ?>/shop.php?category=women" data-i18n="nav_women">Women</a>
            <a href="<?php echo BASE_URL; ?>/shop.php?category=shoes" data-i18n="nav_shoes">Shoes</a>
            <a href="<?php echo BASE_URL; ?>/shop.php?category=accessories" data-i18n="nav_accessories">Accessories</a>
            <a href="<?php echo BASE_URL; ?>/contact.php" data-i18n="nav_contact">Contact</a>
        </nav>

        <div class="header-actions">
            <div class="lang-switch" id="langSwitch">
                <button class="lang-switch__current" id="langCurrent" aria-haspopup="listbox">
                    <span id="langCurrentLabel">KA</span>
                </button>
                <ul class="lang-switch__list" id="langList">
                    <li><button type="button" data-lang-option="ka">ქართული</button></li>
                    <li><button type="button" data-lang-option="en">English</button></li>
                    <li><button type="button" data-lang-option="ru">Русский</button></li>
                </ul>
            </div>

            <button class="icon-btn cart-btn" id="cartToggle" aria-label="Cart">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M6 8h12l-1 12H7L6 8z" stroke="currentColor" stroke-width="1.4"/><path d="M9 8V6a3 3 0 016 0v2" stroke="currentColor" stroke-width="1.4"/></svg>
                <span class="cart-count" id="cartCount">0</span>
            </button>
        </div>
    </div>

    <!-- Mobile nav drawer -->
    <nav class="mobile-nav" id="mobileNav">
        <a href="<?php echo BASE_URL; ?>/shop.php?category=men" data-i18n="nav_men">Men</a>
        <a href="<?php echo BASE_URL; ?>/shop.php?category=women" data-i18n="nav_women">Women</a>
        <a href="<?php echo BASE_URL; ?>/shop.php?category=shoes" data-i18n="nav_shoes">Shoes</a>
        <a href="<?php echo BASE_URL; ?>/shop.php?category=accessories" data-i18n="nav_accessories">Accessories</a>
        <a href="<?php echo BASE_URL; ?>/contact.php" data-i18n="nav_contact">Contact</a>
    </nav>
</header>

<!-- Cart drawer -->
<div class="cart-drawer" id="cartDrawer">
    <div class="cart-drawer__head">
        <h3 data-i18n="cart_title">My Cart</h3>
        <button id="cartClose" aria-label="Close">&times;</button>
    </div>
    <div class="cart-drawer__items" id="cartItems">
        <p class="cart-empty-msg" data-i18n="cart_empty">Your cart is empty</p>
    </div>
    <div class="cart-drawer__foot">
        <div class="cart-subtotal">
            <span data-i18n="subtotal">Subtotal</span>
            <span id="cartSubtotal">0.00 GEL</span>
        </div>
        <a href="<?php echo BASE_URL; ?>/checkout.php" class="btn btn--dark btn--full" data-i18n="checkout">Checkout</a>
        <button class="btn btn--ghost btn--full" id="cartContinue" data-i18n="continue_shopping">Continue Shopping</button>
    </div>
</div>
<div class="overlay" id="overlay"></div>

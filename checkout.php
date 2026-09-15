<?php
$pageTitle = 'Checkout';
$pageCss = 'checkout.css';
include __DIR__ . '/includes/header.php';
$extraScripts = ['/assets/js/checkout.js'];
?>

<main>
<section class="shop-section" style="max-width:760px;">
    <h1 class="section-title" data-i18n="checkout">Checkout</h1>

    <?php
    require_once __DIR__ . '/config/bog.php';
    if (BOG_TEST_MODE):
    ?>
    <p class="admin-success">🧪 სატესტო რეჟიმი — რეალური გადახდა არ განხორციელდება, შეკვეთა ავტომატურად დადასტურდება.</p>
    <?php endif; ?>

    <div id="checkoutEmptyMsg" style="display:none;" class="admin-error">
        <span data-i18n="cart_empty">Your cart is empty</span> — <a href="<?php echo BASE_URL; ?>/shop.php" data-i18n="continue_shopping">Continue Shopping</a>
    </div>

    <div id="checkoutContent">
        <div class="admin-form" style="margin-bottom:24px;">
            <h3 style="margin-bottom:16px; font-size:18px;" data-i18n="cart_title">My Cart</h3>
            <div id="checkoutCartSummary"></div>
            <div class="cart-subtotal" style="margin-top:14px; font-size:16px; font-weight:600;">
                <span data-i18n="subtotal">Subtotal</span>
                <span id="checkoutTotal">0.00 GEL</span>
            </div>
        </div>

        <form class="admin-form" id="checkoutForm">
            <div class="form-row">
                <div class="form-group">
                    <label>სახელი, გვარი</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>ტელეფონი</label>
                    <input type="tel" name="phone" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>ელ-ფოსტა</label>
                    <input type="email" name="email" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>მისამართი (ქალაქი, ქუჩა, კორპუსი)</label>
                    <textarea name="address" required></textarea>
                </div>
            </div>

            <p id="checkoutError" class="admin-error" style="display:none;"></p>

            <div class="form-actions">
                <button type="submit" class="btn btn--dark" id="checkoutSubmitBtn">გადახდაზე გადასვლა</button>
            </div>
        </form>
    </div>
</section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

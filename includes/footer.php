<!-- USP marquee -->
<div class="usp-bar">
    <div class="usp-track">
        <span data-i18n="free_delivery">Free Delivery</span><span class="dot">&bull;</span>
        <span data-i18n="duties_included">Duties Included</span><span class="dot">&bull;</span>
        <span data-i18n="easy_returns">Easy Returns</span><span class="dot">&bull;</span>
        <span data-i18n="free_delivery">Free Delivery</span><span class="dot">&bull;</span>
        <span data-i18n="duties_included">Duties Included</span><span class="dot">&bull;</span>
        <span data-i18n="easy_returns">Easy Returns</span><span class="dot">&bull;</span>
    </div>
</div>

<footer class="site-footer">
    <div class="footer-top">
        <div class="footer-brand">
            <p class="logo logo--footer" data-i18n="site_name">Evan</p>
            <p class="footer-tagline" data-i18n="footer_tagline">Where Style Meets Ease</p>
        </div>

        <div class="footer-col">
            <h4 data-i18n="footer_info">Info</h4>
            <a href="<?php echo BASE_URL; ?>/about.php" data-i18n="about_us">About Us</a>
            <a href="<?php echo BASE_URL; ?>/returns.php" data-i18n="footer_returns">Returns Policy</a>
            <a href="<?php echo BASE_URL; ?>/privacy.php" data-i18n="footer_privacy">Privacy Policy</a>
            <a href="<?php echo BASE_URL; ?>/terms.php" data-i18n="footer_terms">Terms of Service</a>
            <a href="<?php echo BASE_URL; ?>/shipping.php" data-i18n="footer_shipping">Shipping Policy</a>
        </div>

        <div class="footer-col">
            <h4 data-i18n="footer_help">Help</h4>
            <a href="<?php echo BASE_URL; ?>/profile.php" data-i18n="footer_profile">Profile</a>
            <a href="<?php echo BASE_URL; ?>/contact.php" data-i18n="nav_contact">Contact</a>
            <a href="<?php echo BASE_URL; ?>/size-guide.php" data-i18n="footer_size_guide">Size Guide</a>
            <a href="<?php echo BASE_URL; ?>/stores.php" data-i18n="footer_stores">Stores</a>
        </div>

        <div class="footer-col footer-col--newsletter">
            <h4 data-i18n="newsletter_title">Join our community</h4>
            <p data-i18n="newsletter_sub">Exclusive updates...</p>
            <form class="newsletter-form" action="<?php echo BASE_URL; ?>/subscribe.php" method="post">
                <input type="email" name="email" data-i18n-placeholder="newsletter_placeholder" placeholder="Your email" required>
                <button type="submit" data-i18n="sign_up">Sign Up</button>
            </form>
            <div class="social-links">
                <a href="#" aria-label="Instagram">IG</a>
                <a href="#" aria-label="Facebook">FB</a>
                <a href="#" aria-label="TikTok">TT</a>
                <a href="#" aria-label="WhatsApp">WA</a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; <span id="year">2026</span> <span data-i18n="site_name">Evan</span>. <span data-i18n="rights">All rights reserved.</span></p>
    </div>
</footer>

<script>window.BASE_URL = <?php echo json_encode(BASE_URL); ?>;</script>
<script src="<?php echo BASE_URL; ?>/assets/js/i18n.js"></script>
<script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
<script src="<?php echo BASE_URL; ?>/assets/js/products.js"></script>
<?php if (!empty($extraScripts)): foreach ($extraScripts as $src): ?>
<script src="<?php echo BASE_URL . htmlspecialchars($src); ?>"></script>
<?php endforeach; endif; ?>
<script>document.getElementById('year').textContent = new Date().getFullYear();</script>
</body>
</html>

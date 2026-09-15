<?php
$pageTitle = 'Contact';
$pageCss = 'pages.css';
include __DIR__ . '/includes/header.php';
$sent = isset($_GET['sent']);
?>
<main>
<section class="shop-section" style="max-width:640px;">
    <h1 class="section-title" data-i18n="nav_contact">Contact</h1>
    <p style="margin-bottom:24px;" data-i18n="contact_intro">Have a question? Send us a message.</p>

    <?php if ($sent): ?>
        <p class="admin-success" data-i18n="contact_success">Thank you! We've received your message.</p>
    <?php endif; ?>

    <form class="admin-form" method="post" action="<?php echo BASE_URL; ?>/contact-send.php">
        <div class="form-group" style="margin-bottom:16px;">
            <label data-i18n="contact_name">Name</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-group" style="margin-bottom:16px;">
            <label data-i18n="contact_email_label">Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group" style="margin-bottom:16px;">
            <label data-i18n="contact_message">Message</label>
            <textarea name="message" required></textarea>
        </div>
        <button type="submit" class="btn btn--dark" data-i18n="contact_send">Send</button>
    </form>
</section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>

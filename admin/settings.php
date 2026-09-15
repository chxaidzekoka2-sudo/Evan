<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getDb();
$keys = ['whatsapp', 'facebook', 'instagram', 'telegram', 'phone', 'email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('UPDATE settings SET setting_value = ? WHERE setting_key = ?');
    foreach ($keys as $key) {
        $stmt->execute([trim($_POST[$key] ?? ''), $key]);
    }
    $saved = true;
}

$rows = $pdo->query('SELECT setting_key, setting_value FROM settings')->fetchAll();
$values = [];
foreach ($rows as $r) $values[$r['setting_key']] = $r['setting_value'];

$activeNav = 'settings';
$pageTitle = 'Settings';
require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-topbar">
    <h1>პარამეტრები</h1>
</div>

<?php if (!empty($saved)): ?>
    <p class="admin-success">შენახულია.</p>
<?php endif; ?>

<form class="admin-form" method="post">
    <div class="form-row">
        <div class="form-group">
            <label>WhatsApp (ბმული ან ნომერი)</label>
            <input type="text" name="whatsapp" value="<?php echo htmlspecialchars($values['whatsapp'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>Facebook</label>
            <input type="text" name="facebook" value="<?php echo htmlspecialchars($values['facebook'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Instagram</label>
            <input type="text" name="instagram" value="<?php echo htmlspecialchars($values['instagram'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>Telegram</label>
            <input type="text" name="telegram" value="<?php echo htmlspecialchars($values['telegram'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>ტელეფონი</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($values['phone'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>ელ-ფოსტა</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($values['email'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn--dark">შენახვა</button>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>

<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['mark_read_id'])) {
    $pdo->prepare('UPDATE contact_messages SET is_read = 1 WHERE id = ?')->execute([(int)$_POST['mark_read_id']]);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete_id'])) {
    $pdo->prepare('DELETE FROM contact_messages WHERE id = ?')->execute([(int)$_POST['delete_id']]);
}

$messages = $pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
$subscribers = $pdo->query('SELECT * FROM newsletter_subscribers ORDER BY created_at DESC')->fetchAll();

$activeNav = 'messages';
$pageTitle = 'Messages';
require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-topbar">
    <h1>საკონტაქტო შეტყობინებები</h1>
</div>

<table style="margin-bottom:40px;">
    <thead><tr><th>სახელი</th><th>ელ-ფოსტა</th><th>შეტყობინება</th><th>თარიღი</th><th></th></tr></thead>
    <tbody>
        <?php if (empty($messages)): ?>
            <tr><td colspan="5">შეტყობინებები არ არის.</td></tr>
        <?php endif; ?>
        <?php foreach ($messages as $m): ?>
        <tr style="<?php echo $m['is_read'] ? 'opacity:.6;' : 'font-weight:600;'; ?>">
            <td><?php echo htmlspecialchars($m['name']); ?></td>
            <td><?php echo htmlspecialchars($m['email']); ?></td>
            <td style="max-width:340px;"><?php echo nl2br(htmlspecialchars($m['message'])); ?></td>
            <td><?php echo date('d.m.Y H:i', strtotime($m['created_at'])); ?></td>
            <td class="table-actions">
                <?php if (!$m['is_read']): ?>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="mark_read_id" value="<?php echo $m['id']; ?>">
                    <button type="submit" class="btn btn--ghost btn--sm">წაკითხულად მონიშვნა</button>
                </form>
                <?php endif; ?>
                <form method="post" style="display:inline;" onsubmit="return confirm('წაშლა?');">
                    <input type="hidden" name="delete_id" value="<?php echo $m['id']; ?>">
                    <button type="submit" class="btn btn--danger btn--sm">წაშლა</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2 style="font-size:20px;">გამომწერები (Newsletter)</h2>
<table>
    <thead><tr><th>ელ-ფოსტა</th><th>თარიღი</th></tr></thead>
    <tbody>
        <?php if (empty($subscribers)): ?>
            <tr><td colspan="2">გამომწერები არ არის.</td></tr>
        <?php endif; ?>
        <?php foreach ($subscribers as $s): ?>
        <tr>
            <td><?php echo htmlspecialchars($s['email']); ?></td>
            <td><?php echo date('d.m.Y', strtotime($s['created_at'])); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>

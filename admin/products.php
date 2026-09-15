<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getDb();

$categoryFilter = $_GET['category'] ?? '';
$where = [];
$params = [];
if ($categoryFilter !== '') {
    $where[] = 'p.category_id = :category_id';
    $params[':category_id'] = (int)$categoryFilter;
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$stmt = $pdo->prepare("
    SELECT p.id, p.name_ka, p.price, p.is_active, p.is_new, c.name_ka AS category_name
    FROM products p
    JOIN categories c ON c.id = p.category_id
    $whereSql
    ORDER BY p.created_at DESC
");
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query('SELECT id, name_ka FROM categories ORDER BY sort_order')->fetchAll();

$activeNav = 'products';
$pageTitle = 'Products';
require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-topbar">
    <h1>პროდუქტები</h1>
    <a href="<?php echo BASE_URL; ?>/admin/product-form.php" class="btn btn--dark">+ ახალი პროდუქტი</a>
</div>

<form class="filter-bar" method="get">
    <select name="category" onchange="this.form.submit()">
        <option value="">ყველა კატეგორია</option>
        <?php foreach ($categories as $c): ?>
            <option value="<?php echo $c['id']; ?>" <?php echo $categoryFilter == $c['id'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($c['name_ka']); ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<table>
    <thead>
        <tr>
            <th>სახელი</th>
            <th>კატეგორია</th>
            <th>ფასი</th>
            <th>სტატუსი</th>
            <th>სიახლე</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($products)): ?>
        <tr><td colspan="6">პროდუქტები არ მოიძებნა.</td></tr>
        <?php endif; ?>
        <?php foreach ($products as $p): ?>
        <tr>
            <td><?php echo htmlspecialchars($p['name_ka']); ?></td>
            <td><?php echo htmlspecialchars($p['category_name']); ?></td>
            <td><?php echo number_format($p['price'], 2); ?> GEL</td>
            <td>
                <span class="badge <?php echo $p['is_active'] ? 'badge--active' : 'badge--inactive'; ?>">
                    <?php echo $p['is_active'] ? 'აქტიური' : 'გამორთული'; ?>
                </span>
            </td>
            <td><?php echo $p['is_new'] ? '✓' : '—'; ?></td>
            <td class="table-actions">
                <a href="<?php echo BASE_URL; ?>/admin/product-form.php?id=<?php echo $p['id']; ?>" class="btn btn--ghost btn--sm">რედაქტირება</a>
                <form method="post" action="<?php echo BASE_URL; ?>/admin/product-delete.php" onsubmit="return confirm('დარწმუნებული ხარ რომ წაშალო?');" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                    <button type="submit" class="btn btn--danger btn--sm">წაშლა</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>

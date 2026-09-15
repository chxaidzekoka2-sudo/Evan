<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getDb();
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
$errors = [];
$form = ['slug' => '', 'name_ka' => '', 'name_en' => '', 'name_ru' => ''];

if ($editId) {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([$editId]);
    $found = $stmt->fetch();
    if ($found) $form = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['slug']    = trim($_POST['slug'] ?? '');
    $form['name_ka'] = trim($_POST['name_ka'] ?? '');
    $form['name_en'] = trim($_POST['name_en'] ?? '');
    $form['name_ru'] = trim($_POST['name_ru'] ?? '');
    $postId = (int)($_POST['id'] ?? 0);

    if ($form['slug'] === '' || $form['name_ka'] === '') {
        $errors[] = 'Slug და ქართული სახელი აუცილებელია.';
    } else {
        if ($postId > 0) {
            $stmt = $pdo->prepare('UPDATE categories SET slug=?, name_ka=?, name_en=?, name_ru=? WHERE id=?');
            $stmt->execute([$form['slug'], $form['name_ka'], $form['name_en'], $form['name_ru'], $postId]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO categories (slug, name_ka, name_en, name_ru, sort_order) VALUES (?,?,?,?,?)');
            $stmt->execute([$form['slug'], $form['name_ka'], $form['name_en'], $form['name_ru'], 99]);
        }
        header('Location: ' . BASE_URL . '/admin/categories.php');
        exit;
    }
}

$categories = $pdo->query('SELECT * FROM categories ORDER BY sort_order')->fetchAll();

$activeNav = 'categories';
$pageTitle = 'Categories';
require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-topbar">
    <h1>კატეგორიები</h1>
</div>

<?php if (($_GET['error'] ?? '') === 'has_products'): ?>
    <p class="admin-error">ამ კატეგორიის წაშლა ვერ ხერხდება — ჯერ გადაიტანე ან წაშალე მასში არსებული პროდუქტები.</p>
<?php endif; ?>

<table style="margin-bottom:30px;">
    <thead>
        <tr><th>Slug</th><th>KA</th><th>EN</th><th>RU</th><th></th></tr>
    </thead>
    <tbody>
        <?php foreach ($categories as $c): ?>
        <tr>
            <td><?php echo htmlspecialchars($c['slug']); ?></td>
            <td><?php echo htmlspecialchars($c['name_ka']); ?></td>
            <td><?php echo htmlspecialchars($c['name_en']); ?></td>
            <td><?php echo htmlspecialchars($c['name_ru']); ?></td>
            <td class="table-actions">
                <a href="<?php echo BASE_URL; ?>/admin/categories.php?edit=<?php echo $c['id']; ?>" class="btn btn--ghost btn--sm">რედაქტირება</a>
                <form method="post" action="<?php echo BASE_URL; ?>/admin/category-delete.php" onsubmit="return confirm('წაშლისას ამ კატეგორიის პროდუქტებიც წაიშლება. გავაგრძელო?');" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                    <button type="submit" class="btn btn--danger btn--sm">წაშლა</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2 style="font-size:20px;"><?php echo $editId ? 'კატეგორიის რედაქტირება' : 'ახალი კატეგორია'; ?></h2>

<?php foreach ($errors as $err): ?><p class="admin-error"><?php echo htmlspecialchars($err); ?></p><?php endforeach; ?>

<form class="admin-form" method="post">
    <input type="hidden" name="id" value="<?php echo $editId ?? ''; ?>">
    <div class="form-row">
        <div class="form-group">
            <label>Slug (ლათინურად, მაგ. men)</label>
            <input type="text" name="slug" value="<?php echo htmlspecialchars($form['slug']); ?>" required>
        </div>
        <div class="form-group">
            <label>სახელი (KA)</label>
            <input type="text" name="name_ka" value="<?php echo htmlspecialchars($form['name_ka']); ?>" required>
        </div>
        <div class="form-group">
            <label>Name (EN)</label>
            <input type="text" name="name_en" value="<?php echo htmlspecialchars($form['name_en']); ?>">
        </div>
        <div class="form-group">
            <label>Название (RU)</label>
            <input type="text" name="name_ru" value="<?php echo htmlspecialchars($form['name_ru']); ?>">
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn--dark"><?php echo $editId ? 'შენახვა' : 'დამატება'; ?></button>
        <?php if ($editId): ?><a href="<?php echo BASE_URL; ?>/admin/categories.php" class="btn btn--ghost">გაუქმება</a><?php endif; ?>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>

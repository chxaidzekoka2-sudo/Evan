<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getDb();
$productId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$isEdit = $productId !== null;

$errors = [];
$product = [
    'sku' => '', 'category_id' => '', 'price' => '',
    'name_ka' => '', 'name_en' => '', 'name_ru' => '',
    'description_ka' => '', 'description_en' => '', 'description_ru' => '',
    'is_new' => 0, 'is_active' => 1,
];
$selectedSizes = []; // size_id => stock_qty
$existingImages = []; // [id, image_path]

if ($isEdit) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$productId]);
    $found = $stmt->fetch();
    if (!$found) {
        header('Location: ' . BASE_URL . '/admin/products.php');
        exit;
    }
    $product = $found;

    $sizeStmt = $pdo->prepare('SELECT size_id, stock_qty FROM product_sizes WHERE product_id = ?');
    $sizeStmt->execute([$productId]);
    foreach ($sizeStmt->fetchAll() as $row) {
        $selectedSizes[$row['size_id']] = $row['stock_qty'];
    }

    $imgStmt = $pdo->prepare('SELECT id, image_path FROM product_images WHERE product_id = ? ORDER BY sort_order');
    $imgStmt->execute([$productId]);
    $existingImages = $imgStmt->fetchAll();
}

$categories = $pdo->query('SELECT id, name_ka FROM categories ORDER BY sort_order')->fetchAll();
$allSizes = $pdo->query('SELECT id, label, type FROM sizes ORDER BY type, sort_order')->fetchAll();

// ------------------------------------------------------------------
// FORM SUBMIT
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product['sku']         = trim($_POST['sku'] ?? '');
    $product['category_id'] = (int)($_POST['category_id'] ?? 0);
    $product['price']       = (float)($_POST['price'] ?? 0);
    $product['name_ka']     = trim($_POST['name_ka'] ?? '');
    $product['name_en']     = trim($_POST['name_en'] ?? '');
    $product['name_ru']     = trim($_POST['name_ru'] ?? '');
    $product['description_ka'] = trim($_POST['description_ka'] ?? '');
    $product['description_en'] = trim($_POST['description_en'] ?? '');
    $product['description_ru'] = trim($_POST['description_ru'] ?? '');
    $product['is_new']      = isset($_POST['is_new']) ? 1 : 0;
    $product['is_active']   = isset($_POST['is_active']) ? 1 : 0;

    if ($product['sku'] === '' || $product['name_ka'] === '' || $product['category_id'] <= 0 || $product['price'] <= 0) {
        $errors[] = 'SKU, ქართული სახელი, კატეგორია და ფასი აუცილებელია.';
    }

    if (empty($errors)) {
        if ($isEdit) {
            $stmt = $pdo->prepare("
                UPDATE products SET sku=?, category_id=?, price=?,
                name_ka=?, name_en=?, name_ru=?,
                description_ka=?, description_en=?, description_ru=?,
                is_new=?, is_active=? WHERE id=?
            ");
            $stmt->execute([
                $product['sku'], $product['category_id'], $product['price'],
                $product['name_ka'], $product['name_en'], $product['name_ru'],
                $product['description_ka'], $product['description_en'], $product['description_ru'],
                $product['is_new'], $product['is_active'], $productId
            ]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO products (sku, category_id, price, name_ka, name_en, name_ru,
                    description_ka, description_en, description_ru, is_new, is_active)
                VALUES (?,?,?,?,?,?,?,?,?,?,?)
            ");
            $stmt->execute([
                $product['sku'], $product['category_id'], $product['price'],
                $product['name_ka'], $product['name_en'], $product['name_ru'],
                $product['description_ka'], $product['description_en'], $product['description_ru'],
                $product['is_new'], $product['is_active']
            ]);
            $productId = (int)$pdo->lastInsertId();
            $isEdit = true;
        }

        // --- ზომები/მარაგი ---
        $pdo->prepare('DELETE FROM product_sizes WHERE product_id = ?')->execute([$productId]);
        if (!empty($_POST['sizes']) && is_array($_POST['sizes'])) {
            $insSize = $pdo->prepare('INSERT INTO product_sizes (product_id, size_id, stock_qty) VALUES (?,?,?)');
            foreach ($_POST['sizes'] as $sizeId => $checked) {
                $qty = (int)($_POST['stock'][$sizeId] ?? 0);
                if ($qty > 0) {
                    $insSize->execute([$productId, (int)$sizeId, $qty]);
                }
            }
        }

        // --- არსებული ფოტოს წაშლა ---
        if (!empty($_POST['remove_images']) && is_array($_POST['remove_images'])) {
            $delImg = $pdo->prepare('SELECT image_path FROM product_images WHERE id = ? AND product_id = ?');
            $delStmt = $pdo->prepare('DELETE FROM product_images WHERE id = ? AND product_id = ?');
            foreach ($_POST['remove_images'] as $imgId) {
                $delImg->execute([(int)$imgId, $productId]);
                $row = $delImg->fetch();
                if ($row) {
                    $path = __DIR__ . '/../uploads/products/' . basename($row['image_path']);
                    if (is_file($path)) @unlink($path);
                }
                $delStmt->execute([(int)$imgId, $productId]);
            }
        }

        // --- ახალი ფოტოების ატვირთვა ---
        if (!empty($_FILES['images']['name'][0])) {
            $uploadDir = __DIR__ . '/../uploads/products/';
            $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
            $insImg = $pdo->prepare('INSERT INTO product_images (product_id, image_path, sort_order) VALUES (?,?,?)');
            foreach ($_FILES['images']['name'] as $i => $originalName) {
                if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
                $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowedExt, true)) continue;
                $filename = 'p' . $productId . '_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $uploadDir . $filename)) {
                    $insImg->execute([$productId, $filename, $i]);
                }
            }
        }

        header('Location: ' . BASE_URL . '/admin/products.php');
        exit;
    }

    // შეცდომის შემთხვევაში, ზომების refresh (POST-იდან)
    $selectedSizes = [];
    if (!empty($_POST['sizes'])) {
        foreach ($_POST['sizes'] as $sizeId => $checked) {
            $selectedSizes[$sizeId] = (int)($_POST['stock'][$sizeId] ?? 0);
        }
    }
}

$activeNav = 'products';
$pageTitle = $isEdit ? 'Edit Product' : 'New Product';
require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-topbar">
    <h1><?php echo $isEdit ? 'პროდუქტის რედაქტირება' : 'ახალი პროდუქტი'; ?></h1>
    <a href="<?php echo BASE_URL; ?>/admin/products.php" class="btn btn--ghost">← სიაში დაბრუნება</a>
</div>

<?php foreach ($errors as $err): ?>
    <p class="admin-error"><?php echo htmlspecialchars($err); ?></p>
<?php endforeach; ?>

<form class="admin-form" method="post" enctype="multipart/form-data">

    <div class="form-row">
        <div class="form-group">
            <label>SKU</label>
            <input type="text" name="sku" value="<?php echo htmlspecialchars($product['sku']); ?>" required>
        </div>
        <div class="form-group">
            <label>კატეგორია</label>
            <select name="category_id" required>
                <option value="">— აირჩიე —</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?php echo $c['id']; ?>" <?php echo $product['category_id'] == $c['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($c['name_ka']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>ფასი (GEL)</label>
            <input type="number" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($product['price']); ?>" required>
        </div>
    </div>

    <!-- მრავალენოვანი ველები -->
    <div class="lang-tabs">
        <button type="button" class="is-active" data-lang-tab="ka">ქართული</button>
        <button type="button" data-lang-tab="en">English</button>
        <button type="button" data-lang-tab="ru">Русский</button>
    </div>

    <div class="lang-panel is-active" data-lang-panel="ka">
        <div class="form-group" style="margin-bottom:14px;">
            <label>სახელი (KA)</label>
            <input type="text" name="name_ka" value="<?php echo htmlspecialchars($product['name_ka']); ?>" required>
        </div>
        <div class="form-group">
            <label>აღწერა (KA)</label>
            <textarea name="description_ka"><?php echo htmlspecialchars($product['description_ka']); ?></textarea>
        </div>
    </div>
    <div class="lang-panel" data-lang-panel="en">
        <div class="form-group" style="margin-bottom:14px;">
            <label>Name (EN)</label>
            <input type="text" name="name_en" value="<?php echo htmlspecialchars($product['name_en']); ?>">
        </div>
        <div class="form-group">
            <label>Description (EN)</label>
            <textarea name="description_en"><?php echo htmlspecialchars($product['description_en']); ?></textarea>
        </div>
    </div>
    <div class="lang-panel" data-lang-panel="ru">
        <div class="form-group" style="margin-bottom:14px;">
            <label>Название (RU)</label>
            <input type="text" name="name_ru" value="<?php echo htmlspecialchars($product['name_ru']); ?>">
        </div>
        <div class="form-group">
            <label>Описание (RU)</label>
            <textarea name="description_ru"><?php echo htmlspecialchars($product['description_ru']); ?></textarea>
        </div>
    </div>

    <!-- ზომები/მარაგი -->
    <div class="form-group" style="margin:22px 0 10px;">
        <label>ზომები და მარაგი (რაოდენობა 0 = არ არის მარაგში)</label>
    </div>
    <div class="size-stock-grid">
        <?php foreach ($allSizes as $s): ?>
            <label class="size-stock-item">
                <input type="checkbox" name="sizes[<?php echo $s['id']; ?>]" <?php echo isset($selectedSizes[$s['id']]) ? 'checked' : ''; ?>>
                <?php echo htmlspecialchars($s['label']); ?>
                <input type="number" name="stock[<?php echo $s['id']; ?>]" min="0" value="<?php echo $selectedSizes[$s['id']] ?? 0; ?>" placeholder="qty">
            </label>
        <?php endforeach; ?>
    </div>

    <!-- ფოტოები -->
    <div class="form-group" style="margin:22px 0 10px;">
        <label>ფოტოები</label>
    </div>
    <?php if ($existingImages): ?>
        <div class="existing-images">
            <?php foreach ($existingImages as $img): ?>
                <div class="image-item">
                    <img src="<?php echo BASE_URL; ?>/uploads/products/<?php echo htmlspecialchars($img['image_path']); ?>" alt="">
                    <button type="button" class="remove-image" data-image-id="<?php echo $img['id']; ?>" title="წაშლა">✕</button>
                    <input type="checkbox" name="remove_images[]" value="<?php echo $img['id']; ?>" class="remove-image-checkbox" style="display:none;">
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <div class="form-group">
        <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp">
        <span style="font-size:11px; opacity:.6;">JPG/PNG/WEBP, შეგიძლია რამდენიმეს ერთად ატვირთვა.</span>
    </div>

    <div class="checkbox-row" style="margin-top:20px;">
        <input type="checkbox" name="is_new" id="is_new" <?php echo $product['is_new'] ? 'checked' : ''; ?>>
        <label for="is_new" style="text-transform:none; letter-spacing:0;">მონიშნე როგორც "სიახლე"</label>
    </div>
    <div class="checkbox-row" style="margin-top:8px;">
        <input type="checkbox" name="is_active" id="is_active" <?php echo $product['is_active'] ? 'checked' : ''; ?>>
        <label for="is_active" style="text-transform:none; letter-spacing:0;">აქტიური (საიტზე გამოჩენა)</label>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn--dark"><?php echo $isEdit ? 'შენახვა' : 'შექმნა'; ?></button>
        <a href="<?php echo BASE_URL; ?>/admin/products.php" class="btn btn--ghost">გაუქმება</a>
    </div>
</form>

<script>
// ენის ტაბები
document.querySelectorAll('[data-lang-tab]').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('[data-lang-tab]').forEach(b => b.classList.remove('is-active'));
        document.querySelectorAll('[data-lang-panel]').forEach(p => p.classList.remove('is-active'));
        btn.classList.add('is-active');
        document.querySelector(`[data-lang-panel="${btn.dataset.langTab}"]`).classList.add('is-active');
    });
});

// ფოტოს წაშლის ღილაკი (მონიშნავს hidden checkbox-ს და მალავს ბარათს)
document.querySelectorAll('.remove-image').forEach(btn => {
    btn.addEventListener('click', () => {
        const item = btn.closest('.image-item');
        item.querySelector('.remove-image-checkbox').checked = true;
        item.style.opacity = '0.3';
        btn.disabled = true;
    });
});
</script>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>

<?php
/**
 * ====================================================================
 * admin/setup.php — ერთჯერადი wizard, პირველი admin ანგარიშის შესაქმნელად.
 *
 * გამოყენების შემდეგ ეს ფაილი აუცილებლად წაშალე ან გადაარქვი სერვერზე,
 * თორემ ნებისმიერს შეეძლება ახალი admin ანგარიშის შექმნა.
 * ====================================================================
 */
require_once __DIR__ . '/../config/db.php';
$pdo = getDb();

$existing = $pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();

$error = '';
$success = false;

if ($existing > 0) {
    $error = 'Admin ანგარიში უკვე არსებობს. წაშალე ეს ფაილი (admin/setup.php) უსაფრთხოებისთვის.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || strlen($password) < 8) {
        $error = 'მომხმარებლის სახელი აუცილებელია, პაროლი მინიმუმ 8 სიმბოლო.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO admin_users (username, password_hash) VALUES (?, ?)');
        $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="ka">
<head>
<meta charset="UTF-8">
<title>Evan Admin — Setup</title>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/admin/assets/admin.css">
</head>
<body class="admin-auth-page">
<div class="admin-auth-box">
    <h1>Admin Setup</h1>

    <?php if ($success): ?>
        <p class="admin-success">ანგარიში შეიქმნა! ახლა <a href="<?php echo BASE_URL; ?>/admin/login.php">შედი</a> და შემდეგ <strong>წაშალე ეს ფაილი</strong> (admin/setup.php) სერვერიდან.</p>
    <?php elseif ($error): ?>
        <p class="admin-error"><?php echo htmlspecialchars($error); ?></p>
        <?php if ($existing > 0): ?><a href="<?php echo BASE_URL; ?>/admin/login.php">შესვლის გვერდზე დაბრუნება</a><?php endif; ?>
    <?php endif; ?>

    <?php if ($existing == 0 && !$success): ?>
    <form method="post">
        <label>მომხმარებლის სახელი</label>
        <input type="text" name="username" required autofocus>
        <label>პაროლი (მინ. 8 სიმბოლო)</label>
        <input type="password" name="password" required minlength="8">
        <button type="submit" class="btn btn--dark btn--full">შექმნა</button>
    </form>
    <?php endif; ?>
</div>
</body>
</html>

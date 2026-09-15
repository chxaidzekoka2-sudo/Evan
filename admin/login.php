<?php
require_once __DIR__ . '/../config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!empty($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . '/admin/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT id, password_hash FROM admin_users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $username;
        header('Location: ' . BASE_URL . '/admin/index.php');
        exit;
    } else {
        $error = 'მომხმარებლის სახელი ან პაროლი არასწორია.';
    }
}
?>
<!DOCTYPE html>
<html lang="ka">
<head>
<meta charset="UTF-8">
<title>Evan Admin — Login</title>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/admin/assets/admin.css">
</head>
<body class="admin-auth-page">
<div class="admin-auth-box">
    <h1>Admin Login</h1>
    <?php if ($error): ?><p class="admin-error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
    <form method="post">
        <label>მომხმარებლის სახელი</label>
        <input type="text" name="username" required autofocus>
        <label>პაროლი</label>
        <input type="password" name="password" required>
        <button type="submit" class="btn btn--dark btn--full">შესვლა</button>
    </form>
</div>
</body>
</html>

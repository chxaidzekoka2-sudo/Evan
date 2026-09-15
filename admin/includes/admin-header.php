<?php
/**
 * გამოსაყენებელია ასე ყოველ admin გვერდზე:
 *   $activeNav = 'products'; // 'dashboard' | 'products' | 'categories' | 'settings'
 *   $pageTitle = 'Products';
 *   require_once __DIR__ . '/includes/auth.php';
 *   require_once __DIR__ . '/includes/admin-header.php';
 */
?>
<!DOCTYPE html>
<html lang="ka">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($pageTitle ?? 'Admin'); ?> — Evan Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600&family=Jost:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/admin/assets/admin.css">
</head>
<body>
<div class="admin-layout">
    <aside class="admin-sidebar">
        <span class="logo">Evan Admin</span>
        <nav>
            <a href="<?php echo BASE_URL; ?>/admin/index.php" class="<?php echo ($activeNav ?? '') === 'dashboard' ? 'is-active' : ''; ?>">დეშბორდი</a>
            <a href="<?php echo BASE_URL; ?>/admin/products.php" class="<?php echo ($activeNav ?? '') === 'products' ? 'is-active' : ''; ?>">პროდუქტები</a>
            <a href="<?php echo BASE_URL; ?>/admin/categories.php" class="<?php echo ($activeNav ?? '') === 'categories' ? 'is-active' : ''; ?>">კატეგორიები</a>
            <a href="<?php echo BASE_URL; ?>/admin/orders.php" class="<?php echo ($activeNav ?? '') === 'orders' ? 'is-active' : ''; ?>">შეკვეთები</a>
            <a href="<?php echo BASE_URL; ?>/admin/messages.php" class="<?php echo ($activeNav ?? '') === 'messages' ? 'is-active' : ''; ?>">შეტყობინებები</a>
            <a href="<?php echo BASE_URL; ?>/admin/settings.php" class="<?php echo ($activeNav ?? '') === 'settings' ? 'is-active' : ''; ?>">პარამეტრები</a>
        </nav>
        <a href="<?php echo BASE_URL; ?>/admin/logout.php" class="logout-link">გასვლა (<?php echo htmlspecialchars($_SESSION['admin_username'] ?? ''); ?>)</a>
    </aside>
    <main class="admin-main">

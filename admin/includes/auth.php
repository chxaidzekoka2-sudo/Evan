<?php
/**
 * ====================================================================
 * admin/includes/auth.php — session-based auth guard.
 * ყოველ დაცულ admin გვერდზე ზემოთ ჩასვი:
 *   require_once __DIR__ . '/includes/auth.php';
 * ====================================================================
 */
require_once __DIR__ . '/../../config/db.php'; // BASE_URL-ისთვის

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}

<?php
require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/contact.php');
    exit;
}

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name !== '' && $email !== '' && $message !== '') {
    $pdo = getDb();
    $stmt = $pdo->prepare('INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)');
    $stmt->execute([$name, $email, $message]);
}

header('Location: ' . BASE_URL . '/contact.php?sent=1');
exit;

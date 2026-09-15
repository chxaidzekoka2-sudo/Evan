<?php
require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$redirectTo = $_SERVER['HTTP_REFERER'] ?? '/index.php';
$separator = (strpos($redirectTo, '?') === false) ? '?' : '&';

if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $pdo = getDb();
    try {
        $stmt = $pdo->prepare('INSERT INTO newsletter_subscribers (email) VALUES (?)');
        $stmt->execute([$email]);
    } catch (PDOException $e) {
        // duplicate email (უკვე გამოწერილი) — არაფერი გამონაკლისი, უბრალოდ ჩავთვალოთ წარმატებულად
    }
    header('Location: ' . $redirectTo . $separator . 'subscribed=1');
    exit;
}

header('Location: ' . $redirectTo . $separator . 'subscribed=0');
exit;

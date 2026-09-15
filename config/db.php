<?php
/**
 * ====================================================================
 * config/db.php — PDO კავშირი მონაცემთა ბაზასთან.
 *
 * XAMPP-ის default პარამეტრები: host=localhost, user=root, pass='' (ცარიელი).
 * cPanel-ზე დეპლოისას (Kalipso-ს მსგავსად), უბრალოდ შეცვალე DB_USER/DB_PASS/DB_NAME
 * cPanel-ის მიერ მოცემულით (ჩვეულებრივ ფორმატია cpaneluser_dbname).
 * ====================================================================
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'evan_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

function getDb(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Database connection failed']);
            exit;
        }
    }
    return $pdo;
}

/**
 * BASE_URL — თუ საიტი დომენის root-ზეა (მაგ. localhost/ ან production
 * domain root), დატოვე ცარიელი: ''.
 * თუ საიტი ქვედირექტორიაშია (მაგ. localhost/evan/), დააყენე '/evan'
 * (წინ ხაზი, ბოლოში არა).
 */
define('BASE_URL', '/evan');

/**
 * მიმდინარე ენის დადგენა request-იდან (?lang=ka/en/ru), ka-ს default-ად.
 * ეს ვალიდაცია ინახავს, რომ name_{$lang} column-ის სახელი ყოველთვის
 * ერთ-ერთი ცნობილი, უსაფრთხო მნიშვნელობა იყოს (არასდროს პირდაპირ
 * მომხმარებლის input-ი არ მიდის SQL column სახელში დაუვალიდებლად).
 */
function currentLang(): string {
    $allowed = ['ka', 'en', 'ru'];
    $lang = $_GET['lang'] ?? 'ka';
    return in_array($lang, $allowed, true) ? $lang : 'ka';
}

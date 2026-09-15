<?php
/**
 * ====================================================================
 * config/bog.php — Bank of Georgia Online Payment API-ის კონფიგურაცია.
 *
 * client_id/client_secret მიიღებ businessmanager.bog.ge-ზე ვირტუალური
 * POS-ის გააქტიურების შემდეგ. ეს არასდროს არ უნდა აიტვირთოს public
 * repo-ში — production-ზე გამოიყენე environment variable, არა ეს ფაილი.
 * ====================================================================
 */

/**
 * BOG_TEST_MODE — true მანამ, სანამ არ გაქვს რეალური BOG ბიზნეს
 * ანგარიში + HTTPS დომენი. ამ რეჟიმში api/create-order.php საერთოდ
 * არ უკავშირდება BOG-ს — პირდაპირ ინიშნავს შეკვეთას "paid"-ად და
 * გადაგამისამართებს checkout-success.php-ზე, რომ მთელი checkout-ის
 * ნაკადი (ბაზა, მარაგის კლება, admin-ის orders გვერდი) ლოკალურად
 * გატესტო ნამდვილი ბარათის/დომენის გარეშე.
 *
 * production-ზე გადასვლისას (რეალური client_id/secret + HTTPS დომენი
 * უკვე გექნება) აუცილებლად შეცვალე false-ზე.
 */
define('BOG_TEST_MODE', true);

define('BOG_CLIENT_ID', 'YOUR_CLIENT_ID');
define('BOG_CLIENT_SECRET', 'YOUR_CLIENT_SECRET');

define('BOG_AUTH_URL', 'https://oauth2.bog.ge/auth/realms/bog/protocol/openid-connect/token');
define('BOG_ORDER_URL', 'https://api.bog.ge/payments/v1/ecommerce/orders');

// შენი საიტის საჯარო (HTTPS) მისამართი — cPanel-ზე დეპლოისას შეცვალე
// namely production დომენზე. localhost-ზე callback ვერ იმუშავებს
// (BOG ვერ მიაწვდენს POST-ს localhost-ს) — ტესტისთვის ngrok ან real
// server გჭირდება.
define('SITE_BASE_URL', 'https://your-domain.ge');

// BOG-ის public key, callback-ის ხელმოწერის შესამოწმებლად (SHA256withRSA)
define('BOG_CALLBACK_PUBLIC_KEY', "-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAu4RUyAw3+CdkS3ZNILQh
zHI9Hemo+vKB9U2BSabppkKjzjjkf+0Sm76hSMiu/HFtYhqWOESryoCDJoqffY0Q
1VNt25aTxbj068QNUtnxQ7KQVLA+pG0smf+EBWlS1vBEAFbIas9d8c9b9sSEkTrr
TYQ90WIM8bGB6S/KLVoT1a7SnzabjoLc5Qf/SLDG5fu8dH8zckyeYKdRKSBJKvhx
tcBuHV4f7qsynQT+f2UYbESX/TLHwT5qFWZDHZ0YUOUIvb8n7JujVSGZO9/+ll/g
4ZIWhC1MlJgPObDwRkRd8NFOopgxMcMsDIZIoLbWKhHVq67hdbwpAq9K9WMmEhPn
PwIDAQAB
-----END PUBLIC KEY-----");

/**
 * OAuth2 access token-ის მიღება (client_credentials grant).
 * @return string|null
 */
function bogGetAccessToken(): ?string {
    $ch = curl_init(BOG_AUTH_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_USERPWD => BOG_CLIENT_ID . ':' . BOG_CLIENT_SECRET,
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_POSTFIELDS => http_build_query(['grant_type' => 'client_credentials']),
    ]);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status !== 200) return null;
    $data = json_decode($response, true);
    return $data['access_token'] ?? null;
}

/**
 * BOG-ში e-commerce შეკვეთის შექმნა.
 * @param array $order      { id, total_amount, currency, external_order_id }
 * @param array $basketItems [{ product_id, description, quantity, unit_price }]
 * @return array|null       { order_id (BOG-ის), redirect_url } ან null შეცდომისას
 */
function bogCreateOrder(string $accessToken, array $order, array $basketItems): ?array {
    $payload = [
        'callback_url' => SITE_BASE_URL . '/payment-callback.php',
        'external_order_id' => (string)$order['external_order_id'],
        'purchase_units' => [
            'currency' => $order['currency'] ?? 'GEL',
            'total_amount' => (float)$order['total_amount'],
            'basket' => $basketItems,
        ],
        'redirect_urls' => [
            'success' => SITE_BASE_URL . '/checkout-success.php?order=' . $order['external_order_id'],
            'fail' => SITE_BASE_URL . '/checkout-fail.php?order=' . $order['external_order_id'],
        ],
    ];

    $ch = curl_init(BOG_ORDER_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Accept-Language: ka',
        ],
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
    ]);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status !== 200 && $status !== 201) {
        error_log('BOG create order failed: ' . $response);
        return null;
    }

    $data = json_decode($response, true);
    if (empty($data['_links']['redirect']['href'])) return null;

    return [
        'bog_order_id' => $data['id'],
        'redirect_url' => $data['_links']['redirect']['href'],
    ];
}

/**
 * Callback-ის ხელმოწერის ვალიდაცია.
 * @param string $rawBody       raw POST body (json ჩარევის/parse-ის ᲬᲘᲜᲐᲗ)
 * @param string $signatureB64  Callback-Signature header-ის მნიშვნელობა
 */
function bogVerifyCallbackSignature(string $rawBody, string $signatureB64): bool {
    $signature = base64_decode($signatureB64);
    $publicKey = openssl_pkey_get_public(BOG_CALLBACK_PUBLIC_KEY);
    if ($publicKey === false) return false;

    $result = openssl_verify($rawBody, $signature, $publicKey, OPENSSL_ALGO_SHA256);
    return $result === 1;
}

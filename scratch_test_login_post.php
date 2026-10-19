<?php
/**
 * Full browser simulation: GET login → POST login → follow redirect to admin dashboard.
 * Uses a temp file-based cookie jar so cookies persist across requests.
 */

$cookieFile = tempnam(sys_get_temp_dir(), 'lv_cookies_');
file_put_contents($cookieFile, ''); // initialize

function doRequest(string $url, array $opts = [], string $cookieFile = ''): array
{
    $ch = curl_init();
    $defaults = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_COOKIEFILE     => $cookieFile,
        CURLOPT_COOKIEJAR      => $cookieFile,
        CURLOPT_HTTPHEADER     => [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/130.0.0.0',
            'Accept: text/html,application/xhtml+xml',
            'Accept-Language: es-ES,es;q=0.9',
        ],
    ];
    curl_setopt_array($ch, array_replace($defaults, $opts, [CURLOPT_URL => $url]));
    $raw = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);

    $headers = substr($raw, 0, $headerSize);
    $body    = substr($raw, $headerSize);

    preg_match('/^Location:\s*(.+)$/im', $headers, $locM);
    $location = isset($locM[1]) ? trim($locM[1]) : null;

    preg_match('/<title[^>]*>([^<]+)<\/title>/i', $body, $titleM);
    $title = $titleM[1] ?? null;

    return ['code' => $code, 'location' => $location, 'body' => $body, 'title' => $title, 'headers' => $headers];
}

// ────────────────────────────────────────────────────────────────────────
// Step 1: GET /login
// ────────────────────────────────────────────────────────────────────────
$base = 'http://localhost/mi-web/public';
$r1 = doRequest("$base/login", [], $cookieFile);
echo "STEP 1 GET /login → HTTP {$r1['code']}\n";
echo "  Title: {$r1['title']}\n";

// Extract CSRF token
preg_match('/name="_token"\s+value="([^"]+)"/', $r1['body'], $tm);
$token = $tm[1] ?? null;
echo "  _token: " . ($token ? 'FOUND (' . strlen($token) . ' chars)' : 'NOT FOUND') . "\n";

if (!$token) {
    echo "  ERROR: No CSRF token found. Body snippet:\n";
    echo substr($r1['body'], 0, 1000) . "\n";
    @unlink($cookieFile);
    exit(1);
}

// ────────────────────────────────────────────────────────────────────────
// Step 2: POST /login
// ────────────────────────────────────────────────────────────────────────
$r2 = doRequest("$base/login", [
    CURLOPT_POST       => true,
    CURLOPT_POSTFIELDS => http_build_query([
        '_token'   => $token,
        'login'    => 'admin',
        'password' => '1234',
    ]),
    CURLOPT_HTTPHEADER => [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/130.0.0.0',
        'Content-Type: application/x-www-form-urlencoded',
        'Referer: ' . "$base/login",
        'Origin: http://localhost',
    ],
], $cookieFile);

echo "\nSTEP 2 POST /login → HTTP {$r2['code']}\n";
echo "  Location: " . ($r2['location'] ?? 'NONE') . "\n";
echo "  Title: " . ($r2['title'] ?? 'none') . "\n";

// Show validation errors
preg_match_all('/<p[^>]*class="[^"]*(?:text-rose|error)[^"]*"[^>]*>(.*?)<\/p>/is', $r2['body'], $errM);
if (!empty($errM[1])) {
    foreach ($errM[1] as $e) {
        echo "  ❌ Error: " . strip_tags(trim($e)) . "\n";
    }
}

// ────────────────────────────────────────────────────────────────────────
// Step 3: Follow redirect if 302
// ────────────────────────────────────────────────────────────────────────
if ($r2['code'] === 302 && $r2['location']) {
    $loc = $r2['location'];
    // Make absolute if relative
    if (!str_starts_with($loc, 'http')) {
        $loc = 'http://localhost' . $loc;
    }
    echo "\nSTEP 3 GET $loc → ";
    $r3 = doRequest($loc, [], $cookieFile);
    echo "HTTP {$r3['code']}\n";
    echo "  Title: " . ($r3['title'] ?? 'none') . "\n";
    echo "  Location: " . ($r3['location'] ?? 'NONE') . "\n";

    // Check for PHP errors
    if (str_contains($r3['body'], 'Illuminate\\') || str_contains($r3['body'], 'Exception') || str_contains($r3['body'], 'Error:')) {
        echo "\n  ⚠️  PHP/Laravel error detected:\n";
        // Extract error message
        preg_match('/<title[^>]*>([^<]+)<\/title>/i', $r3['body'], $errTitle);
        preg_match('/<div[^>]*class="[^"]*exception-message[^"]*"[^>]*>(.*?)<\/div>/is', $r3['body'], $excM);
        echo "  " . ($errTitle[1] ?? '') . "\n";
        echo "  " . strip_tags($excM[1] ?? '') . "\n";
        echo "\n  Full body (first 2000 chars):\n";
        echo substr($r3['body'], 0, 2000) . "\n";
    } else {
        // Follow another redirect if needed
        if ($r3['code'] === 302 && $r3['location']) {
            $loc2 = $r3['location'];
            if (!str_starts_with($loc2, 'http')) {
                $loc2 = 'http://localhost' . $loc2;
            }
            echo "\nSTEP 4 GET $loc2 → ";
            $r4 = doRequest($loc2, [], $cookieFile);
            echo "HTTP {$r4['code']}\n";
            echo "  Title: " . ($r4['title'] ?? 'none') . "\n";
            echo "  Success: " . (str_contains($r4['body'], 'Dashboard') || str_contains($r4['body'], 'Bienvenido') ? '✅ YES' : '❓ check body') . "\n";
            echo "  Body preview: " . substr(strip_tags($r4['body']), 0, 500) . "\n";
        } else {
            echo "  Body preview: " . substr(strip_tags($r3['body']), 0, 500) . "\n";
        }
    }
} elseif ($r2['code'] === 419) {
    echo "\n  ⚠️  CSRF TOKEN MISMATCH (419). Session/Cookie issue.\n";
    echo "  Headers returned:\n";
    echo $r2['headers'] . "\n";
} elseif ($r2['code'] === 200) {
    echo "\n  Stayed on login page (validation error or redirect failed):\n";
    // Show any errors visible
    preg_match_all('/class="[^"]*text-rose[^"]*"[^>]*>\s*(.*?)\s*<\/p>/is', $r2['body'], $errM2);
    foreach ($errM2[1] as $e) {
        echo "  ❌ " . strip_tags(trim($e)) . "\n";
    }
    echo "  Body: " . substr(strip_tags($r2['body']), 0, 1000) . "\n";
}

@unlink($cookieFile);

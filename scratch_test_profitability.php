<?php
/**
 * End-to-end test of the profitability module HTTP interface.
 * Tests: index page, create page, simulator page.
 */

$cookieFile = tempnam(sys_get_temp_dir(), 'lv_prof_');
file_put_contents($cookieFile, '');
$base = 'http://localhost/mi-web/public';

function req(string $url, array $curlOpts = [], string $cookieFile = ''): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_COOKIEFILE     => $cookieFile,
        CURLOPT_COOKIEJAR      => $cookieFile,
        CURLOPT_HTTPHEADER     => [
            'User-Agent: Mozilla/5.0 Chrome/130',
            'Accept: text/html',
        ],
    ] + $curlOpts);
    $raw      = curl_exec($ch);
    $code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $hdrSize  = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);
    $headers  = substr($raw, 0, $hdrSize);
    $body     = substr($raw, $hdrSize);
    preg_match('/^Location:\s*(.+)$/im', $headers, $locM);
    preg_match('/<title[^>]*>([^<]+)<\/title>/i', $body, $titleM);
    return [
        'code'     => $code,
        'location' => isset($locM[1]) ? trim($locM[1]) : null,
        'title'    => $titleM[1] ?? null,
        'body'     => $body,
    ];
}

// ── 1. Login ──────────────────────────────────────────────────────
$r = req("$base/login", [], $cookieFile);
preg_match('/name="_token"\s+value="([^"]+)"/', $r['body'], $tm);
$token = $tm[1] ?? null;
echo "[1] GET /login → HTTP {$r['code']}, token: " . ($token ? 'OK' : 'MISSING') . "\n";

$r2 = req("$base/login", [
    CURLOPT_POST       => true,
    CURLOPT_POSTFIELDS => http_build_query(['_token' => $token, 'login' => 'admin', 'password' => '1234']),
    CURLOPT_HTTPHEADER => ['User-Agent: Mozilla/5.0', 'Content-Type: application/x-www-form-urlencoded', 'Referer: ' . "$base/login"],
], $cookieFile);
echo "[2] POST /login → HTTP {$r2['code']}, Location: {$r2['location']}\n";

// Follow redirect
if ($r2['code'] === 302) {
    $loc = $r2['location'];
    if (!str_starts_with($loc, 'http')) $loc = 'http://localhost' . $loc;
    $r3 = req($loc, [], $cookieFile);
    echo "[3] GET $loc → HTTP {$r3['code']}, Title: {$r3['title']}\n";
}

// ── 2. Profitability index ──────────────────────────────────────────
$rp = req("$base/profitability", [], $cookieFile);
echo "\n[4] GET /profitability → HTTP {$rp['code']}, Title: {$rp['title']}\n";
if ($rp['code'] !== 200) {
    echo "  Body: " . substr(strip_tags($rp['body']), 0, 500) . "\n";
}

// ── 3. Create form ──────────────────────────────────────────────────
$rc = req("$base/profitability/create", [], $cookieFile);
echo "[5] GET /profitability/create → HTTP {$rc['code']}, Title: {$rc['title']}\n";
if ($rc['code'] !== 200) {
    echo "  Body: " . substr(strip_tags($rc['body']), 0, 500) . "\n";
}

// ── 4. Simulator page ──────────────────────────────────────────────
$rs = req("$base/profitability/simulator", [], $cookieFile);
echo "[6] GET /profitability/simulator → HTTP {$rs['code']}, Title: {$rs['title']}\n";
if ($rs['code'] !== 200) {
    echo "  Body: " . substr(strip_tags($rs['body']), 0, 500) . "\n";
}

// ── 5. Live simulate AJAX ──────────────────────────────────────────
// Get CSRF
preg_match('/name="_token"\s+value="([^"]+)"/', $rc['body'], $tm2);
$token2 = $tm2[1] ?? $token;

$simData = http_build_query([
    '_token' => $token2,
    'container_id' => 'TEST-01',
    'arrival_date' => date('Y-m-d'),
    'transitaria' => 'ENCI',
    'kg_occidente' => 5000,
    'kg_centro' => 3000,
    'kg_oriente' => 4000,
]);

$rl = req("$base/profitability/simulate-live", [
    CURLOPT_POST       => true,
    CURLOPT_POSTFIELDS => $simData,
    CURLOPT_HTTPHEADER => [
        'User-Agent: Mozilla/5.0',
        'Content-Type: application/x-www-form-urlencoded',
        'X-Requested-With: XMLHttpRequest',
        'Accept: application/json',
        'Referer: ' . "$base/profitability/simulator",
    ],
], $cookieFile);
echo "\n[7] POST /profitability/simulate-live → HTTP {$rl['code']}\n";
$json = json_decode($rl['body'], true);
if ($json && isset($json['verdict'])) {
    echo "  ✅ Verdict: {$json['verdict']}\n";
    echo "  Revenue: \${$json['revenue']['total_usd']} USD\n";
    echo "  Net Profit: \${$json['profit']['net_profit_usd']} USD\n";
    echo "  Net Margin: {$json['profit']['net_margin_pct']}%\n";
    echo "  CM: \${$json['margins']['contribution_margin_usd']} USD\n";
} else {
    echo "  Response: " . substr($rl['body'], 0, 600) . "\n";
}

@unlink($cookieFile);
echo "\n✅ All tests completed.\n";

<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Check tables
echo "=== TABLES ===\n";
$tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public' ORDER BY tablename");
foreach ($tables as $t) {
    echo "  " . $t->tablename . "\n";
}

// Check sessions table
echo "\n=== SESSIONS TABLE EXISTS: " . (Schema::hasTable('sessions') ? 'YES' : 'NO') . " ===\n";

// Check session count
if (Schema::hasTable('sessions')) {
    $count = DB::table('sessions')->count();
    echo "Session rows: $count\n";
}

// Check config
echo "\n=== SESSION CONFIG ===\n";
echo "driver: " . config('session.driver') . "\n";
echo "domain: " . (config('session.domain') ?: 'NULL') . "\n";
echo "path: " . config('session.path') . "\n";
echo "secure: " . (config('session.secure') ? 'true' : 'false') . "\n";
echo "same_site: " . config('session.same_site') . "\n";
echo "cookie: " . config('session.cookie') . "\n";

echo "\n=== APP CONFIG ===\n";
echo "url: " . config('app.url') . "\n";
echo "env: " . config('app.env') . "\n";

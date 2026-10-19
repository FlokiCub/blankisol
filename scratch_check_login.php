<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== USER CHECK ===\n";
$users = User::orderBy('username')->get(['id','username','email','role','is_active','password']);
foreach ($users as $u) {
    $pass1234 = Hash::check('1234', $u->password) ? 'OK' : 'FAIL';
    echo sprintf(
        "%-20s | %-30s | %-20s | active:%-5s | pass1234:%s\n",
        $u->username,
        $u->email,
        is_string($u->role) ? $u->role : $u->role->value,
        $u->is_active ? 'YES' : 'NO',
        $pass1234
    );
}

echo "\n=== DASHBOARD ROUTES ===\n";
$routes = app('router')->getRoutes();
foreach (['dashboard','admin.dashboard','login'] as $name) {
    try {
        $url = route($name);
        echo "$name => $url\n";
    } catch (\Exception $e) {
        echo "$name => NOT FOUND\n";
    }
}

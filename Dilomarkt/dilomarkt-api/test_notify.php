<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Notifications\VerifyEmailNotification;

// Make a temporary fake user (not saved)
$user = new User();
$user->email = 'ebosak322@gmail.com';
$user->name  = 'Test User';

try {
    $user->notify(new VerifyEmailNotification('123456'));
    echo "SUCCESS: Notification sent via notify()\n";
} catch (\Exception $e) {
    echo "FAILED via notify(): " . get_class($e) . "\n";
    echo $e->getMessage() . "\n";
}

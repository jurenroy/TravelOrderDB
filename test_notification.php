<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use App\Http\Controllers\LeaveFormController;

$app = require_once 'bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

$controller = new LeaveFormController();
$result = $controller->sendNotification('Test', 'Message', true);

echo "Notification sent: " . ($result ? 'Success' : 'Failed') . "\n";

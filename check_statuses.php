<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$statuses = App\Models\ProjectStatus::all();
foreach ($statuses as $status) {
    echo "ID: " . $status->ProjectStatusID . " - Name: '" . $status->StatusName . "'\n";
}













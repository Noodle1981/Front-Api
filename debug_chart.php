<?php

use App\Models\ApiLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$range = '7d';
$endDate = Carbon::today();
$startDate = Carbon::today()->subDays(6);

echo "Start Date: " . $startDate->toDateTimeString() . "\n";
echo "End Date: " . $endDate->toDateTimeString() . "\n";

$msgLogs = ApiLog::whereBetween('happened_at', [$startDate->startOfDay(), $endDate->endOfDay()])->get();

echo "Total Logs Found: " . $msgLogs->count() . "\n";

foreach($msgLogs as $log) {
    echo "Log ID: {$log->id} - Status: {$log->status} - Date: {$log->happened_at->format('Y-m-d H:i:s')} \n";
}

// Mimic Grouping
$dates = [];
$period = \Carbon\CarbonPeriod::create($startDate, $endDate);
foreach ($period as $dt) {
    $dateStr = $dt->format('Y-m-d');
    echo "Checking Date: $dateStr \n";
    
    $success = $msgLogs->filter(function($log) use ($dateStr) {
        return $log->happened_at->format('Y-m-d') === $dateStr && $log->status === 'success';
    })->count();
    
    $error = $msgLogs->filter(function($log) use ($dateStr) {
        return $log->happened_at->format('Y-m-d') === $dateStr && $log->status === 'error';
    })->count();
    
    echo "  Success: $success, Error: $error \n";
}

<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$batch = App\Models\WorkflowFileBatch::find(8);
if ($batch) {
    echo "ID: " . $batch->id . "\n";
    echo "Created At: " . $batch->created_at . "\n";
    echo "Metadata: " . json_encode($batch->files_metadata) . "\n";
} else {
    echo "Batch 8 not found\n";
}

echo "Current Server Time: " . now() . "\n";
echo "Config Timezone: " . config('app.timezone') . "\n";

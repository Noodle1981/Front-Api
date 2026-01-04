<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$count = App\Models\WorkflowUploadedFile::where('workflow_file_batch_id', 8)->count();
echo "Files for Batch 8: " . $count . "\n";

if ($count > 0) {
    $files = App\Models\WorkflowUploadedFile::where('workflow_file_batch_id', 8)->get();
    foreach ($files as $file) {
        echo "- " . $file->original_filename . "\n";
    }
}

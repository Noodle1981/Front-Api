<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Workflow Storage Path
    |--------------------------------------------------------------------------
    |
    | This value determines the path where uploaded workflow files will be stored.
    | The path is relative to the storage/app directory.
    |
    */

    'storage_path' => env('WORKFLOW_STORAGE_PATH', 'workflows'),

    /*
    |--------------------------------------------------------------------------
    | Maximum File Size
    |--------------------------------------------------------------------------
    |
    | Maximum file size allowed for uploads in megabytes (MB).
    |
    */

    'max_file_size' => env('WORKFLOW_MAX_FILE_SIZE', 10),

    /*
    |--------------------------------------------------------------------------
    | Allowed File Extensions
    |--------------------------------------------------------------------------
    |
    | File extensions that are allowed for workflow file uploads.
    |
    */

    'allowed_extensions' => ['xlsx', 'xls', 'csv'],

    /*
    |--------------------------------------------------------------------------
    | Python API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the external Python API that processes workflow rules.
    |
    */

    'python_api' => [
        'url' => env('WORKFLOW_PYTHON_API_URL', 'http://localhost:8000/api/execute'),
        'timeout' => env('WORKFLOW_PYTHON_API_TIMEOUT', 60),
        'use_mock' => env('WORKFLOW_USE_MOCK_API', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Batch Code Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for generating batch codes.
    |
    */

    'batch_code' => [
        'prefix' => 'WF',
        'length' => 6,
    ],

];

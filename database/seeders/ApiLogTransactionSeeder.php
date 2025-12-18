<?php

namespace Database\Seeders;

use App\Models\ApiLog;
use App\Models\ApiService;
use App\Models\Client;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ApiLogTransactionSeeder extends Seeder
{
    public function run()
    {
        $client = Client::first();
        $mpService = ApiService::where('slug', 'mercado-pago')->first();
        
        if (!$client || !$mpService) {
            $this->command->info('No client or service found properly. skipping.');
            return;
        }

        // 1. Create Fake Logs (Technical Data)
        $this->command->info('Creating API Logs...');
        
        // Success Log
        ApiLog::create([
            'client_id' => $client->id,
            'api_service_id' => $mpService->id,
            'status' => 'success',
            'event_type' => 'Sync Ventas',
            'details' => json_encode(['processed' => 15, 'time_ms' => 450]),
            'happened_at' => Carbon::now()->subMinutes(10),
        ]);

        // Error Log (with JSON payload)
        ApiLog::create([
            'client_id' => $client->id,
            'api_service_id' => $mpService->id,
            'status' => 'error',
            'event_type' => 'Auth Failed',
            'details' => json_encode([
                'error' => 'invalid_token', 
                'message' => 'The access token has expired',
                'request_id' => 'req_12345abcdef'
            ]),
            'happened_at' => Carbon::now()->subMinutes(30),
        ]);

        // 2. Create Fake Transactions (Financial Data)
        $this->command->info('Creating Transactions...');
        
        Transaction::create([
            'client_id' => $client->id,
            'api_service_id' => $mpService->id,
            'date_at' => Carbon::now()->subDays(1),
            'amount' => 15000.50,
            'currency' => 'ARS',
            'type' => 'income',
            'description' => 'Cobro MP - Ventas del Dia',
            'status' => 'verified',
            'raw_data' => ['payment_id' => 999111, 'fee' => 500],
        ]);

        Transaction::create([
            'client_id' => $client->id,
            'api_service_id' => $mpService->id,
            'date_at' => Carbon::now()->subDays(2),
            'amount' => 4500.00,
            'currency' => 'ARS',
            'type' => 'expense',
            'description' => 'Comisión MercadoLibre',
            'status' => 'verified',
            'raw_data' => ['ref' => 'COM-2023'],
        ]);
    }
}

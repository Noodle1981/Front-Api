<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ResetDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-demo {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the database and seed with complete demo data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->laravel->environment('production') && !$this->option('force')) {
            if (!$this->confirm('This will wipe all database data. Do you wish to continue?')) {
                return;
            }
        }

        $this->info('🚀 Starting Demo Reset Process...');

        // 1. Migrate Fresh
        $this->info('1️⃣  Cleaning database (migrate:fresh)...');
        Artisan::call('migrate:fresh', [], $this->output);

        // 2. RolePermissionSeeder
        $this->info('2️⃣  Seeding Roles & Permissions...');
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder'], $this->output);

        // 3. Workflow Types
        $this->info('Seeding Workflow Types...');
        Artisan::call('db:seed', ['--class' => 'WorkflowTypeSeeder'], $this->output);

        // 3. ApiServiceSeeder - REMOVED (no longer needed in minimal version)
        // $this->info('Seeding API Services...');
        // Artisan::call('db:seed', ['--class' => 'ApiServiceSeeder'], $this->output);

        // 4. CompleteDemoSeeder
        $this->info('4️⃣  Generating Complete Demo Data (Users, Clients, transactions)...');
        Artisan::call('db:seed', ['--class' => 'CompleteDemoSeeder'], $this->output);

        $this->newLine();
        $this->info('🎉 Demo Reset Complete!');

        $this->table(
            ['Role', 'Email', 'Password'],
            [
                ['Super Admin', 'admin@example.com', 'password'],
                ['Programador', 'analista@example.com', 'password'],
                ['Operador (Demo)', 'user@example.com', 'password'],
                ['Operador (Alt)', 'maria.gonzalez@demo.com', 'password123'],
            ]
        );
    }
}

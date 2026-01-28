<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('workflow_file_batches', function (Blueprint $table) {
            // SQLite doesn't support modifying columns directly, so we need to handle this differently
            // For MySQL/PostgreSQL, we would use: $table->foreignId('branch_id')->nullable()->change();
        });

        // For SQLite compatibility, we recreate the constraint
        if (config('database.default') === 'sqlite') {
            // SQLite requires table recreation for column modification
            // We'll use raw SQL to work around this
            DB::statement('PRAGMA foreign_keys=off;');

            DB::statement('
                CREATE TABLE workflow_file_batches_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    workflow_type_id INTEGER NOT NULL,
                    client_id INTEGER NOT NULL,
                    branch_id INTEGER NULL,
                    user_id INTEGER NOT NULL,
                    batch_code VARCHAR(50) UNIQUE,
                    status VARCHAR(20) DEFAULT "pending",
                    validation_errors TEXT,
                    uploaded_at TIMESTAMP,
                    validated_at TIMESTAMP,
                    workflow_request_id INTEGER NULL,
                    files_count INTEGER DEFAULT 0,
                    total_rows INTEGER DEFAULT 0,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP,
                    FOREIGN KEY (workflow_type_id) REFERENCES workflow_types(id) ON DELETE CASCADE,
                    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
                    FOREIGN KEY (branch_id) REFERENCES clients(id) ON DELETE CASCADE,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (workflow_request_id) REFERENCES workflow_requests(id) ON DELETE SET NULL
                );
            ');

            DB::statement('
                INSERT INTO workflow_file_batches_new
                SELECT id, workflow_type_id, client_id, branch_id, user_id, batch_code, status,
                       validation_errors, uploaded_at, validated_at, workflow_request_id,
                       files_count, total_rows, created_at, updated_at
                FROM workflow_file_batches;
            ');

            DB::statement('DROP TABLE workflow_file_batches;');
            DB::statement('ALTER TABLE workflow_file_batches_new RENAME TO workflow_file_batches;');

            // Recreate indexes
            DB::statement('CREATE INDEX workflow_file_batches_client_id_branch_id_index ON workflow_file_batches(client_id, branch_id);');
            DB::statement('CREATE INDEX workflow_file_batches_status_index ON workflow_file_batches(status);');

            DB::statement('PRAGMA foreign_keys=on;');
        } else {
            // For MySQL/PostgreSQL
            Schema::table('workflow_file_batches', function (Blueprint $table) {
                $table->unsignedBigInteger('branch_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: Reversing this would require setting a default branch_id for existing records
        // which may not be possible. This migration is effectively one-way.
    }
};

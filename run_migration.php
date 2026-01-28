<?php

/**
 * Script para ejecutar la migración que hace branch_id nullable
 * Ejecutar con: php run_migration.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Haciendo branch_id nullable en workflow_file_batches ===\n\n";

try {
    $dbConnection = config('database.default');
    echo "Conexion de base de datos: {$dbConnection}\n";

    if ($dbConnection === 'sqlite') {
        echo "Ejecutando migracion para SQLite...\n";

        DB::statement('PRAGMA foreign_keys=off;');

        // Verificar estructura actual
        $columns = DB::select("PRAGMA table_info(workflow_file_batches)");
        echo "Columnas actuales:\n";
        $columnNames = [];
        foreach ($columns as $col) {
            echo "  - {$col->name} (notnull: " . ($col->notnull ? 'true' : 'false') . ")\n";
            $columnNames[] = $col->name;
        }

        // Crear nueva tabla con branch_id nullable (basada en la estructura real)
        DB::statement('DROP TABLE IF EXISTS workflow_file_batches_new;');

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
                files_metadata TEXT,
                uploaded_at TIMESTAMP,
                validated_at TIMESTAMP,
                workflow_request_id INTEGER NULL,
                created_at TIMESTAMP,
                updated_at TIMESTAMP,
                FOREIGN KEY (workflow_type_id) REFERENCES workflow_types(id) ON DELETE CASCADE,
                FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
                FOREIGN KEY (branch_id) REFERENCES clients(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (workflow_request_id) REFERENCES workflow_requests(id) ON DELETE SET NULL
            );
        ');
        echo "Nueva tabla creada.\n";

        // Copiar datos (solo columnas que existen)
        DB::statement('
            INSERT INTO workflow_file_batches_new (id, workflow_type_id, client_id, branch_id, user_id, batch_code, status,
                   validation_errors, files_metadata, uploaded_at, validated_at, workflow_request_id, created_at, updated_at)
            SELECT id, workflow_type_id, client_id, branch_id, user_id, batch_code, status,
                   validation_errors, files_metadata, uploaded_at, validated_at, workflow_request_id, created_at, updated_at
            FROM workflow_file_batches;
        ');
        echo "Datos copiados.\n";

        // Eliminar tabla vieja y renombrar
        DB::statement('DROP TABLE workflow_file_batches;');
        DB::statement('ALTER TABLE workflow_file_batches_new RENAME TO workflow_file_batches;');
        echo "Tabla renombrada.\n";

        // Recrear índices
        DB::statement('CREATE INDEX workflow_file_batches_client_id_branch_id_index ON workflow_file_batches(client_id, branch_id);');
        DB::statement('CREATE INDEX workflow_file_batches_status_index ON workflow_file_batches(status);');
        echo "Indices recreados.\n";

        DB::statement('PRAGMA foreign_keys=on;');

        echo "\n=== Migracion completada con exito ===\n";
    } else {
        echo "Ejecutando migracion para MySQL/PostgreSQL...\n";

        Schema::table('workflow_file_batches', function ($table) {
            $table->unsignedBigInteger('branch_id')->nullable()->change();
        });

        echo "\n=== Migracion completada con exito ===\n";
    }

    // Verificar resultado
    echo "\nVerificando estructura de branch_id:\n";
    if ($dbConnection === 'sqlite') {
        $columns = DB::select("PRAGMA table_info(workflow_file_batches)");
        foreach ($columns as $col) {
            if ($col->name === 'branch_id') {
                echo "branch_id: notnull=" . ($col->notnull ? 'true' : 'false') . "\n";
            }
        }
    }

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

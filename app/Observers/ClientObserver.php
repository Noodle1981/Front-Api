<?php

namespace App\Observers;

use App\Models\Client;
use Illuminate\Support\Facades\Log;

class ClientObserver
{
    /**
     * Handle the Client "created" event.
     */
    public function created(Client $client): void
    {
        Log::info('Cliente creado', [
            'id' => $client->id,
            'name' => $client->name,
            'user_id' => $client->user_id
        ]);
    }

    /**
     * Handle the Client "updating" event.
     */
    public function updating(Client $client): void
    {
        // Guarda los cambios significativos
        $changes = $client->getDirty();
        if (!empty($changes)) {
            Log::info('Actualizando cliente', [
                'id' => $client->id,
                'changes' => $changes
            ]);
        }
    }

    /**
     * Handle the Client "deleted" event.
     */
    public function deleted(Client $client): void
    {
        Log::info('Cliente eliminado', [
            'id' => $client->id,
            'name' => $client->name,
            'is_soft_delete' => $client->isForceDeleting() ? 'No' : 'Sí'
        ]);
    }

    /**
     * Handle the Client "restored" event.
     */
    public function restored(Client $client): void
    {
        Log::info('Cliente restaurado', [
            'id' => $client->id,
            'name' => $client->name
        ]);
    }

    /**
     * Handle the Client "force deleted" event.
     */
    public function forceDeleted(Client $client): void
    {
        Log::info('Cliente eliminado permanentemente', [
            'id' => $client->id,
            'name' => $client->name
        ]);
    }
}
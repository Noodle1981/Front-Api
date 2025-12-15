<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\LeadReminder;
use App\Notifications\LeadInactivityNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckInactiveLeads extends Command
{
    protected $signature = 'leads:check-inactive';
    protected $description = 'Check for inactive leads and create reminders';

    public function handle()
    {
        $this->info('Checking for inactive leads...');

        // Buscar leads sin actividad en los últimos 5 días
        $inactiveLeads = Lead::where('status', '!=', 'convertido')
            ->where('status', '!=', 'perdido')
            ->where(function ($query) {
                $query->whereDoesntHave('activities', function ($q) {
                    $q->where('created_at', '>', now()->subDays(5));
                })
                ->whereDoesntHave('reminders', function ($q) {
                    $q->where('created_at', '>', now()->subDays(5));
                });
            })
            ->get();

        foreach ($inactiveLeads as $lead) {
            // Crear recordatorio solo si no existe uno pendiente
            $existingReminder = $lead->reminders()
                ->where('status', 'pending')
                ->exists();

            if (!$existingReminder) {
                LeadReminder::create([
                    'lead_id' => $lead->id,
                    'user_id' => $lead->user_id,
                    'title' => '⚠️ Lead sin actividad: ' . $lead->name,
                    'description' => 'Este lead no ha tenido actividad en los últimos 5 días. Es importante hacer seguimiento.',
                    'due_date' => now()->addDay(),
                    'priority' => 'high',
                ]);

                // Notificar al usuario asignado
                $lead->user->notify(new LeadInactivityNotification($lead));
            }
        }

        $this->info('Proceso completado. Se revisaron ' . $inactiveLeads->count() . ' leads inactivos.');
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SequenceEnrollment;
use App\Models\SequenceStep;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ProcessSequences extends Command
{
    protected $signature = 'app:process-sequences';
    protected $description = 'Procesa las inscripciones de secuencias activas y ejecuta los pasos correspondientes.';

    public function handle()
    {
        $this->info('Iniciando procesamiento de secuencias...');
        Log::info('== INICIO PROCESAMIENTO DE SECUENCIAS ==');

        $enrollmentsToProcess = SequenceEnrollment::where('status', 'active')
            ->whereDate('next_step_due_at', '<=', Carbon::today())
            ->with('contact', 'currentStep', 'user') // Cargar relaciones necesarias
            ->get();

        if ($enrollmentsToProcess->isEmpty()) {
            $this->info('No hay secuencias para procesar hoy.');
            Log::info('No hay secuencias para procesar hoy.');
            return self::SUCCESS;
        }

        $this->info(sprintf('Se encontraron %d inscripciones para procesar.', $enrollmentsToProcess->count()));

        foreach ($enrollmentsToProcess as $enrollment) {
            $this->processEnrollment($enrollment);
        }

        Log::info('== FIN PROCESAMIENTO DE SECUENCIAS ==');
        $this->info('Procesamiento de secuencias finalizado.');

        return self::SUCCESS;
    }

    private function processEnrollment(SequenceEnrollment $enrollment)
    {
        $step = $enrollment->currentStep;
        $contact = $enrollment->contact;

        $this->line("Procesando paso #{$step->order} para el contacto '{$contact->name}' (Inscripción ID: {$enrollment->id})");
        Log::info("Procesando Inscripción ID: {$enrollment->id}");

        try {
            switch ($step->type) {
                case 'email':
                    $this->executeEmailStep($step, $contact);
                    break;
                case 'task':
                    $this->executeTaskStep($step, $contact, $enrollment->user);
                    break;
                default:
                    Log::warning("Tipo de paso desconocido '{$step->type}' para el paso ID: {$step->id}");
                    break;
            }

            $this->advanceSequence($enrollment);

        } catch (Throwable $e) {
            $this->error("Error procesando la inscripción ID {$enrollment->id}: " . $e->getMessage());
            Log::error("Error procesando la inscripción ID {$enrollment->id}: " . $e->getMessage(), ['exception' => $e]);
        }
    }

    private function executeEmailStep(SequenceStep $step, $contact)
    {
        $this->info("  -> Ejecutando paso de EMAIL: '{$step->subject}'");
        try {
            // Usamos Mail::raw para no depender de una Vista o Mailable complejo.
            // El cuerpo del correo viene directamente del paso de la secuencia.
            Mail::raw($step->body, function ($message) use ($step, $contact) {
                $message->to($contact->email)
                        ->subject($step->subject);
            });
            Log::info("    Correo enviado a {$contact->email}");
        } catch (Throwable $e) {
            // Si falla el envío (ej. SMTP no configurado), solo lo logueamos pero no detenemos el proceso.
            $this->error("  -> Falló el envío de correo a {$contact->email}: " . $e->getMessage());
            Log::error("Fallo en envío de email para Inscripción ID {$contact->id}: " . $e->getMessage());
        }
    }

    private function executeTaskStep(SequenceStep $step, $contact, $user)
    {
        $this->info("  -> Ejecutando paso de TAREA: '{$step->subject}'");
        Task::create([
            'title' => $step->subject,
            'description' => $step->body,
            'due_date' => Carbon::today(),
            'user_id' => $user->id,
            'taskable_id' => $contact->id,
            'taskable_type' => get_class($contact),
            'status' => 'pendiente',
        ]);
        Log::info("    Tarea creada para el usuario ID {$user->id} relacionada con el contacto ID {$contact->id}");
    }

    private function advanceSequence(SequenceEnrollment $enrollment)
    {
        $nextStep = SequenceStep::where('sequence_id', $enrollment->sequence_id)
                                ->where('order', '>', $enrollment->currentStep->order)
                                ->orderBy('order', 'asc')
                                ->first();

        if ($nextStep) {
            $enrollment->update([
                'current_step_id' => $nextStep->id,
                'next_step_due_at' => Carbon::today()->addDays($nextStep->delay_days),
            ]);
            $this->info("  -> Secuencia avanzada al paso #{$nextStep->order}. Próxima ejecución: {$enrollment->next_step_due_at->toDateString()}");
            Log::info("    Inscripción ID {$enrollment->id} avanzada al paso #{$nextStep->order}");
        } else {
            $enrollment->update(['status' => 'completed']);
            $this->info("  -> Fin de la secuencia para el contacto '{$enrollment->contact->name}'.");
            Log::info("    Inscripción ID {$enrollment->id} completada.");
        }
    }
}
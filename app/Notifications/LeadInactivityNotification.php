<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadInactivityNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $lead;

    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route('leads.edit', $this->lead);

        return (new MailMessage)
            ->subject('⚠️ Lead Inactivo: ' . $this->lead->name)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Hemos detectado que el lead "' . $this->lead->name . '" no ha tenido actividad en los últimos 5 días.')
            ->line('Datos del lead:')
            ->line('- Compañía: ' . ($this->lead->company ?? 'No especificada'))
            ->line('- Email: ' . ($this->lead->email ?? 'No especificado'))
            ->line('- Teléfono: ' . ($this->lead->phone ?? 'No especificado'))
            ->line('- Estado actual: ' . ucfirst($this->lead->status))
            ->action('Ver Lead', $url)
            ->line('Es importante mantener el seguimiento para no perder oportunidades.');
    }

    public function toArray($notifiable): array
    {
        return [
            'lead_id' => $this->lead->id,
            'lead_name' => $this->lead->name,
            'message' => 'Lead sin actividad en los últimos 5 días',
            'type' => 'lead_inactivity',
        ];
    }
}
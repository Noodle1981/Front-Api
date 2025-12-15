<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadQualifiedNotification extends Notification implements ShouldQueue
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
        $url = route('leads.show', $this->lead);

        return (new MailMessage)
            ->subject('🎯 Lead Calificado: ' . $this->lead->name)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('El lead "' . $this->lead->name . '" ha sido calificado como prospecto viable.')
            ->line('Puntuación: ' . $this->lead->score . ' puntos')
            ->line('Probabilidad de conversión: ' . ucfirst($this->lead->conversion_probability))
            ->line('Criterios cumplidos: ' . count($this->lead->qualification_data['met_criteria']) . ' de ' . $this->lead->qualification_data['total_criteria'])
            ->action('Ver Detalles', $url)
            ->line('Es momento de considerar la conversión a cliente.');
    }

    public function toArray($notifiable): array
    {
        return [
            'lead_id' => $this->lead->id,
            'lead_name' => $this->lead->name,
            'score' => $this->lead->score,
            'message' => 'Lead calificado como prospecto viable',
            'type' => 'lead_qualified',
        ];
    }
}
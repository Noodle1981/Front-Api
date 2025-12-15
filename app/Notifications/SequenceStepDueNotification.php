<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\SequenceEnrollment;
use App\Models\SequenceStep;
use App\Models\Task;

class SequenceStepDueNotification extends Notification
{
    use Queueable;

    protected $enrollment;
    protected $step;
    protected $task;

    /**
     * Create a new notification instance.
     */
    public function __construct(SequenceEnrollment $enrollment, SequenceStep $step, ?Task $task = null)
    {
        $this->enrollment = $enrollment;
        $this->step = $step;
        $this->task = $task;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // We will store in database for UI display
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $enrollableName = $this->enrollment->enrollable ? $this->enrollment->enrollable->name : 'N/A';
        $stepType = $this->step->type;
        $stepSubject = $this->step->subject ?? 'Paso de secuencia';
        $taskId = $this->task ? $this->task->id : null;

        $contactPhone = null;
        $taskType = null;
        $taskDescription = null;
        $videoLink = null;

        if ($this->enrollment->enrollable) {
            // Try to get phone if the enrollable has it (e.g., Contact)
            $contact = $this->enrollment->enrollable;
            $contactPhone = $contact->phone ?? null;
        }

        if ($this->task) {
            $taskType = $this->task->type ?? null;
            $taskDescription = $this->task->description ?? null;
            $videoLink = $this->task->video_link ?? null;
        }

        return [
            'enrollment_id' => $this->enrollment->id,
            'enrollable_id' => $this->enrollment->enrollable_id,
            'enrollable_type' => $this->enrollment->enrollable_type,
            'enrollable_name' => $enrollableName,
            'sequence_id' => $this->enrollment->sequence->id,
            'sequence_name' => $this->enrollment->sequence->name,
            'step_id' => $this->step->id,
            'step_type' => $stepType,
            'step_subject' => $stepSubject,
            'task_id' => $taskId,
            'task_type' => $taskType,
            'task_description' => $taskDescription,
            'video_link' => $videoLink,
            'contact_phone' => $contactPhone,
            'message' => "Tienes un paso de secuencia pendiente: {$stepSubject} ({$stepType}) para {$enrollableName}.",
        ];
    }
}

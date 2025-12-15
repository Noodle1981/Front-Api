<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SequenceEnrollment;
use App\Models\SequenceStep;
use App\Models\Task; // Assuming tasks are created for 'task' type steps
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Notifications\SequenceStepDueNotification;
use Illuminate\Support\Facades\Notification;

class ProcessSequenceEnrollments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sequence:process-enrollments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processes pending sequence enrollments and triggers actions based on step type.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing sequence enrollments...');

        $enrollments = SequenceEnrollment::where('next_step_due_at', '<=', Carbon::now())
                                       ->where('status', 'active') // Assuming 'active' status for enrollments
                                       ->get();

        foreach ($enrollments as $enrollment) {
            $this->processEnrollment($enrollment);
        }

        $this->info('Sequence enrollments processing complete.');
    }

    protected function processEnrollment(SequenceEnrollment $enrollment)
    {
        $currentStep = $enrollment->currentStep;

        if (!$currentStep) {
            $this->warn("Enrollment {$enrollment->id} has no current step. Skipping.");
            $enrollment->update(['status' => 'completed']); // Mark as completed if no steps left
            return;
        }

        $this->info("Processing enrollment {$enrollment->id}, step {$currentStep->id} (Type: {$currentStep->type})");

        switch ($currentStep->type) {
            case 'email':
                // Logic to send email
                $this->sendEmail($enrollment, $currentStep);
                $this->advanceEnrollment($enrollment, $currentStep);
                break;
            case 'task':
                // Logic to create a task
                $this->createTask($enrollment, $currentStep);
                $this->advanceEnrollment($enrollment, $currentStep);
                break;
            case 'call':
                // Logic to create a call activity/task
                $this->createCallActivity($enrollment, $currentStep);
                $this->advanceEnrollment($enrollment, $currentStep);
                break;
            case 'video_call':
                // Logic to create a video call activity/task
                $this->createVideoCallActivity($enrollment, $currentStep);
                $this->advanceEnrollment($enrollment, $currentStep);
                break;
            default:
                $this->error("Unknown step type '{$currentStep->type}' for enrollment {$enrollment->id}.");
                break;
        }
    }

    protected function sendEmail(SequenceEnrollment $enrollment, SequenceStep $step)
    {
        // Implement email sending logic here.
        // This would typically involve a Mailable class and Mail::to()->send()
        Log::info("Sending email for enrollment {$enrollment->id}, step {$step->id}. Subject: {$step->subject}");
        // For now, just logging.
    }

    protected function createTask(SequenceEnrollment $enrollment, SequenceStep $step)
    {
        $task = Task::create([
            'user_id' => $enrollment->user_id,
            'title' => $step->subject ?? 'Sequence Task',
            'description' => $step->body ?? 'Task from sequence enrollment.',
            'due_date' => Carbon::now()->addDay(),
            'status' => 'pending',
            'taskable_id' => $enrollment->enrollable_id,
            'taskable_type' => $enrollment->enrollable_type,
        ]);
        Log::info("Created task for enrollment {$enrollment->id}, step {$step->id}.");
        $enrollment->user->notify(new SequenceStepDueNotification($enrollment, $step, $task));
    }

    protected function createCallActivity(SequenceEnrollment $enrollment, SequenceStep $step)
    {
        $task = Task::create([
            'user_id' => $enrollment->user_id,
            'title' => $step->subject ?? 'Sequence Call',
            'description' => $step->body ?? 'Call from sequence enrollment.',
            'due_date' => Carbon::now()->addDay(),
            'status' => 'pending',
            'type' => 'call',
            'taskable_id' => $enrollment->enrollable_id,
            'taskable_type' => $enrollment->enrollable_type,
        ]);
        Log::info("Created call activity for enrollment {$enrollment->id}, step {$step->id}.");
        $enrollment->user->notify(new SequenceStepDueNotification($enrollment, $step, $task));
    }

    protected function createVideoCallActivity(SequenceEnrollment $enrollment, SequenceStep $step)
    {
        $task = Task::create([
            'user_id' => $enrollment->user_id,
            'title' => $step->subject ?? 'Sequence Video Call',
            'description' => $step->body ?? 'Video call from sequence enrollment.',
            'due_date' => Carbon::now()->addDay(),
            'status' => 'pending',
            'type' => 'video_call',
            'taskable_id' => $enrollment->enrollable_id,
            'taskable_type' => $enrollment->enrollable_type,
        ]);
        Log::info("Created video call activity for enrollment {$enrollment->id}, step {$step->id}.");
        $enrollment->user->notify(new SequenceStepDueNotification($enrollment, $step, $task));
    }

    protected function advanceEnrollment(SequenceEnrollment $enrollment, SequenceStep $currentStep)
    {
        $nextStep = $enrollment->sequence->steps()
                                         ->where('order', '>', $currentStep->order)
                                         ->first();

        if ($nextStep) {
            $enrollment->update([
                'current_step_id' => $nextStep->id,
                'next_step_due_at' => Carbon::now()->addDays($nextStep->delay_days),
            ]);
            $this->info("Enrollment {$enrollment->id} advanced to step {$nextStep->id}. Next due: {$enrollment->next_step_due_at}");
        } else {
            $enrollment->update([
                'status' => 'completed',
                'current_step_id' => null,
                'next_step_due_at' => null,
            ]);
            $this->info("Enrollment {$enrollment->id} completed all steps.");
        }
    }
}

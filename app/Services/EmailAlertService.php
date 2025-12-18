<?php

namespace App\Services;

use App\Models\EmailLog;
use App\Models\ClientCredential;
use App\Mail\TestEmailMailable;
use App\Mail\ApiErrorAlertMailable;
use Illuminate\Support\Facades\Mail;
use Exception;

class EmailAlertService
{
    /**
     * Send a test email to verify SMTP configuration
     */
    public function sendTestEmail(string $recipientEmail): array
    {
        try {
            Mail::to($recipientEmail)->send(new TestEmailMailable());
            
            $this->logEmail([
                'recipient_email' => $recipientEmail,
                'subject' => 'Email de Prueba - Front-API',
                'type' => 'test',
                'status' => 'sent',
                'content' => 'Email de prueba enviado exitosamente',
            ]);

            return [
                'success' => true,
                'message' => 'Email de prueba enviado exitosamente a ' . $recipientEmail
            ];
        } catch (Exception $e) {
            $this->logEmail([
                'recipient_email' => $recipientEmail,
                'subject' => 'Email de Prueba - Front-API',
                'type' => 'test',
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error al enviar email: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send API error alert email
     */
    public function sendApiErrorAlert(ClientCredential $credential, array $errorData): array
    {
        try {
            $recipientEmail = $credential->alert_email ?? $credential->client->user->email;
            
            Mail::to($recipientEmail)->send(new ApiErrorAlertMailable($credential, $errorData));
            
            $this->logEmail([
                'user_id' => $credential->client->user_id,
                'recipient_email' => $recipientEmail,
                'subject' => 'Alerta: Error en API - ' . $credential->apiService->name,
                'type' => 'api_error',
                'status' => 'sent',
                'content' => $errorData['message'] ?? 'Error en API',
                'metadata' => [
                    'client_id' => $credential->client_id,
                    'credential_id' => $credential->id,
                    'api_service_id' => $credential->api_service_id,
                    'error_data' => $errorData,
                ],
            ]);

            return [
                'success' => true,
                'message' => 'Alerta enviada a ' . $recipientEmail
            ];
        } catch (Exception $e) {
            $this->logEmail([
                'user_id' => $credential->client->user_id ?? null,
                'recipient_email' => $recipientEmail ?? 'unknown',
                'subject' => 'Alerta: Error en API',
                'type' => 'api_error',
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'metadata' => [
                    'client_id' => $credential->client_id,
                    'credential_id' => $credential->id,
                ],
            ]);

            return [
                'success' => false,
                'message' => 'Error al enviar alerta: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Log email to database
     */
    protected function logEmail(array $data): void
    {
        EmailLog::create($data);
    }

    /**
     * Get email statistics
     */
    public function getEmailStats(int $days = 30): array
    {
        $query = EmailLog::recent($days);

        return [
            'total' => $query->count(),
            'sent' => (clone $query)->sent()->count(),
            'failed' => (clone $query)->failed()->count(),
            'success_rate' => $this->calculateSuccessRate($query),
            'by_type' => $this->getEmailsByType($query),
            'daily_trend' => $this->getDailyTrend($days),
        ];
    }

    /**
     * Calculate success rate
     */
    protected function calculateSuccessRate($query): float
    {
        $total = $query->count();
        if ($total === 0) return 0;

        $sent = (clone $query)->sent()->count();
        return round(($sent / $total) * 100, 1);
    }

    /**
     * Get emails grouped by type
     */
    protected function getEmailsByType($query): array
    {
        return (clone $query)
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
    }

    /**
     * Get daily email trend
     */
    protected function getDailyTrend(int $days): array
    {
        $trend = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $trend['labels'][] = $date->format('d/m');
            $trend['sent'][] = EmailLog::whereDate('created_at', $date->format('Y-m-d'))
                ->sent()
                ->count();
            $trend['failed'][] = EmailLog::whereDate('created_at', $date->format('Y-m-d'))
                ->failed()
                ->count();
        }
        return $trend;
    }
}

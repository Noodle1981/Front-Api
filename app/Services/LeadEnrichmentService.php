<?php

namespace App\Services;

class LeadEnrichmentService
{
    public function validateEmail(string $email): bool
    {
        // Validación básica de formato
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Verificar que el dominio tenga registros MX
        $domain = explode('@', $email)[1];
        return checkdnsrr($domain, 'MX');
    }

    public function validatePhone(string $phone): bool
    {
        // Eliminar caracteres no numéricos
        $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Validar formato internacional
        if (!preg_match('/^\+?[1-9]\d{1,14}$/', $cleanPhone)) {
            return false;
        }

        return true;
    }

    public function basicEnrichment($lead)
    {
        // Enriquecimiento básico sin servicios externos
        if ($lead->email) {
            $domain = explode('@', $lead->email)[1];
            
            // Inferir datos de la empresa desde el dominio
            if (!$lead->website) {
                $lead->website = "https://www." . $domain;
            }
            
            if (!$lead->company && $domain !== 'gmail.com' && $domain !== 'hotmail.com') {
                $lead->company = ucfirst(explode('.', $domain)[0]);
            }
        }

        // Validar y actualizar estado de verificación
        $lead->email_verified = $this->validateEmail($lead->email);
        $lead->phone_verified = $this->validatePhone($lead->phone);
        
        $lead->last_enrichment_at = now();
        $lead->save();

        return $lead;
    }
}
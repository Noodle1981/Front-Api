<?php

namespace App\Models\Concerns;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;

trait NormalizesPhone
{
    /**
     * Normaliza y guarda el teléfono en formato E.164 cuando es posible.
     * Si el valor está vacío deja null.
     * Si la librería no puede parsear el número, se guarda el valor original.
     *
     * Nota: la región por defecto es 'AR'. Si necesitás otra lógica, adaptar aquí.
     */
    public function setPhoneAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['phone'] = null;
            return;
        }

        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $phoneNumber = $phoneUtil->parse($value, 'AR');
            // Guardamos el número en formato E.164
            $this->attributes['phone'] = $phoneUtil->format($phoneNumber, PhoneNumberFormat::E164);
        } catch (\libphonenumber\NumberParseException $e) {
            // Si la validación falló por alguna razón, guardamos el valor original
            $this->attributes['phone'] = $value;
        }
    }
}

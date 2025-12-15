<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;


class ValidPhoneNumber implements ValidationRule
{
    protected $attributes = [];

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
 public function validate(string $attribute, mixed $value, Closure $fail): void
{
    if (empty($value)) {
        return;
    }

    $phoneUtil = PhoneNumberUtil::getInstance();
    try {
        $phoneNumber = $phoneUtil->parse($value, 'AR');
        if (!$phoneUtil->isValidNumber($phoneNumber)) {
            $fail('El número de teléfono no es válido.');
        }
    } catch (NumberParseException $e) {
        $fail('El número de teléfono no es válido.');
    }
}

    protected function setPhoneAttribute($value)
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

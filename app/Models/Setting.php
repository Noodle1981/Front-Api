<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function setValue($key, $value)
    {
        return static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function getValue($key, $default = null)
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    /**
     * Obtiene todas las configuraciones de correo
     * 
     * @return array
     */
    public static function getMailSettings()
    {
        $mailKeys = [
            'mail_mailer',
            'mail_host',
            'mail_port',
            'mail_encryption',
            'mail_username',
            'mail_password',
            'mail_from_address',
            'mail_from_name',
        ];

        $settings = [];
        foreach ($mailKeys as $key) {
            $settings[$key] = static::getValue($key, '');
        }

        // Valores por defecto si no existen
        $settings['mail_mailer'] = $settings['mail_mailer'] ?: 'smtp';
        $settings['mail_host'] = $settings['mail_host'] ?: 'smtp.gmail.com';
        $settings['mail_port'] = $settings['mail_port'] ?: '587';
        $settings['mail_encryption'] = $settings['mail_encryption'] ?: 'tls';
        $settings['mail_from_address'] = $settings['mail_from_address'] ?: config('mail.from.address', 'hello@example.com');
        $settings['mail_from_name'] = $settings['mail_from_name'] ?: config('mail.from.name', 'Example');

        return $settings;
    }
}

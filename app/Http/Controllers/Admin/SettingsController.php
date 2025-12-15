<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    /**
     * Muestra la configuración del sistema de correo
     */
    public function emailConfig()
    {
        $settings = Setting::getMailSettings();
        return view('admin.settings.email', compact('settings'));
    }

    /**
     * Actualiza la configuración del sistema de correo
     */
    public function updateEmailConfig(Request $request)
    {
        $request->validate([
            'mail_mailer' => 'required|string',
            'mail_host' => 'required|string',
            'mail_port' => 'required|numeric',
            'mail_encryption' => 'nullable|string',
            'mail_username' => 'required|string',
            'mail_password' => 'nullable|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        $settings = $request->only([
            'mail_mailer',
            'mail_host',
            'mail_port',
            'mail_encryption',
            'mail_username',
            'mail_from_address',
            'mail_from_name',
        ]);

        // Solo actualizar la contraseña si se proporciona una nueva
        if ($request->filled('mail_password')) {
            $settings['mail_password'] = $request->mail_password;
        }

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Limpiar caché de configuración
        Artisan::call('config:clear');

        return redirect()->route('admin.settings.email')->with('success', 'Configuración de correo actualizada correctamente.');
    }
}
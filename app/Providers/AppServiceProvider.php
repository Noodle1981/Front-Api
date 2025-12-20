<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Models\Client;
use App\Observers\ClientObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar observers para auditoría y eventos
        Client::observe(ClientObserver::class);

        // Definimos un mapa explícito para las relaciones polimórficas
        // Esto evita problemas y hace las relaciones más seguras y mantenibles
        Relation::morphMap([
            'client' => \App\Models\Client::class,
        ]);

        // Usamos un try-catch porque esta lógica se ejecuta muy temprano,
        // incluso durante las migraciones, cuando la tabla 'settings' podría no existir.
        // También evitamos ejecutar esto durante tests para evitar problemas con SQLite en memoria
        if (app()->runningUnitTests()) {
            return;
        }

        try {
            // Comprobamos si la tabla 'settings' ya ha sido migrada.
            if (Schema::hasTable('settings')) {
                $mailSettings = Setting::where('key', 'like', 'mail_%')->pluck('value', 'key');

                if ($mailSettings->count() > 0) {
                    $config = [
                        'driver' => $mailSettings->get('mail_mailer', 'smtp'),
                        'host' => $mailSettings->get('mail_host'),
                        'port' => $mailSettings->get('mail_port'),
                        'encryption' => $mailSettings->get('mail_encryption'),
                        'username' => $mailSettings->get('mail_username'),
                        'password' => $mailSettings->get('mail_password'),
                        'from' => [
                            'address' => $mailSettings->get('mail_from_address'),
                            'name' => $mailSettings->get('mail_from_name'),
                        ],
                    ];

                    // ¡LA LÍNEA MÁGICA!
                    // Sobrescribimos la configuración de correo en tiempo de ejecución.
                    Config::set('mail', $config);
                }
            }
        } catch (\Exception $e) {
            // Si hay un error (ej: durante la instalación), lo ignoramos para no romper el proceso.
        }
    }
}
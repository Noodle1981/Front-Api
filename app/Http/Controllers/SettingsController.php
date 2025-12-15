<?php
namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Muestra las preferencias del usuario
     */
    public function index()
    {
        $settings = Setting::where('user_id', auth()->id())
                        ->whereIn('key', ['email_notifications', 'task_reminders', 'date_format', 'timezone'])
                        ->pluck('value', 'key');

        return view('settings.index', compact('settings'));
    }

    /**
     * Actualiza las preferencias del usuario
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'task_reminders' => 'boolean',
            'date_format' => 'required|string|in:d/m/Y,Y-m-d,m/d/Y',
            'timezone' => 'required|string|timezone',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'key' => $key,
                ],
                ['value' => $value]
            );
        }

        return redirect()->route('settings.index')
                ->with('success', 'Tus preferencias han sido actualizadas correctamente.');
    }
}
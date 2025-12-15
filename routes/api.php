<?php



use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\DealController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\ActivityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- Rutas Públicas (no requieren autenticación) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- Rutas Protegidas (requieren un token válido) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

     Route::post('/leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');
    
    // Ruta para obtener los datos del usuario autenticado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rutas CRUD para todos los recursos principales
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('contacts', ContactController::class);
    Route::apiResource('leads', LeadController::class);
    Route::apiResource('deals', DealController::class);
    Route::apiResource('tasks', TaskController::class);


    //Rutas para actividades
    Route::apiResource('activities', ActivityController::class)->only(['store']); // Solo necesitamos 'store' por ahora
   
    Route::get('/deals-won', [DealController::class, 'getWonDeals'])->name('deals.won');
    Route::get('/deals-lost', [DealController::class, 'getLostDeals'])->name('deals.lost');
});
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource; // Crearemos este resource a continuación
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // app/Http/Controllers/Api/TaskController.php -> index()
public function index(Request $request)
{
    $tasks = $request->user()->tasks()
        ->with('taskable') // ¡Cargamos a qué está relacionada la tarea!
        ->latest('due_date') // Ordenamos por fecha de vencimiento
        ->paginate(15);
        
    return TaskResource::collection($tasks);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'status' => ['required', Rule::in(['pendiente', 'en_proceso', 'completado'])],
            
            // Validación para la relación polimórfica
            'taskable_id' => 'required|integer',
            'taskable_type' => 'required|string|in:App\Models\Client,App\Models\Deal,App\Models\Lead', // Añade más modelos si es necesario
        ]);

        // Verificamos que el modelo al que se asocia la tarea exista
        $taskable = $validated['taskable_type']::find($validated['taskable_id']);
        if (!$taskable) {
            return response()->json(['message' => 'The associated resource does not exist.'], 404);
        }

        $task = new Task($validated);
        $task->user_id = Auth::id(); // Asignamos el usuario actual
        $task->taskable()->associate($taskable);
        $task->save();

        return new TaskResource($task);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        // Aseguramos que el usuario solo pueda ver sus propias tareas
        if (Auth::id() !== $task->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return new TaskResource($task->load('taskable')); // Cargamos la relación polimórfica
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        // Aseguramos que el usuario solo pueda actualizar sus propias tareas
        if (Auth::id() !== $task->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'sometimes|required|date',
            'status' => ['sometimes','required', Rule::in(['pendiente', 'en_proceso', 'completado'])],
        ]);

        $task->update($validated);

        return new TaskResource($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        // Aseguramos que el usuario solo pueda eliminar sus propias tareas
        if (Auth::id() !== $task->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $task->delete();

        return response()->noContent(); // Respuesta 204: OK, sin contenido
    }
}

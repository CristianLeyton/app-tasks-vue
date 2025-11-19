<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class TaskController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        //autorizacion desde TaskPolicy
        $this->authorize('viewAny', Task::class);

        $user = Auth::user();
        /** @disregard */
        if ($user->hasRole('admin')) {
            $tasks = Task::with('user')->get();
        } else {
            $tasks = Task::where('user_id', $user->id)->get();
        }

        return response()->json($tasks, Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        //autorizacion desde TaskPolicy
        $this->authorize('create', Task::class);

        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
        ]);

        $task = new Task();
        $task->title = $request->title;
        $task->description = $request->description;
        $task->status = $request->input('status', 'pending');
        $task->due_date = $request->due_date;
        //Si es admin puede asignar la tarea a otro usuario
        $user = Auth::user();
        /** @disregard */
        if ($user->hasRole('admin') && $request->filled('user_id')) {
            $request->validate([
                'user_id' => 'exists:users,id'
            ]);
            $task->user_id = $request->user_id;
        } else {
            $task->user_id = $user->id;
        }
        $task->save();

        return response()->json([
            'message' => 'Tarea creada correctamente',
            'task' => $task
        ], Response::HTTP_CREATED);
    }


    public function show(string $id)
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json([
                'message' => 'No se encontró la tarea'
            ], Response::HTTP_NOT_FOUND);
        }

        // Valida permisos con la Policy
        $this->authorize('view', $task);
        return response()->json($task, Response::HTTP_OK);
    }

    public function edit(Request $request, string $id)
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json([
                'message' => 'No se encontró la tarea'
            ], Response::HTTP_NOT_FOUND);
        }

        // Valida permisos con la Policy
        $this->authorize('update', $task);

        // Si el campo viene en el request, lo actualizo (aunque sea null)
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
        ]);

        if ($request->has('title')) {
            $task->title = $request->title;
        }
        if ($request->has('description')) {
            $task->description = $request->description;
        }
        if ($request->has('status')) {
            $task->status = $request->status;
        }
        if ($request->has('due_date')) {
            $task->due_date = $request->due_date;
        }

        //Si es admin, puedo cambiar el user_id siempre y cuando lo envíe en la petición
        /** @disregard */
        if (Auth::user()->hasRole('admin') && $request->has('user_id')) {
            $request->validate([
                'user_id' => 'nullable|exists:users,id'
            ]);
            $task->user_id = $request->user_id;
        }

        $task->due_date = $request->due_date ? $request->due_date : $task->due_date;
        $task->save();

        return response()->json([
            'message' => 'Tarea editada correctamente',
            'task' => $task
        ], Response::HTTP_OK);
    }

    public function delete(string $id)
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json([
                'message' => 'No se encontró la tarea'
            ], Response::HTTP_NOT_FOUND);
        }
        // Valida permisos con la Policy
        $this->authorize('delete', $task);
        $task->delete();

        return response()->json([
            'message' => 'Tarea eliminada correctamente'
        ], Response::HTTP_OK);
    }
}

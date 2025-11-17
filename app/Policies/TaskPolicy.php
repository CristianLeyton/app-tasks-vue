<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{

    public function viewAny(User $user): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $user->can('view task');
    }


    public function view(User $user, Task $task): bool
    {
        // Admin siempre puede ver
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->can('view task')) {
            // Debe ser quien creo la tarea
            return $task->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('create task');
    }

    public function update(User $user, Task $task): bool
    {
        // Si es admin puede editar cualquier tarea
        if ($user->hasRole('admin')) {
            return true;
        }

        // Si es editor puede editar la tarea que creo
        if ($user->can('edit task')) {
            return $task->user_id === $user->id;
        }

        return false;
    }


    public function delete(User $user, Task $task): bool
    {
        // Si es admin puede eliminar cualquier tarea
        if ($user->hasRole('admin')) {
            return true;
        }

        // Si es editor puede eliminar la tarea que creo
        if ($user->can('delete task')) {
            return $task->user_id === $user->id;
        }

        return false;
    }
}

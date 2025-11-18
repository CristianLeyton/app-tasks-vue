<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('user-profile', [AuthController::class, 'userProfile']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('get-users', [AuthController::class, 'getUsers']);

    //Rutas de tareas
    Route::get('list-tasks', [TaskController::class, 'index']);
    Route::post('create-task', [TaskController::class, 'create']);
    Route::get('show-task/{id}', [TaskController::class, 'show']);
    Route::put('edit-task/{id}', [TaskController::class, 'edit']);
    Route::post('delete-task/{id}', [TaskController::class, 'delete']);
});
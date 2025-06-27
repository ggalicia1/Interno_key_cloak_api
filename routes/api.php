<?php

use App\Http\Controllers\KeyCloakController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */


Route::get('users', [UserController::class, 'users']);
Route::get('users/user-by-id', [UserController::class, 'userById']);
Route::post('users', [UserController::class, 'createUser']);
Route::put('users/{realm}/{user_id}', [UserController::class, 'update']);
Route::post('users/credentials/reset-password', [UserController::class, 'resetPassword']);




Route::get('clients', [KeyCloakController::class, 'clients']);
Route::get('clients/client-by-id', [KeyCloakController::class, 'clientById']);
Route::get('roles', [KeyCloakController::class, 'roles']);
Route::get('roles/role-by-name', [KeyCloakController::class, 'roleByName']);
Route::get('realms', [KeyCloakController::class, 'realms']);
Route::get('realms/realm-by-name', [KeyCloakController::class, 'realmByName']);

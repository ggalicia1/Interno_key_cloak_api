<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\KeyCloakController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */


Route::prefix('users')->group(function(){
    Route::get('', [UserController::class, 'users']);
    Route::get('user-by-id', [UserController::class, 'userById']);
    Route::post('', [UserController::class, 'createUser']);
    Route::put('{realm}/{user_id}', [UserController::class, 'update']);
    Route::put('enable-disable/{realm}/{user_id}/{enabled}', [UserController::class, 'enableDisable']);
    Route::post('credentials/reset-password', [UserController::class, 'resetPassword']);
    Route::get('search', [UserController::class, 'search']);
    Route::get('retrieve-realm-roles', [UserController::class, 'retrieveRealmRoles']);

    Route::post('role-assign', [UserController::class, 'roleAssign']);

    Route::put('join-group/{realm}/{user_id}/{group_id}', [UserController::class, 'joinGroup']);
    Route::delete('leave-group/{realm}/{user_id}/{group_id}', [UserController::class, 'leaveGroup']);
    Route::get('retrieve-groups', [UserController::class, 'retrieveGroups']);
});

Route::prefix('clients')->controller(ClientController::class)->group(function(){
    Route::get('', 'clients');
    Route::get('client-by-id', 'clientById');
});



/* Route::get('clients', [KeyCloakController::class, 'clients']);
Route::get('clients/client-by-id', [KeyCloakController::class, 'clientById']);
 */
Route::get('roles', [KeyCloakController::class, 'roles']);
Route::get('roles/role-by-name', [KeyCloakController::class, 'roleByName']);
Route::get('realms', [KeyCloakController::class, 'realms']);
Route::get('realms/realm-by-name', [KeyCloakController::class, 'realmByName']);

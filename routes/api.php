<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientRolesController;
use App\Http\Controllers\KeyCloakController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */


Route::middleware(['JwtKeyCloak', 'ClientKeyCloak'])->prefix('users')->group(function(){
    Route::get('', [UserController::class, 'users']);
    Route::get('user-by-id', [UserController::class, 'userById']);
    Route::post('', [UserController::class, 'createUser']);
    Route::put('{realm}/{user_id}', [UserController::class, 'update']);
    Route::put('enable-disable', [UserController::class, 'enableDisable']);
    Route::post('credentials/reset-password', [UserController::class, 'resetPassword']);
    Route::get('search', [UserController::class, 'search']);
    Route::get('retrieve-realm-roles', [UserController::class, 'retrieveRealmRoles']);

    Route::put('join-group/{realm}/{user_id}/{group_id}', [UserController::class, 'joinGroup']);
    Route::delete('leave-group/{realm}/{user_id}/{group_id}', [UserController::class, 'leaveGroup']);
    Route::get('retrieve-groups', [UserController::class, 'retrieveGroups']);
});
Route::middleware(['JwtKeyCloak', 'ClientKeyCloak'])->prefix('users')->group(function(){
    Route::prefix('clients')->group(function(){
        Route::get('roles', [UserRoleController::class, 'roles']);
        Route::post('assign-role', [UserRoleController::class, 'assignRole']);
        Route::delete('remove-role/{realm}/{user_id}/{client_uuid}/{role_name}', [UserRoleController::class, 'removeRole']);
    });

});


Route::middleware(['JwtKeyCloak', 'ClientKeyCloak'])->prefix('clients')->controller(ClientController::class)->group(function(){
    Route::get('', 'clients');
    Route::get('{realm}/catalog', 'clientsTypeCatalog');
    Route::get('client-by-id', 'clientById');
    Route::post('create', [ClientController::class, 'create']);
    Route::put('update', [ClientController::class, 'update']);
    Route::delete('delete', 'deleteClient');

});
Route::middleware(['JwtKeyCloak', 'ClientKeyCloak'])->prefix('clients/roles')->controller(ClientRolesController::class)->group(function(){
    Route::get('', 'clientRoles');
    Route::get('/role-by-name', 'clientRoleByName');
    Route::post('create', 'createClientRole');
    Route::put('update', 'updateClientRole');
    Route::delete('delete', 'deleteClientRole');
});


Route::get('roles', [KeyCloakController::class, 'roles']);
Route::get('roles/role-by-name', [KeyCloakController::class, 'roleByName']);
Route::get('realms', [KeyCloakController::class, 'realms']);
Route::get('realms/realm-by-name', [KeyCloakController::class, 'realmByName']);

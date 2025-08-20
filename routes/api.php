<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
    API Routes
*/

//Rutas publicas (no requieren autenticacion)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/roles', [AuthController::class, 'getRoles']);
Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

//Rutas privadas (requieren token de Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Información del usuario autenticado
    Route::get('/user', function (Request $request) {
        return response()->json([
            'user' => $request->user()
        ]);
    });

    // Logout Revocar token actual
    Route::post('/logout', [AuthController::class, 'logout']);

    // Revocar todos los tokens de un usuario
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);

    // Perfil del usuario
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    // Cambiar contraseña
    Route::put('/change-password', [AuthController::class, 'changePassword']);
    
    //Eliminar Cuenta propia
    Route::delete('/account', [AuthController::class, 'deleteAccount']);

    //Gestion de Usuarios (Solo para admins)
    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/users', [AuthController::class, 'listUsers']);
        Route::delete('/users/{id}', [AuthController::class, 'deleteUser']);
    });


});
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8'
        ]);

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        $user = Auth::user();

        //Creacion de token de Sanctum
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login correcto',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id
            ],
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    //Registro de nuevo usuario
    public function register(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role_id' => 'required|integer|exists:roles,id'
        ]);

        try {
            $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => 2 // Asignara rol Admin por defecto
        ]);

        //Token automaticamente al registrarse
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario creado', 
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id
            ],
            'token' => $token,
            'token_type' => 'Bearer'
        ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el usuario',
                'error'=> $e->getMessage()
            ], 500);
        }
    }
    
    // Revocar token actual
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logout correcto'
        ]);
    }

    // Revocar todos los tokens de un usuario
    public function logoutAll(Request $request)
    {
        $request->user()->deleteTokens();
        return response()->json([
            'message' => 'Logout todos los dispositivos exitoso'
        ]);
    }

    // Perfil del usuario autenticado
    public function profile(Request $request)
    {
        return response()->json([
            'data' => $request->user()
        ]);
    }

    //Actualizar perfil del usuario
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return response()->json([
            'message' => 'Perfil actualizado',
            'user' => $user
        ]);
    }

    //Cambiar contraseña
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string|min:8',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Contraseña actual incorrecta'
            ], 422);
        }

        // Actualizar contraseña
        $user->update([
            'password' => bcrypt($request->password)
        ]);

        return response()->json([
            'message' => 'Contraseña actualizada exitosamente'
        ]);
    }

    //Eliminar cuenta propia
    public function deleteAccount(Request $request)
    {
        $request-> validate([
            'password' => 'required|string|min:8',
            'confirmation'=> 'required|in:DELETE_MY_ACCOUNT'
        ]);

        $user = $request->user();

        //Verificar contraseña
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Contraseña incorrecta'
            ], 422);
        }

        // Revocar tokens del usuario
        $user->tokens()->delete();

        // Eliminar usuario
        $user->delete();

        return response()->json([
            'message' => 'Cuenta eliminada exitosamente'
        ]);
    }

    //Eliminar cuenta de otro usuario (Solo Admin)
    public function deleteUser(Request $request, $id)
    {
        $currentUser = $request->user();

        // Verificacio que el usuario sea admin
        if ($currentUser->role_id != 1) {
            return response()->json([
                'message' => 'No tienes permiso para realizar esta acción'
            ], 403);
        }

        $userToDelete = User::find($id);

        if (!$userToDelete) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        // Prevenir que un admin se elime a si mismo
        if ($currentUser->id === $userToDelete->id) {
            return response()->json([
                'message' => 'No puedes eliminar a ti mismo'
            ], 422);
        }

        // Revocar tokens del usuario
        $userToDelete->tokens()->delete();

        // Eliminar usuario
        $userName = $userToDelete->name;
        $userToDelete->delete();

        return response()->json([
            'message' => 'Usuario ' . $userName . ' eliminado exitosamente'
        ]);
    }

    public function listUsers()
    {
        // SUGERENCIA: Proteger esta ruta para que solo los administradores puedan acceder.
        if (Auth::user()->role_id != 1) {
            
            return response()->json(['message' => 'No autorizado'], 403);
        
        }

        $users = User::all();

        return response()->json([
            'users' => $users
        ]);
    }

    //Agregamos el metodo getRoles

}

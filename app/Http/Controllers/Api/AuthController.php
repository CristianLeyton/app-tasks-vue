<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        //validacion
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'password_confirmation' => 'required'
        ]);
        //alta
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->assignRole('editor');
        $user->save();
        //respuesta
        return response($user, Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {
        //validacion
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        //emitir token
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            /** @disregard */
            $token = $user->createToken('token')->plainTextToken;
            //respuesta
            return response(["message" => "Sesión iniciada", "token" => $token], Response::HTTP_OK);
        } else {
            return response(
                [
                    "message" => "Las credenciales no son válidas"
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }
    }

    public function userProfile()
    {
        $user = Auth::user();
        /** @disregard */
        $role = $user->getRoleNames()->first();
        return response()->json(
            [
                "userProfile" => $user,
                "role" => $role
            ],
            Response::HTTP_OK
        );
    }

    public function getUsers()
    {
        /** @disregard */
        if (Auth::user()->hasRole('admin')) {
            $users = User::with('roles')->get();
            /** @disregard */
            return response()->json($users, Response::HTTP_OK);
        } else {
            return response()->json([
                "message" => "No autorizado"
            ], Response::HTTP_FORBIDDEN);
        }
    }

    public function logout(Request $request)
    {
        /*         $request->user()->tokens()->delete(); */
        $request->user()->currentAccessToken()->delete();
        return response(
            [
                "message" => "Sesión cerrada"
            ],
            Response::HTTP_OK
        );
    }

    public function updateUserRole(Request $request, $id)
    {
        /** @disregard */
        if (Auth::user()->hasRole('admin')) {
            $user = User::find($id);
            if ($user) {
                $request->validate([
                    'role' => 'required|in:admin,editor,viewer'
                ]);
                $user->syncRoles([$request->role]);
                return response()->json([
                    "message" => "Rol actualizado"
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    "message" => "Usuario no encontrado"
                ], Response::HTTP_NOT_FOUND);
            }
        } else {
            return response()->json([
                "message" => "No autorizado"
            ], Response::HTTP_FORBIDDEN);
        }
    }

    public function deleteUser($id)
    {
        // Evitar que un admin se borre a sí mismo o al usuario con ID 1
        /** @disregard */
        if (Auth::user()->hasRole('admin') && Auth::user()->id != $id && $id != 1) { 
            $user = User::find($id);
            if ($user) {
                $user->delete();
                return response()->json([
                    "message" => "Usuario eliminado"
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    "message" => "Usuario no encontrado"
                ], Response::HTTP_NOT_FOUND);
            }
        } else {
            return response()->json([
                "message" => "No autorizado"
            ], Response::HTTP_FORBIDDEN);
        }
    }
}

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
            return response([
                "message" => "Las credenciales no son válidas"
            ], 
            Response::HTTP_UNAUTHORIZED);
        }
    }

    public function userProfile(Request $request)
    {
        return response()->json([
            "userProfile" => Auth::user()
        ], 
        Response::HTTP_OK);
    }

    public function logout(Request $request)
    {
/*         $request->user()->tokens()->delete(); */
        $request->user()->currentAccessToken()->delete(); 
        return response([
            "message" => "Sesión cerrada"
        ], 
        Response::HTTP_OK);
    }
}

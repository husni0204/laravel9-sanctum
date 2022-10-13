<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function Register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|email|string|max:70|unique:users',
            'password' => 'required|string|min:8'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $token = $user->createToken('auth-sanctum')->plainTextToken;

        return response()->json([
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function Login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|string|max:70',
            'password' => 'required|string|min:8'
        ]);

        if (!Auth::attempt(
            $request->only('email', 'password')
        )) {
            return response()->json(['pesan' => 'Tidak diijinkan'], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $token = $user->createToken('auth-sanctum')->plainTextToken;

        return response()->json([
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function Logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'msg' => 'kamu sudah logout'
        ]);
    }
}

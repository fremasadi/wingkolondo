<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(
                [
                    'message' => 'Email atau password salah',
                ],
                401,
            );
        }

        $user = Auth::user();

        // // 🔒 Batasi khusus kurir
        // if ($user->role !== 'kurir') {
        //     return response()->json(
        //         [
        //             'message' => 'Akses hanya untuk kurir',
        //         ],
        //         403,
        //     );
        // }

        $token = $user->createToken('kurir-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'no_hp' => $user->no_hp,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }
}

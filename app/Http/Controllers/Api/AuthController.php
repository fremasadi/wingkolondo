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
            'email'     => 'required|email',
            'password'  => 'required',
            'fcm_token' => 'nullable|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Email atau password salah',
            ], 401);
        }

        $user = Auth::user();

        // 🔒 Batasi khusus kurir
        if ($user->role !== 'kurir') {
            return response()->json([
                'message' => 'Akses hanya untuk kurir',
            ], 403);
        }

        // Hapus semua token lama agar hanya 1 device yang aktif (single-session)
        $user->tokens()->delete();

        // Buat token baru untuk device ini
        $token = $user->createToken('kurir-token')->plainTextToken;

        // Update FCM token — selalu replace dengan token device terbaru
        $user->update([
            'fcm_token' => $request->fcm_token ?? $user->fcm_token,
        ]);

        return response()->json([
            'message' => 'Login berhasil',
            'token'   => $token,
            'data'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'no_hp' => $user->no_hp,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        // Hapus FCM token agar notifikasi tidak terkirim ke device yang sudah logout
        $user->update(['fcm_token' => null]);

        // Hapus Sanctum token saat ini
        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }
}

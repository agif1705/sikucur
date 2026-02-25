<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
 /**
  * Login user and create token
  */
 public function login(Request $request)
 {
  $request->validate([
   'username' => 'required|string',
   'password' => 'required|string',
  ]);

  $user = User::where('username', $request->username)->first();

  if (!$user || !Hash::check($request->password, $user->password)) {
   throw ValidationException::withMessages([
    'username' => ['Username atau password salah.'],
   ]);
  }

  // Cek apakah user aktif
  if (!$user->aktif) {
   return response()->json([
    'message' => 'Akun Anda tidak aktif.'
   ], 403);
  }

  // Hapus token lama (optional)
  $user->tokens()->delete();

  // Buat token baru
  $token = $user->createToken('auth-token')->plainTextToken;

  return response()->json([
   'message' => 'Login berhasil',
   'access_token' => $token,
   'token_type' => 'Bearer',
   'user' => [
    'id' => $user->id,
    'name' => $user->name,
    'username' => $user->username,
    'email' => $user->email,
    'jabatan_id' => $user->jabatan_id,
    'nagari_id' => $user->nagari_id,
   ]
  ]);
 }

 /**
  * Logout user (revoke token)
  */
 public function logout(Request $request)
 {
  // Hapus token yang sedang digunakan
  /** @var \Laravel\Sanctum\PersonalAccessToken $token */
  $token = $request->user()->currentAccessToken();
  $token->delete();

  return response()->json([
   'message' => 'Logout berhasil'
  ]);
 }

 /**
  * Get authenticated user info
  */
 public function me(Request $request)
 {
  return response()->json([
   'user' => $request->user()
  ]);
 }
}

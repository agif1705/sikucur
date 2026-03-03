<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RekapAbsensiPegawai;
use App\Models\User;
use Illuminate\Http\Request;
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
  $absensi = RekapAbsensiPegawai::where('user_id', $user->id)->whereMonth('date', now()->month)->whereYear('date', now()->year)->get();
  $holidays = (new RekapAbsensiPegawai())->Holiday(now()->month, now()->year);
  $workingDays = RekapAbsensiPegawai::getWorkingDaysThisMonth(now()->month, now()->year);

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
    'jabatan_id' => $user->jabatan->name,
    'nagari_id' => $user->nagari->name,
   ],
   'absensi' => [
    'total_absensi' => $absensi->count(),
    'total_hadir' => $absensi->where('status_absensi', 'Hadir')->count(),
    'total_izin' => $absensi->where('status_absensi', 'Izin')->count(),
    'total_sakit' => $absensi->where('status_absensi', 'Sakit')->count(),
    'total_libur' => $holidays,
    'total_hari_kerja' => $workingDays,
    'total_tidak_hadir' => $workingDays - $absensi->where('status_absensi', 'Hadir')->count() - $absensi->where('status_absensi', 'Izin')->count() - $absensi->where('status_absensi', 'Sakit')->count(),
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

 /**
  * Get attendance history for authenticated user
  */
 public function historyAbsensi(Request $request)
 {
  $user = $request->user();

  // Get month and year from request, default to current month/year
  $month = $request->input('month', now()->month);
  $year = $request->input('year', now()->year);

  // Get attendance records
  $absensi = RekapAbsensiPegawai::where('user_id', $user->id)
   ->whereMonth('date', $month)
   ->whereYear('date', $year)
   ->orderBy('date', 'desc')
   ->get()
   ->map(function ($item) {
    return [
     'id' => $item->id,
     'date' => $item->date,
     'time_in' => $item->time_in,
     'time_out' => $item->time_out,
     'status_absensi' => $item->status_absensi,
     'is_late' => $item->is_late,
    ];
   });

  // Get summary statistics
  $holidays = (new RekapAbsensiPegawai())->Holiday($month, $year);
  $workingDays = RekapAbsensiPegawai::getWorkingDaysThisMonth($month, $year);

  return response()->json([
   'message' => 'History absensi berhasil diambil',
   'data' => $absensi,
   'summary' => [
    'total_absensi' => $absensi->count(),
    'terlambat' => $absensi->where('is_late', true)->count(),
    'total_hadir' => $absensi->where('status_absensi', 'Hadir')->count(),
    'total_izin' => $absensi->where('status_absensi', 'Izin')->count(),
    'total_sakit' => $absensi->where('status_absensi', 'Sakit')->count(),
    'total_libur' => $holidays,
    'total_hari_kerja' => $workingDays,
    'total_tidak_hadir' => $workingDays - $absensi->where('status_absensi', 'Hadir')->count() - $absensi->where('status_absensi', 'Izin')->count() - $absensi->where('status_absensi', 'Sakit')->count(),
   ],
   'period' => [
    'month' => $month,
    'year' => $year,
   ]
  ]);
 }
}

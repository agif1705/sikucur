<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Nagari;
use App\Models\WdmsModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class WhatsAppController extends Controller
{
    public function izinPegawai()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Izin pegawai berhasil diproses.',
        ]);
    }
    public function kehadiran(Request $request)
    {
        $now = Carbon::now()->format('Y-m-d');
        // $now = Carbon::create(2025, 6, 13)->format('Y-m-d');
        $tvNow = Carbon::now()->format('d M Y');
        $noWa = preg_replace('/^(\+62|62|0)/', '', $request->no_wa);

        if (User::where('no_hp', $noWa)->exists()) {
            $user = User::with('nagari')->where('no_hp', $noWa)->select('id', 'name', 'emp_id', 'nagari_id', 'no_hp')->first();
            $user_role = $user->roles->pluck('name')->first();
            $users = WdmsModel::with(['user' => function ($query) {
                $query->select('name', 'emp_id', 'no_hp');
            }])->where('terminal_sn', $user->nagari->sn_fingerprint)
                ->whereDate('punch_time', $now)
                ->select('emp_id', 'punch_time', 'emp_code')
                ->whereTime('punch_time', '<=', '12:00')
                ->get()
                ->unique('emp_code')
                ->map(function ($item) {
                    $item->time_only = \Carbon\Carbon::parse($item->punch_time)->format('H:i');
                    $item->date_only = \Carbon\Carbon::parse($item->punch_time)->format('Y-m-d');
                    $item->user_name = $item->user->name;
                    if ($item->time_only > '08:00') {
                        $item->is_late = true;
                    } else {
                        $item->is_late = false;
                    }
                    return $item;
                })->toJson();
            return $users;
        }
    }
}

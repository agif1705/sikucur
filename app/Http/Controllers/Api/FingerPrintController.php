<?php

namespace App\Http\Controllers\Api;

use App\Events\RealtimeEvent;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Nagari;
use Illuminate\Http\Request;
use App\Models\Re;
use App\Helpers\WhatsAppHelper;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\FingerPrint;
use App\Models\RekapAbsensiPegawai;
use App\Services\WahaService;

class FingerPrintController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function supabase(Request $request)
    {
        // Validate the incoming request data
        $data = $request->validate([
            'sn_mesin' => 'required|string|max:255',
            'emp_id' => 'required',
            'emp_code' => 'required',
            'punch_time' => 'required',
        ]);
        $date = Carbon::parse($data['punch_time'])->format('Y-m-d');
        $time_in = Carbon::parse($data['punch_time'])->format('H:i:s');
        $is_late = $time_in > '08:00:00';
        if (isset($data['emp_id'])) {
            $emp_id = $data['emp_id'];
        } else {
            $emp_id = $data['emp_code'];
        }
        $nagari_id = Nagari::where('sn_fingerprint', $data['sn_mesin'])->first()->id;
        $user = User::whereEmpId($emp_id)->first();
        RekapAbsensiPegawai::unguard();
        $save_attandance = RekapAbsensiPegawai::create([
            'user_id' => $user->id,
            'nagari_id' => $nagari_id,
            'is_late' => $is_late,
            'sn_mesin' => $request->input('sn_mesin'),
            'status_absensi' => 'Hadir',
            'resource' => 'Fingerprint',
            'time_in' => $time_in,
            'date' => $date,
        ]);
        // dd($save_attandance);
        $absensi = RekapAbsensiPegawai::where('sn_mesin', $data['sn_mesin'])
            ->whereUserId($user->id)
            ->whereDate('date', $date)
            ->whereTime('time_in', $time_in)
            ->first();
        // Cek apakah absensi sudah tercatat sebelumnya
        if (!$absensi) {
            $save_attandance = RekapAbsensiPegawai::create([
                'user_id' => $user->id,
                'nagari_id' => $nagari_id,
                'is_late' => $is_late,
                'sn_mesin' => $request->input('sn_mesin'),
                'status_absensi' => 'Hadir',
                'resource' => 'Fingerprint',
                'time_in' => $time_in,
                'date' => $date,
            ]);
            return response()->json([
                'message' => 'Fingerprint data received successfully.',
                'user_id' => $request->input('emp_code'),
                'no_hp' => $user->no_hp ?? null,
                'sn_mesin' => $request->input('sn_mesin'),
                'punch_time' => $time_in,
                'punch_date' => $date,
                'emp_id' => $emp_id,
                'absensi_exists' => $absensi
            ], 200);
        }
    }
    /**
     * Schedule a newly created resource in storage.
     */
    public function schedule(Request $request)
    {
        $date = Carbon::now()->format('Y-m-d');
        $nagari = Nagari::all();
        foreach ($nagari as $item) {
            $item->users = User::where('nagari_id', $item->id)
                ->where('aktif', true)
                ->get()
                ->map(function ($user) use ($date) {
                    $user->absensi = RekapAbsensiPegawai::where('user_id', $user->id)
                        ->whereDate('date', $date)
                        ->first();
                    return $user;
                });
        }

        return response()->json([
            'data' => $nagari,
            'test' => $request->input('jabatan_id'),
            'message' => 'WhatApp schedule data received successfully.',
        ], 200); // Not Implemented
    }
}

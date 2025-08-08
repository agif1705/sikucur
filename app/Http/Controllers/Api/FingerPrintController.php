<?php

namespace App\Http\Controllers\Api;

use App\Events\RealtimeEvent;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Nagari;
use Illuminate\Http\Request;
use App\Models\AbsensiPegawai;
use App\Helpers\WhatsAppHelper;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class FingerPrintController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // Validate the incoming request data
        $request->validate([
            'sn_mesin' => 'required|string|max:255',
            'emp_id' => 'required|integer|max:255',
            'emp_code' => 'required|string|max:255',
            'punch_time' => 'required', // Assuming each data entry is a string
        ]);
        $date_in = Carbon::parse($request->input('punch_time'))->format('Y-m-d');
        $time_in = Carbon::parse($request->input('punch_time'))->format('H:i:s');
        $is_late = $time_in > '08:00:00' ? 'Terlambat' : 'Ontime';
        if ($request->input('emp_id')) {
            $emp_id = $request->input('emp_id');
        }else {
            $emp_id = $request->input('emp_code'); 
        }
        $nagari_id = Nagari::where('sn_fingerprint', $request->input('sn_mesin'))->first()->id;
        $user = User::whereEmpId($emp_id)->select('id', 'no_hp', 'name', 'nagari_id','aktif')->first();
        $absensi = AbsensiPegawai::where('sn_mesin', $request->input('sn_mesin'))
            ->whereUserId($user->id)
            ->whereDate('date_in', $date_in)
            ->whereTime('time_in', $time_in)
            ->exists();
        // Cek apakah absensi sudah tercatat sebelumnya
        if ($absensi === false) {
            DB::beginTransaction();
                try {
                    $save_attandance = AbsensiPegawai::create([
                        'absensi_by' => 'fingerprint',
                        'emp_id' => $emp_id,
                        'absensi' => 'Hadir', 
                        'status_absensi' => $is_late,
                        'sn_mesin' => $request->input('sn_mesin'),
                        'accept' => true, 
                        'accept_by' => "fingerprint", 
                        'user_id' => $user->id,
                        'nagari_id' => $nagari_id,
                        'keterangan_absensi' => 'Hadir_Fingerprint',
                        'time_in' => $time_in,
                        'date_in' => $date_in
                    ]);
                    DB::commit();
                    // RealtimeEvent::dispatch($request->input('sn_mesin'));
                    if ($user->aktif) {
                        $response = retry(3, function () use ($user, $is_late) {
                            return WhatsAppHelper::sendMessage(
                                $user->no_hp,
                                'Hai *' . $user->name . '* , Anda *' . $is_late . '* anda telah hadir menggunakan fingerprint di *Nagari ' . $user->nagari->name .
                                    '* ,ini akan masuk ke WhatsApp Wali Nagari ' . $user->nagari->name .
                                    ' *Sebelum Jam: 12:00 Siang* terima kasih '
                            );
                        });
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['message' => 'Failed to record attendance: ' . $e->getMessage()], 500);
                }
                
        }

        return response()->json([
            'message' => 'Fingerprint data received successfully.',
            'user_id' => $request->input('emp_code'),
            'no_hp' => $user->no_hp ?? null,
            'sn_mesin' => $request->input('sn_mesin'),
            'punch_time' => $time_in,
            'punch_date' => $date_in,
            'emp_id' => $emp_id,
            'absensi_exists' => $absensi
        ], 200);
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
                    $user->absensi = AbsensiPegawai::where('user_id', $user->id)
                        ->whereDate('date_in', $date)
                        ->first();
                    return $user;
                });
        }
        

        
        return response()->json([
            'data' => $nagari ,
            'test' => $request->input('jabatan_id'),
            'message' => 'WhatApp schedule data received successfully.',
        ], 200); // Not Implemented
    }
}

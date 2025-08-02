<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FingerPrintController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'emp_id' => 'required|integer',
            'absensi' => 'required|string',
            'sn_mesin' => 'required|string',
            'punch_time' => 'required|date',
        ]);
        // Simpan data ke database atau lakukan proses lain sesuai kebutuhan
        // Contoh: AbsensiPgw::create($validatedData);
        $absensi_pegawai = [
            'emp_id' => $validatedData['emp_id'],
            'absensi' => $validatedData['absensi'],
            'sn_mesin' => $validatedData['sn_mesin'],
            'punch_time' => $validatedData['punch_time'],
        ];
        $test = $request->json()->all();
        $oke = "halo";
        return response()->json(['message' => $test, 'status' => 'success'], 200);
    }
}

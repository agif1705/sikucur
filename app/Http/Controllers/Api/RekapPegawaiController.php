<?php

namespace App\Http\Controllers\Api;

use PDF;
use App\Models\User;
use App\Models\Nagari;
use App\Models\WhatsAppLog;
use Illuminate\Http\Request;
use App\Services\GowaService;
use Illuminate\Support\Carbon;
use App\Models\RekapAbsensiPegawai;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Services\Pdf\AbsensiReportBulananService;

class RekapPegawaiController extends Controller
{
    public function webhook(Request $request)
    {
        // ini ada perubahan di iclock_transaction dan mengirimkan wa ke User
        $data = $request->validate([
            'id'  => 'required|integer',
            'sn_mesin'  => 'required|string|max:255',
            'emp_id'    => 'required',
            'emp_code'  => 'required',
            'punch_time' => 'required', // format string
        ]);

        $date = Carbon::parse($data['punch_time'])->format('Y-m-d');
        $time_in = Carbon::parse($data['punch_time'])->format('H:i:s');
        $is_late = Carbon::parse($data['punch_time'])->greaterThan(Carbon::createFromTime(8, 0)) ? 'Terlambat' : 'Ontime';
        $isLateInt = $is_late === 'Terlambat' ? 1 : 0;

        // Tentukan emp_id yang dipakai
        $emp_id = !empty($data['emp_id']) ? intval($data['emp_id']) : intval($data['emp_code']);


        // Cari nagari dan user
        $nagari_id = Nagari::where('sn_fingerprint', $data['sn_mesin'])->first()?->id;
        $user = User::whereEmpId($emp_id)->first();

        if (!$user || !$nagari_id) {
            return response()->json(['message' => 'User atau mesin tidak ditemukan'], 404);
        }
        // Cek absensi hari ini
        $absensi = RekapAbsensiPegawai::where('sn_mesin', $data['sn_mesin'])
            ->whereUserId($user->id)
            ->whereDate('date', $date)
            ->first();

        if (!$absensi) {
            // Absensi pertama (masuk)
            $pesan_masuk = "Hai *" . $user->name . "* (Jabatan : " . $user->jabatan->name . ")" . ",\nKehadiran :  " . $is_late . "* anda telah hadir pada jam *" . carbon::parse($data['punch_time'])->format('H:i') . '* menggunakan fingerprint di *Nagari ' . $user->nagari->name . "* ,ini akan masuk ke WhatsApp Wali Nagari " . $user->nagari->name . " *Sebelum Jam: 10:05 Siang* terima kasih \n   ketik : *info* -> untuk melihat informasi perintah dan bantuan lebih lanjut. \n \n_Sent || via *Cv.Baduo Mitra Solustion*_";
            $absensi = RekapAbsensiPegawai::create([
                'user_id'        => $user->id,
                'nagari_id'      => $nagari_id,
                'is_late'        => $isLateInt,
                'status_absensi' => 'Hadir',
                'sn_mesin'       => $data['sn_mesin'],
                'resource'       => 'Fingerprint',
                'id_resource'    => 'fp-' . $data['id'],
                'time_in'        => Carbon::parse($data['punch_time'])->format('H:i'),
                'date'           => Carbon::parse($data['punch_time'])->format('Y-m-d'),
            ]);
            if ($user->aktif) {
                $wa = new GowaService();
                $result = $wa->sendText($user->no_hp, $pesan_masuk);
                WhatsAppLog::create([
                    'user_id' => $user->id,
                    'phone'   => $user->no_hp,
                    'message' => $pesan_masuk,
                    'status'  => $result['code'] ?? false,
                    'response' => $result,
                ]);
            }
            return response()->json([
                'message'      => $pesan_masuk,
                'phone'       => $user->no_hp,
                'user_id'      => $user->emp_code,
                'time_in'      => $time_in,
                'date'         => $date,
                'absensi_type' => 'IN',
                'dataSender'   =>  $absensi,
                'wa_response'  => $result ?? null
            ], 200);
        } else {
            // Absensi kedua (pulang)
            if (Carbon::parse($data['punch_time'])->greaterThan(Carbon::createFromTime(12, 0))) {
                $absensi->update([
                    'time_out' => Carbon::parse($data['punch_time'])->format('H:i'),
                ]);
                $pesan_pulang = "Hai *" . $user->name . "* , Anda telah melakukan absensi pulang pada jam *" .
                    Carbon::parse($data['punch_time'])->format('H:i') . "* menggunakan fingerprint di *Nagari " . $user->nagari->name .
                    "* ,terima kasih \n   ketik : *info* -> untuk melihat informasi perintah dan bantuan lebih lanjut.\n \n _Sent || via *Cv.Baduo Mitra Solustion*_";
                if ($user->aktif) {
                    $wa = new GowaService();
                    $result = $wa->sendText($user->no_hp, $pesan_pulang);
                    WhatsAppLog::create([
                        'user_id' => $user->id,
                        'phone'   => $user->no_hp,
                        'message' => $pesan_pulang,
                        'status'  => $result['code'] ?? false,
                        'response' => $result,
                    ]);
                }
                return response()->json([
                    'message'      => $pesan_pulang,
                    'phone'       => $user->no_hp,
                    'user_id'      => $user->emp_code,
                    'time_out'     => Carbon::parse($data['punch_time'])->format('H:i'),
                    'date'         => Carbon::parse($data['punch_time'])->format('Y-m-d'),
                    'wa_response'  => $result ?? null,
                    'absensi_type' => 'OUT',
                ], 200);
            }
        }
    }
    public function absensiBulanan(Request $request,  AbsensiReportBulananService $service)
    {
        try {
            $data = $request->validate([
                'tahun' => 'required|integer|min:2020|max:' . (now()->year + 1),
                'bulan' => 'required|integer|min:1|max:12',
            ]);

            $tahun = $data['tahun'];
            $bulan = $data['bulan'];
            $filename = "absensi-pegawai-{$bulan}-{$tahun}.pdf";
            $storagePath = "private/public/absensi/{$filename}";
            $fullPath = storage_path("app/{$storagePath}");

            // Ambil semua nagari beserta user + jabatan
            $nagaris = Nagari::with(['users.jabatan' => function ($query) {
                $query->whereNotNull('name');
            }])->whereHas('users')->get();

            if ($nagaris->isEmpty()) {
                return response()->json([
                    'error' => 'Tidak ada nagari dengan pegawai yang ditemukan'
                ], 404);
            }

            // Cek apakah file sudah ada
            if (!Storage::exists($storagePath)) {
                Log::info('Generating new PDF report', [
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'nagari_count' => $nagaris->count()
                ]);

                // Generate PDF untuk setiap nagari (ambil yang terakhir sebagai master)
                $report = null;
                foreach ($nagaris as $nagari) {
                    try {
                        $report = $service->generate($tahun, $bulan, $nagari->id);
                        Log::info('Generated report for nagari', [
                            'nagari_id' => $nagari->id,
                            'nagari_name' => $nagari->name
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to generate report for nagari', [
                            'nagari_id' => $nagari->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                if (!$report) {
                    return response()->json([
                        'error' => 'Gagal membuat laporan PDF'
                    ], 500);
                }
            } else {
                Log::info('Using existing PDF file', ['path' => $fullPath]);
            }

            // Verifikasi file exists
            if (!Storage::exists($storagePath)) {
                return response()->json([
                    'error' => 'File PDF tidak ditemukan setelah generate'
                ], 500);
            }

            // Kirim WhatsApp ke semua user
            $response = [];
            $totalSent = 0;
            $totalFailed = 0;

            foreach ($nagaris as $nagari) {
                foreach ($nagari->users as $user) {
                    if (!$user->aktif || !$user->no_hp) {
                        Log::info('Skipping inactive user or user without phone', [
                            'user_id' => $user->id,
                            'name' => $user->name,
                            'aktif' => $user->aktif,
                            'has_phone' => !empty($user->no_hp)
                        ]);
                        continue;
                    }

                    $jabatan = $user->jabatan->name ?? 'Tidak ada jabatan';
                    $pesan = "Hai *{$user->name}* (Jabatan: {$jabatan}),\\n\\n" .
                        "Pegawai Nagari {$nagari->name}\\n\\n" .
                        "📊 Laporan Absensi Bulan *{$bulan}* Tahun *{$tahun}*\\n\\n" .
                        "Laporan ini dikirim setiap awal bulan untuk ditinjau kembali.\\n" .
                        "Laporan PDF dapat disimpan untuk dokumentasi.\\n\\n" .
                        "_Note: Dikirim ke seluruh pegawai dan pimpinan_\\n\\n" .
                        "Ketik: *info* untuk bantuan lebih lanjut.\\n\\n" .
                        "_Sent via Cv.Baduo Mitra Solution_";

                    try {
                        $gowa = new GowaService();
                        $result = $gowa->sendFile(
                            phone: $user->no_hp,
                            path: $fullPath,
                            caption: $pesan
                        );

                        $response[] = [
                            'user_id' => $user->id,
                            'name' => $user->name,
                            'phone' => $user->no_hp,
                            'nagari' => $nagari->name,
                            'status' => 'sent',
                            'response' => $result
                        ];
                        $totalSent++;

                        // Log WhatsApp
                        WhatsAppLog::create([
                            'user_id' => $user->id,
                            'phone' => $user->no_hp,
                            'message' => $pesan,
                            'status' => $result['code'] ?? false,
                            'response' => $result,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to send WhatsApp', [
                            'user_id' => $user->id,
                            'phone' => $user->no_hp,
                            'error' => $e->getMessage()
                        ]);

                        $response[] = [
                            'user_id' => $user->id,
                            'name' => $user->name,
                            'phone' => $user->no_hp,
                            'nagari' => $nagari->name,
                            'status' => 'failed',
                            'error' => $e->getMessage()
                        ];
                        $totalFailed++;
                    }

                    // Delay untuk avoid rate limiting
                    usleep(500000); // 0.5 second delay
                }
            }

            // Response API
            return response()->json([
                'success' => true,
                'file' => $filename,
                'path' => $fullPath,
                'storage_path' => $storagePath,
                'file_size' => Storage::size($storagePath) . ' bytes',
                'url' => Storage::url("public/absensi/{$filename}"),
                'summary' => [
                    'total_nagari' => $nagaris->count(),
                    'total_users' => $nagaris->sum(fn($n) => $n->users->count()),
                    'total_sent' => $totalSent,
                    'total_failed' => $totalFailed
                ],
                'gowa_response' => $response
            ]);
        } catch (\Exception $e) {
            Log::error('Error in absensiBulanan: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Terjadi kesalahan saat memproses laporan',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
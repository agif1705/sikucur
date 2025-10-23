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
            $jam = Carbon::parse($data['punch_time'])->format('H:i:s');
            $statusEmoji = $is_late === 'Terlambat' ? '⏳' : '✅';
            $pesan_masuk =
                "👋 Hai *{$user->name}* (Jabatan: *{$user->jabatan->name}*)\n" .
                "Kehadiran: {$statusEmoji} *{$is_late}*\n" .
                "⏰ Jam: *{$jam}*\n" .
                "📍 Lokasi: *Nagari {$user->nagari->name}*\n\n" .
                "Pesan ini akan masuk ke WhatsApp Wali Nagari {$user->nagari->name} sebelum jam *10:05* (Siang). Terima kasih.\n" .
                "ℹ️ Ketik: *info* untuk melihat perintah dan bantuan.\n\n" .
                "_Sent || via *Cv.Baduo Mitra Solution*_";

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

                $jamPulang = Carbon::parse($data['punch_time'])->format('H:i');
                $jamMasukCarbon = $absensi->time_in ? Carbon::parse($absensi->time_in) : null;
                $durasiMenit = $jamMasukCarbon ? $jamMasukCarbon->diffInMinutes(Carbon::parse($data['punch_time'])) : null;
                $durasiText = $durasiMenit !== null
                    ? sprintf('%d jam %02d menit', intdiv($durasiMenit, 60), $durasiMenit % 60)
                    : null;
                $pesan_pulang = "👋 Hai *{$user->name}*,\n\n" .
                    "✅ Absensi pulang berhasil\n" .
                    "🕐 Waktu Pulang: *{$jamPulang}*\n" .
                    "📍 Lokasi: Nagari {$user->nagari->name}\n" .
                    "📱 Metode: Fingerprint\n\n" .
                    "Terima kasih atas dedikasi Anda hari ini.\n\n" .
                    "Ketik: *info* untuk melihat informasi perintah dan bantuan lebih lanjut.\n\n" .
                    "_Sent via Cv.Baduo Mitra Solution_";
                if ($user->aktif) {
                    $wa = new GowaService();
                    $result = $wa->sendText($user->no_hp, $pesan_pulang);
                    WhatsAppLog::create([
                        'user_id'  => $user->id,
                        'phone'    => $user->no_hp,
                        'message'  => $pesan_pulang,
                        'status'   => $result['code'] ?? false,
                        'response' => $result,
                    ]);
                }

                return response()->json([
                    'message'      => $pesan_pulang,
                    'phone'        => $user->no_hp,
                    'user_id'      => $user->emp_code,
                    'time_out'     => $jamPulang,
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

            // Ambil semua nagari beserta user + jabatan yang aktif dan punya no_hp
            $nagaris = Nagari::with(['users' => function ($query) {
                $query->where('aktif', true)
                    ->whereNotNull('no_hp')
                    ->whereRaw("CAST(no_hp AS TEXT) != ''")
                    ->with('jabatan');
            }])->whereHas('users', function ($query) {
                $query->where('aktif', true)
                    ->whereNotNull('no_hp')
                    ->whereRaw("CAST(no_hp AS TEXT) != ''");
            })->get();

            if ($nagaris->isEmpty()) {
                return response()->json([
                    'error' => 'Tidak ada nagari dengan pegawai aktif yang punya nomor HP'
                ], 404);
            }

            // Generate PDF untuk setiap nagari dan simpan
            $pdfFiles = [];
            foreach ($nagaris as $nagari) {
                try {
                    $report = $service->generate($tahun, $bulan, $nagari->id);

                    if ($report && isset($report['pdf']) && isset($report['filename'])) {
                        $filename = $report['filename'];
                        $storagePath = $report['path'];
                        $fullPath = storage_path("app/private/{$storagePath}");

                        // Pastikan direktori ada
                        $directory = dirname($fullPath);
                        if (!is_dir($directory)) {
                            mkdir($directory, 0755, true);
                        }

                        // Save PDF ke storage
                        $pdfContent = $report['pdf']->output();
                        Storage::put($storagePath, $pdfContent);

                        $pdfFiles[] = [
                            'nagari' => $nagari,
                            'filename' => $filename,
                            'storage_path' => $storagePath,
                            'full_path' => $fullPath
                        ];

                        Log::info('PDF generated successfully', [
                            'nagari' => $nagari->name,
                            'filename' => $filename,
                            'file_size' => strlen($pdfContent)
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to generate report for nagari', [
                        'nagari_id' => $nagari->id,
                        'nagari_name' => $nagari->name,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            if (empty($pdfFiles)) {
                return response()->json([
                    'error' => 'Gagal membuat laporan PDF untuk semua nagari'
                ], 500);
            }

            // Kirim WhatsApp ke semua user dengan PDF masing-masing nagari
            $response = [];
            $totalSent = 0;
            $totalFailed = 0;

            foreach ($pdfFiles as $pdfFile) {
                $nagari = $pdfFile['nagari'];
                $filename = $pdfFile['filename'];
                $fullPath = $pdfFile['full_path'];

                foreach ($nagari->users as $user) {
                    $jabatan = $user->jabatan->name ?? 'Tidak ada jabatan';
                    $pesan = "Hai *{$user->name}* (Jabatan: {$jabatan}),\n\n" .
                        "Pegawai Nagari {$nagari->name}\n\n" .
                        "📊 Laporan Absensi Bulan *{$bulan}* Tahun *{$tahun}*\n\n" .
                        "Laporan ini dikirim setiap awal bulan untuk ditinjau kembali.\n" .
                        "Laporan PDF dapat disimpan untuk dokumentasi.\n" .
                        "_Note: Dikirim ke seluruh pegawai dan pimpinan_\n\n" .
                        "Ketik: *info* untuk bantuan lebih lanjut.\n\n" .
                        "_Sent via Cv.Baduo Mitra Solution_";

                    try {
                        // Verifikasi file exists
                        if (!file_exists($fullPath)) {
                            throw new \Exception("File PDF tidak ditemukan: {$fullPath}");
                        }

                        $gowa = new GowaService();
                        $result = $gowa->sendFile(
                            phone: $user->no_hp, // Kirim ke user asli
                            // phone: "6281282779593", // Comment untuk production
                            path: $fullPath,
                            caption: $pesan
                        );

                        $response[] = [
                            'user_id' => $user->id,
                            'name' => $user->name,
                            'phone' => $user->no_hp,
                            'nagari' => $nagari->name,
                            'status' => 'sent',
                            'pdf_file' => $filename,
                            'response' => $result
                        ];
                        $totalSent++;

                        // Log WhatsApp success
                        WhatsAppLog::create([
                            'user_id' => $user->id,
                            'phone' => $user->no_hp,
                            'message' => $pesan,
                            'status' => $result['code'] ?? false,
                            'response' => $result,
                        ]);
                    } catch (\Exception $e) {
                        $response[] = [
                            'user_id' => $user->id,
                            'name' => $user->name,
                            'phone' => $user->no_hp,
                            'nagari' => $nagari->name,
                            'status' => 'failed',
                            'pdf_file' => $filename,
                            'error' => $e->getMessage()
                        ];
                        $totalFailed++;

                        // Log WhatsApp error
                        WhatsAppLog::create([
                            'user_id' => $user->id,
                            'phone' => $user->no_hp,
                            'message' => $pesan,
                            'status' => false,
                            'response' => ['error' => $e->getMessage()],
                        ]);
                    }
                    // Delay untuk avoid rate limiting
                    usleep(500000); // 0.5 second delay
                }
            }

            // Response API
            return response()->json([
                'success' => true,
                'generated_files' => array_map(function ($file) {
                    return [
                        'nagari' => $file['nagari']->name,
                        'filename' => $file['filename'],
                        'file_size' => file_exists($file['full_path']) ? filesize($file['full_path']) . ' bytes' : 'File not found',
                        'url' => Storage::url($file['storage_path'])
                    ];
                }, $pdfFiles),
                'summary' => [
                    'total_nagari' => count($pdfFiles),
                    'total_users' => $nagaris->sum(fn($n) => $n->users->count()),
                    'total_sent' => $totalSent,
                    'total_failed' => $totalFailed
                ],
                'whatsapp_results' => $response
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

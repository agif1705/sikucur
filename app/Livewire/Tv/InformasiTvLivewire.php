<?php

namespace App\Livewire\Tv;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Nagari;
use Livewire\Component;
use App\Models\WdmsModel;
use App\Models\ListYoutube;
use App\Models\WhatsAppLog;
use Livewire\Attributes\On;
use App\Services\GowaService;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Log;
use App\Models\RekapAbsensiPegawai;

/**
 * Komponen Livewire untuk menampilkan informasi di TV
 * Menangani absensi fingerprint real-time dan menampilkan data nagari
 */

class InformasiTvLivewire extends Component
{
    /**
     * Tanggal hari ini
     */
    public $now;

    /**
     * Data users yang absen hari ini
     */
    public $users;

    /**
     * Data TV informasi dan galeri
     */
    public $tv, $galeri;

    /**
     * Serial number fingerprint, logo nagari, tanggal TV, data, playlist
     */
    public $sn_fp, $logo, $tvNow, $datas, $playlistStr;

    /**
     * ID video YouTube default
     */
    public $videoId = "Lb4AwReHYxQ";

    /**
     * Event listener untuk update data fingerprint
     * Menangani absensi masuk dan pulang pegawai
     */
    #[On('fingerprint-updated')]
    public function updateData($mesin, $data)
    {
        try {
            // Validasi data input
            if (!isset($data['emp_id']) && !isset($data['emp_code'])) {
                return response()->json(['message' => 'Employee ID tidak ditemukan'], 400);
            }

            // Ambil employee ID
            $emp_id = $data['emp_id'] ?? intval($data['emp_code']);

            // Validasi punch_time
            if (!isset($data['punch_time'])) {
                return response()->json(['message' => 'Waktu absensi tidak valid'], 400);
            }

            $punchTime = Carbon::parse($data['punch_time']);
            $today = $punchTime->format('Y-m-d');
            $timeFormatted = $punchTime->format('H:i');

            // Tentukan status keterlambatan
            $isLate = $punchTime->format('H:i') > '08:00';
            $statusKehadiran = $isLate ? 'Terlambat' : 'Tepat Waktu';

            // Cari nagari berdasarkan serial number mesin
            $nagari = Nagari::where('sn_fingerprint', $data['terminal_sn'])->first();
            if (!$nagari) {
                return response()->json(['message' => 'Mesin fingerprint tidak ditemukan'], 404);
            }

            // Cari user berdasarkan employee ID dan nagari
            $user = User::where('emp_id', $emp_id)
                ->where('nagari_id', $nagari->id)
                ->with(['nagari', 'jabatan'])
                ->first();

            if (!$user) {
                return response()->json(['message' => 'Pegawai tidak ditemukan'], 404);
            }

            // Cek apakah sudah absen hari ini
            $absensiHariIni = RekapAbsensiPegawai::where('sn_mesin', $data['terminal_sn'])
                ->where('user_id', $user->id)
                ->whereDate('date', $today)
                ->first();

            if (!$absensiHariIni) {
                // Absensi masuk (pertama kali hari ini)
                $this->createAbsensiMasuk($user, $nagari, $data, $punchTime, $isLate, $statusKehadiran);
            } else {
                // Absensi pulang (jika waktu setelah jam 12:00)
                if ($punchTime->hour >= 12) {
                    $this->updateAbsensiPulang($absensiHariIni, $user, $punchTime);
                }
            }

            // Dispatch event untuk update UI
            $this->dispatch('absenBerhasil',
                nama: $user->name,
                jam: $timeFormatted,
                status: $statusKehadiran
            );

            // Update data users untuk tampilan
            $this->users = WdmsModel::getAbsensiMasuk($mesin);

        } catch (\Exception $e) {
            // Log error jika diperlukan
            Log::error('Error processing fingerprint data: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat memproses absensi'], 500);
        }
    }

    /**
     * Membuat record absensi masuk baru
     */
    private function createAbsensiMasuk($user, $nagari, $data, $punchTime, $isLate, $statusKehadiran)
    {
        // Buat pesan WhatsApp untuk absensi masuk
        $pesanMasuk = $this->generatePesanAbsensiMasuk($user, $punchTime, $statusKehadiran);

        // Simpan data absensi
        $absensi = RekapAbsensiPegawai::create([
            'user_id'        => $user->id,
            'nagari_id'      => $nagari->id,
            'is_late'        => $isLate,
            'status_absensi' => 'Hadir',
            'sn_mesin'       => $data['terminal_sn'],
            'resource'       => 'Fingerprint',
            'id_resource'    => 'fp-' . ($data['id'] ?? 'unknown'),
            'time_in'        => $punchTime->format('H:i'),
            'date'           => $punchTime->format('Y-m-d'),
        ]);

        // Kirim WhatsApp jika user aktif dan perubahan
        if ($user->aktif) {
            $this->sendWhatsAppNotification($user, $pesanMasuk);
        }
    }

    /**
     * Update absensi untuk jam pulang
     */
    private function updateAbsensiPulang($absensi, $user, $punchTime)
    {
        // Update waktu pulang
        $absensi->update([
            'time_out' => $punchTime->format('H:i'),
        ]);

        // Buat pesan WhatsApp untuk absensi pulang
        $pesanPulang = $this->generatePesanAbsensiPulang($user, $punchTime);

        // Kirim WhatsApp jika user aktif
        if ($user->aktif && $user->no_hp) {
            $this->sendWhatsAppNotification($user, $pesanPulang);
        }
    }

    /**
     * Generate pesan WhatsApp untuk absensi masuk
     */
    private function generatePesanAbsensiMasuk($user, $punchTime, $statusKehadiran)
    {
        $jabatan = $user->jabatan->name ?? 'Tidak ada jabatan';
        $nagariName = $user->nagari->name ?? 'Unknown';

        return "Hai *{$user->name}* (Jabatan: {$jabatan}),\n\n" .
               "✅ Status Kehadiran: *{$statusKehadiran}*\n" .
               "🕐 Waktu Absen: *{$punchTime->format('H:i')}*\n" .
               "📍 Lokasi: Nagari {$nagariName}\n" .
               "📱 Metode: Fingerprint\n\n" .
               "Data ini akan dikirim ke WhatsApp Wali Nagari {$nagariName} sebelum jam 10:05.\n\n" .
               "Ketik: *info* untuk melihat informasi perintah dan bantuan lebih lanjut.\n\n" .
               "_Sent via Cv.Baduo Mitra Solution_";
    }

    /**
     * Generate pesan WhatsApp untuk absensi pulang
     */
    private function generatePesanAbsensiPulang($user, $punchTime)
    {
        $nagariName = $user->nagari->name ?? 'Unknown';

        return "Hai *{$user->name}*,\n\n" .
               "✅ Absensi pulang berhasil\n" .
               "🕐 Waktu Pulang: *{$punchTime->format('H:i')}*\n" .
               "📍 Lokasi: Nagari {$nagariName}\n" .
               "📱 Metode: Fingerprint\n\n" .
               "Terima kasih atas dedikasi Anda hari ini.\n\n" .
               "Ketik: *info* untuk melihat informasi perintah dan bantuan lebih lanjut.\n\n" .
               "_Sent via Cv.Baduo Mitra Solution_";
    }

    /**
     * Kirim notifikasi WhatsApp dan log hasilnya
     */
    private function sendWhatsAppNotification($user, $message)
    {
        try {
            $wa = new GowaService();
            $result = $wa->sendText($user->no_hp, $message);

            // Log hasil pengiriman WhatsApp
            WhatsAppLog::create([
                'user_id' => $user->id,
                'phone'   => $user->no_hp,
                'message' => $message,
                'status'  => $result['code'] ?? false,
                'response' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending WhatsApp: ' . $e->getMessage());
        }
    }

    /**
     * Event listener untuk update rekap absensi
     */
    #[On('rekap-absensi-updated')]
    public function rekapAbsensiUpdated($data)
    {
        $user = User::whereId($data['user_id'])->first();
        if ($user) {
            $statusKehadiran = $data['is_late'] ? 'Terlambat' : 'Tepat Waktu';
            $this->dispatch('absenBerhasil',
                nama: $user->name,
                jam: $data['time_in'],
                status: $statusKehadiran
            );
        }
    }

    /**
     * Event listener untuk penghapusan data fingerprint
     */
    #[On('fingerprint-deleted')]
    public function deleteData()
    {
        $this->users = WdmsModel::getAbsensiMasuk($this->sn_fp);
    }

    /**
     * Render komponen dengan layout TV
     */
    #[Layout('components.layouts.tv')]
    public function render()
    {
        return view('livewire.tv.informasi-tv-livewire');
    }

    /**
     * Inisialisasi komponen dengan data nagari dan pengaturan
     */
    public function mount($sn)
    {
        try {
            // Set tanggal hari ini
            $this->now = Carbon::now()->format('Y-m-d');
            $this->tvNow = Carbon::now()->format('d M Y');

            // Cari nagari berdasarkan slug dengan eager loading
            $nagari = Nagari::with(['TvInformasi', 'galeri'])
                ->where('slug', $sn)
                ->first();

            if (!$nagari) {
                abort(404, 'Nagari tidak ditemukan');
            }

            // Set data nagari
            $this->logo = $nagari->logo;
            $this->sn_fp = $nagari->sn_fingerprint;
            $this->tv = $nagari->TvInformasi;
            $this->galeri = $nagari->galeri->take(10);

            // Ambil playlist YouTube dengan optimasi query
            $this->setupYouTubePlaylist($nagari->id);

            // Ambil data absensi hari ini
            $this->users = WdmsModel::getAbsensiMasuk($this->sn_fp);

        } catch (\Exception $e) {
            Log::error('Error in mount method: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan saat memuat data');
        }
    }

    /**
     * Setup playlist YouTube untuk nagari
     */
    private function setupYouTubePlaylist($nagariId)
    {
        try {
            $youtubeList = ListYoutube::where('nagari_id', $nagariId)
                ->select('id_youtube')
                ->get();

            if ($youtubeList->isNotEmpty()) {
                $this->playlistStr = $youtubeList->pluck('id_youtube')->implode(',');
            } else {
                // Default playlist jika tidak ada
                $this->playlistStr = $this->videoId;
            }
        } catch (\Exception $e) {
            Log::error('Error setting up YouTube playlist: ' . $e->getMessage());
            $this->playlistStr = $this->videoId;
        }
    }
}
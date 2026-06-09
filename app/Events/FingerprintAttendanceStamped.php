<?php

namespace App\Events;

use App\Models\RekapAbsensiPegawai;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class FingerprintAttendanceStamped implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public RekapAbsensiPegawai $attendance,
        public string $type,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin.attendance'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'fingerprint.stamped';
    }

    public function broadcastWith(): array
    {
        $attendance = $this->attendance->loadMissing('user.nagari');
        $user = $attendance->user;
        $nagari = $attendance->nagari ?? $user?->nagari;
        $date = $attendance->date ? Carbon::parse($attendance->date)->format('Y-m-d') : null;

        return [
            'id' => $attendance->id,
            'type' => $this->type,
            'title' => $this->type === 'out' ? 'Absensi pulang fingerprint' : 'Absensi masuk fingerprint',
            'employee_name' => $user?->name ?? 'Pegawai tidak ditemukan',
            'nagari_name' => $nagari?->name,
            'sn_mesin' => $attendance->sn_mesin,
            'date' => $date,
            'time' => $this->type === 'out' ? $attendance->time_out : $attendance->time_in,
            'status' => $attendance->status_absensi,
            'is_late' => (bool) $attendance->is_late,
            'resource' => $attendance->resource,
        ];
    }
}

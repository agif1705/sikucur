<?php

namespace App\Handlers;

use App\Models\WhatsAppCommand;

class InfoPerintahHandler implements \App\Contracts\WhatsAppCommandHandler
{
    /**App\Handlers\InfoPerintahHandler
     * Create a new class instance.
     */
    public function handle($user, $chat, $data)
    {
        // ambil semua command yang dimiliki nagari user ini
        $commands = WhatsAppCommand::where('nagari_id', $user->nagari_id)
            ->select('command', 'description') // misal ada kolom description
            ->get();
        if ($commands->isEmpty()) {
            return [
                'success' => false,
                'message' => "Tidak ada perintah tersedia untuk nagari {$user->nagari->name}.",
                'data' => null,
            ];
        }
        $pesan = "📊 *Daftar perintah tersedia untuk nagari {$user->nagari->name}* \n ";
        foreach ($commands as $i => $item) {
            $no = $i + 1;
            $pesan .= "\n{$no}. *{$item->command}* → {$item->description}\n";
        }

        return [
            'success' => true,
            'message' => "{$pesan} \n *Contoh perintah ketik dan kirim →*  \"absensi-pegawai\" maka akan wa membalas",
            'data'    => [
                'pegawai' => true,
                'data' => $data,
            ],
        ];
    }
}

<?php

namespace App\Handlers;

use App\Contracts\WhatsAppCommandHandler;
use App\Models\IzinPegawai;
use Illuminate\Support\Facades\URL;

class IzinPegawaiHandler implements WhatsAppCommandHandler
{
    public function handle($user, $chat, $data)
    {
        $checkizin = IzinPegawai::where('user_id', $user->id)
            ->where('nagari', $user->nagari->name)
            ->where('expired_at', '>', now())
            ->where('used', false)
            ->first();

        if ($checkizin) {
            $text = "Link izin pegawai sudah ada dan masih berlaku, jika habis waktunya bisa ketikan lagi {$chat}.";
            $url = $this->generateIzinUrl($checkizin->link, $user->nagari->name, $checkizin->expired_at);
            return [
                'success' => true,
                'message' => $this->messageWhatsApp($text, [
                    'name' => $user->name,
                    'nagari' => $user->nagari->name,
                    'link' => $url,
                    'chat' => $chat,
                    'admin' => "AdminNagari",
                ]),
                'data'    => [
                    'pegawai' => true,
                    'data' => $data,
                ],
            ];
        }

        $link = $this->generateUniqueLink();
        $expired_at = now()->addMinutes(30);

        IzinPegawai::create([
            'user_id'    => $user->id,
            'used'       => false,
            'link'       => $link,
            'nagari'     => $user->nagari->name,
            'expired_at' => $expired_at,
        ]);
        $text = "*Link izin berhasil dibuat* \n";
        $url = $this->generateIzinUrl($link, $user->nagari->name, $expired_at);

        return [
            'success' => true,
            'message' => $this->messageWhatsApp($text, [
                'name' => $user->name,
                'nagari' => $user->nagari->name,
                'link' => $url,
                'chat' => $chat,
                'admin' => "AdminNagari",
            ]),
            'data'    => [
                'pegawai' => true,
                'data' => $data,
            ]
        ];
    }

    private function generateIzinUrl($link, $nagari, $expired_at)
    {
        return URL::temporarySignedRoute('izin-pegawai.form', $expired_at, [
            'link'       => $link,
            'nagari'     => $nagari,
            'expired_at' => $expired_at->timestamp,
        ]);
    }

    private function generateUniqueLink()
    {
        return uniqid('izin_', true);
    }
    private function messageWhatsApp($text, $message)
    {
        $replay = $text . " " . "Halo Pegawai {$message['name']} Di Nagari *{$message['nagari']}* .\n Silakan klik link berikut untuk mengisi izin: \n" . "
        {$message['link']}\n Link ini hanya berlaku selama 30 menit. Mohon segera mengisi absensi ini untuk keperluan:\n  {$message['chat']}\n \n Jika ada pertanyaan, silakan hubungi admin: {$message['admin']}\nTerima kasih!, jika habis waktunya bisa ketikan lagi {$message['chat']}. \nketik : info -> untuk melihat informasi perintah dan bantuan lebih lanjut.";
        return $replay;
    }
}

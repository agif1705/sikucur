<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\WhatsAppCommand;
use App\Class\WhatsAppMessageClass;
use App\Http\Controllers\Controller;

class WusapiController extends Controller
{
    public function webhook(Request $request)
    {
        // $token = WAG::setUserToken('bp6448fe');
        // Ambil data JSON dari webhook
        $payload = $request->all();
        // Simpan payload ke log Laravel
        $WA = WhatsAppMessageClass::GetData($payload);
        $sender = $WA->getSender();
        $message = Str::lower(Str::replace(' ', '', $WA->getConversation()));
        $this->perintah($sender, $message);
        return response()->json(['status' => 'success'], 200);
    }
    public function perintah($sender, $message): string
    {
        // Cari command di database
        $pegawai = User::whereNoHp($sender)->first();
        $command = WhatsAppCommand::where('command', $message)->first();

        if ($command) {
            // Jika command ditemukan
            switch ($command->command) {
                case 'izin':
                    return $this->izin($sender, $message);
                default:
                    return $command->response;
            }
        }

        // Default response jika command tidak ditemukan
        return 'Guest';
    }

    public function izin($sender, $message): string
    {
        // Logika khusus untuk perintah izin
        return "Administrator";
    }
}

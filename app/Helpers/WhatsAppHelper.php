<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class WhatsAppHelper
{
    public static function sendMessage(string $target, string $message)
    {
        // $baduo = " \n \n \n \n _Sent || via *Cv.Baduo Mitra Solustion*_";
        // // $wagClient->setUserToken('your-user-token');
        // try {
        //     WAG::setUserToken('saguba39');
        //     $response = WAG::chat()->sendSimpleText($target, $message . ' ' . $baduo);
        // } catch (RequestException $e) {
        //     // Log the error or handle it as needed
        //     throw new \Exception('Failed to send message: ' . $e->getMessage());
        // }
    }
}

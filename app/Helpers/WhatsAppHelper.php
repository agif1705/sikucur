<?php

namespace App\Helpers;

use WAG\LaravelSDK\WAGClient;
use WAG\LaravelSDK\Facades\WAG;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class WhatsAppHelper
{
    /**
     * Mengirim data ke API Fonnte
     */

    //     public static function sendToFonnte(string $target, string $massage): array
    //     {
        //         $wagClient = WAG::setUserToken('bp6448fe');
        //         $wagClient->setUserToken('your-user-token');
        //        $wagClient->setUserToken('your-user-token');
        //        // ...
        //    }
        public static function sendMessage( string $target, string $message)
        {
            $baduo = " \n \n \n \n _Sent || via *Cv.Baduo Mitra Solustion*_";
            // $wagClient->setUserToken('your-user-token');
            try {
                WAG::setUserToken('bp6448fe');
                $response = WAG::chat()->sendSimpleText($target, $message.' '.$baduo);
        } catch (RequestException $e) {
            // Log the error or handle it as needed
            throw new \Exception('Failed to send message: ' . $e->getMessage());
        }
    }
    
}

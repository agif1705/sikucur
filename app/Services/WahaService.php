<?php

namespace App\Services;

use CCK\LaravelWahaSaloonSdk\Waha\Waha;

class WahaService
{
    protected $waha;
    protected $session;

    public function __construct($session = 'default')
    {
        $this->waha = new Waha();
        $this->session = $session;

        // start session (kalau WAHA belum aktif akan di-start)
        $this->waha->sessions()->startTheSession($this->session);
    }

    public function sendText($to, $message)
    {
        $response = $this->waha->sendText()->sendTextMessage(
            chatId: $to . '@c.us',
            text: $message,
            session: $this->session,
            replyTo: null,
            linkPreview: null,
            linkPreviewHighQuality: null
        );

        return $response->json(); // bisa juga return langsung $response
    }

    public function sendFile($to, $file)
    {
        $imageBase64 = base64_encode(file_get_contents('/path/to/image.jpg'));
        $response = $this->waha->misc()->sendImage(
            chatId: $to . '@c.us',
            file: $imageBase64,
            session: $this->session,
            replyTo: null,
            caption: 'Image from server storage'
        );

        return $response->json(); // bisa juga return langsung $response    
    }
}

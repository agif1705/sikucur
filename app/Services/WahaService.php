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
}

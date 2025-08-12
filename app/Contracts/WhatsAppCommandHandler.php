<?php

namespace App\Contracts;

interface WhatsAppCommandHandler
{
    public function handle($user, $chat, $data);
}

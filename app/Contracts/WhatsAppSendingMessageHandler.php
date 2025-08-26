<?php

namespace App\Contracts;

interface WhatsAppSendingMessageHandler
{
    public function handle($chatId, $text);
}

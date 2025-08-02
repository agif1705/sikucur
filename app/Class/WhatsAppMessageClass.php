<?php

namespace App\Class;

class WhatsAppMessageClass
{
    public function __construct(
        public string $type,
        public string $conversation,
        public string $sender,
        public string $pushName,
        public string $timestamp,
        public string $instanceName,
        public string $token
    ) {}

    public static function GetData(array $data): ?self
    {
        // Validasi dasar
        if (!isset($data['jsonData']) || !is_string($data['jsonData'])) {
            throw new \InvalidArgumentException('Invalid or missing jsonData');
        }

        $jsonData = json_decode($data['jsonData'], true);

        // Validasi JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON format in jsonData');
        }

        // Case-insensitive type check
        if (strtolower($jsonData['type'] ?? '') !== 'message') {
            return null;
        }

        // Validasi struktur data
        if (
            !isset($jsonData['event']['Message']['conversation']) ||
            !isset($jsonData['event']['Info']['Sender'])
        ) {
            throw new \InvalidArgumentException('Invalid message structure');
        }

        return new self(
            type: $jsonData['type'],
            conversation: $jsonData['event']['Message']['conversation'],
            sender: $jsonData['event']['Info']['Sender'],
            pushName: $jsonData['event']['Info']['PushName'] ?? '',
            timestamp: $jsonData['event']['Info']['Timestamp'] ?? '',
            instanceName: $data['instanceName'] ?? '',
            token: $data['token'] ?? ''
        );
    }
    /**
     * Get the message type
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the message content
     */
    public function getConversation(): string
    {
        return $this->conversation;
    }

    /**
     * Get the sender's WhatsApp ID
     */
    public function getSender(): string
    {
        $numberPart = explode('@', $this->sender)[0];

        // Hilangkan kode negara (62) dan ambil sisanya
        $phoneNumber = substr($numberPart, 2);
        return $phoneNumber;
    }


    /**
     * Get the sender's display name
     */
    public function getPushName(): string
    {
        return $this->pushName;
    }

    /**
     * Get the message timestamp
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * Get the instance name
     */
    public function getInstanceName(): string
    {
        return $this->instanceName;
    }

    /**
     * Get the authentication token
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Convert the DTO to array
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'conversation' => $this->conversation,
            'sender' => $this->sender,
            'pushName' => $this->pushName,
            'timestamp' => $this->timestamp,
            'instanceName' => $this->instanceName,
            'token' => $this->token,
        ];
    }
}

<?php

namespace App\Features\TempStorage\Serializer;

use App\Features\TempStorage\DTO\Message\TempStorageMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class TempStorageMessageSerializer implements SerializerInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        $data = null;
        try {
            $data = json_decode($encodedEnvelope['body'], true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $this->logger->error($e->getMessage());
        }

        return new Envelope(new TempStorageMessage($data));
    }

    public function encode(Envelope $envelope): array
    {
        $message = $envelope->getMessage();
        $data = $message->data;

        return [
            'body' => json_encode($data),
            'headers' => [],
        ];
    }
}
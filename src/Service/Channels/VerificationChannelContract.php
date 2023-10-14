<?php

namespace Esca7a\Verification\Service\Channels;

interface VerificationChannelContract
{
    /**
     * Реализует механизм отправки сообщения
     */
    public function send(string $verifyValue, string $message): void;

    /**
     * Получение название канала обработчика
     */
    public function code(): string;
}
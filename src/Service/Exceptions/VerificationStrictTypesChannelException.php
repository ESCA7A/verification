<?php

namespace Esca7a\Verification\Service\Exceptions;

use Esca7a\Verification\Service\VerificationService;

class VerificationStrictTypesChannelException extends AbstractVerificationException
{
    protected $message = 'Не установлен канал связи';

    public function getReasonCode(): string
    {
        return 'STRICT_TYPES_CHANNEL_EXCEPTION';
    }

    public function getAdditional(VerificationService $service): mixed
    {
        return null;
    }
}
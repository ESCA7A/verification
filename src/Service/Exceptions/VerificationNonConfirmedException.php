<?php

namespace Esca7a\Verification\Service\Exceptions;

use Esca7a\Verification\Service\VerificationService;

class VerificationNonConfirmedException extends AbstractVerificationException
{
    protected $message = 'Верификация не удалась. Возможно вы вводите неверный код';

    public function getReasonCode(): string
    {
        return 'VERIFICATION_NON_CONFIRMED';
    }

    public function getAdditional(VerificationService $service): mixed
    {
        return null;
    }
}
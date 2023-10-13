<?php

namespace Esca7a\Verification\Service\Exceptions;

use Esca7a\Verification\Service\VerificationService;

class VerificationStrictTypesVerifyValueException extends AbstractVerificationException
{
    protected $message = 'Не установлен адрес верификации';

    public function getReasonCode(): string
    {
        return 'STRICT_TYPES_VERIFY_VALUE_EXCEPTION';
    }

    public function getAdditional(VerificationService $service): mixed
    {
        return null;
    }
}
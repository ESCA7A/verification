<?php

namespace Esca7a\Verification\Service\Exceptions;

use Esca7a\Verification\Service\VerificationService;

class VerificationAddRecordException extends AbstractVerificationException
{
    protected $message = 'Не удалось создать запись';

    public function getReasonCode(): string
    {
        return 'CANNOT_ADD_VERIFICATION_RECORD';
    }

    public function getAdditional(VerificationService $service): mixed
    {
        return null;
    }
}
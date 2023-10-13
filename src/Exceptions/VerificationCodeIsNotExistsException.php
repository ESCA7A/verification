<?php

namespace Esca7a\Verification\Exceptions;

use Esca7a\Verification\VerificationService;

class VerificationCodeIsNotExistsException extends AbstractVerificationException
{
    protected $message = 'Код не существует';

    public function getReasonCode(): string
    {
        return 'VERIFICATION_CODE_IS_NOT_EXISTS';
    }

    public function getAdditional(VerificationService $service): mixed
    {
        return null;
    }
}
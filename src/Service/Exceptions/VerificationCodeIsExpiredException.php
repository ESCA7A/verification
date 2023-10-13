<?php

namespace Esca7a\Verification\Service\Exceptions;

use Esca7a\Verification\Service\VerificationService;

class VerificationCodeIsExpiredException extends AbstractVerificationException
{
    protected $message = 'Код устарел';

    public function getReasonCode(): string
    {
        return 'VERIFICATION_CODE_IS_EXPIRED';
    }

    public function getAdditional(VerificationService $service): mixed
    {
        return null;
    }
}
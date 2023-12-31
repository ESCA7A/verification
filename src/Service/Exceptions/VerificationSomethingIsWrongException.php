<?php

namespace Esca7a\Verification\Service\Exceptions;

use Esca7a\Verification\Service\VerificationService;

class VerificationSomethingIsWrongException extends AbstractVerificationException
{
    protected $message = 'Что-то пошло не так';

    public function getReasonCode(): string
    {
        return 'VERIFICATION_SOMETHING_IS_WRONG';
    }

    public function getAdditional(VerificationService $service): mixed
    {
        return null;
    }
}
<?php

namespace Esca7a\Verification\Service\Exceptions;

class VerificationSendingIsUnavailableException extends AbstractVerificationException
{
    protected $message = 'Время для повторной отправки кода не наступило';

    public function getReasonCode(): string
    {
        return 'VERIFICATION_SENDING_CODE_IS_TEMPORARILY_UNAVAILABLE';
    }

    public function getAdditional($service): mixed
    {
        $repository = $service->getRepository();

        return $repository->pauseExpiredTime();
    }
}
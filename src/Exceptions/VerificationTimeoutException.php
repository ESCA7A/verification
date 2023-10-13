<?php

namespace Esca7a\Verification\Exceptions;

use Esca7a\Verification\VerificationService;

class VerificationTimeoutException extends AbstractVerificationException
{
    protected $message = 'Установлен временный таймаут';

    public function getReasonCode(): string
    {
        return 'TIMEOUT_EXCEPTION';
    }

    public function getAdditional(VerificationService $service): mixed
    {
        $repository = $service->getRepository();

        return $repository->timeoutExpiredTime();
    }
}
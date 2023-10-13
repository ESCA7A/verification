<?php

namespace Esca7a\Verification\Actions;

use Esca7a\Verification\VerificationService;
use Esca7a\Verification\Exceptions\VerificationSendingIsUnavailableException;
use Esca7a\Verification\Exceptions\VerificationTimeoutException;

class SendAction
{
    public function run(VerificationService $service, $repository): void
    {
        $service->checkStrictType();

        $repository->prepare();

        if ($repository->pauseIsSet()) {
            throw new VerificationSendingIsUnavailableException;
        }

        if ($repository->timeoutIsSet()) {
            throw new VerificationTimeoutException;
        }

        $record = $repository->create();
        $repository->flushWithout($record);
    }
}
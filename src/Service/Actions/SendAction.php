<?php

namespace Esca7a\Verification\Service\Actions;

use Esca7a\Verification\Service\VerificationService;
use Esca7a\Verification\Service\Exceptions\VerificationSendingIsUnavailableException;
use Esca7a\Verification\Service\Exceptions\VerificationTimeoutException;

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
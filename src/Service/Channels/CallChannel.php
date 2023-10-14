<?php

namespace Esca7a\Verification\Service\Channels;

use Support\Services\SmsServiceAggregator;

class CallChannel implements VerificationChannelContract
{
    public function code(): string
    {
        return 'call';
    }

    public function send(string $verifyValue, string $message): void
    {
        $service = new SmsServiceAggregator();
        $service->sendCall($verifyValue, $message);
    }
}
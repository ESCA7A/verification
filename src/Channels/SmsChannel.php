<?php

namespace Esca7a\Verification\Channels;

use Support\Services\SmsServiceAggregator;

class SmsChannel implements VerificationChannelContract
{
    public function code(): string
    {
        return 'sms';
    }

    public function send(string $verifyValue, string $message): void
    {
        $service = new SmsServiceAggregator();
        $service->send($verifyValue, $message);
    }

}
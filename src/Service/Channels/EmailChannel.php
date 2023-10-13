<?php

namespace Esca7a\Verification\Service\Channels;

use Domain\Mail\VerificationRequestMail as MailVerify;
use Esca7a\Verification\Service\Exceptions\VerificationFailedMessageFromChannelException;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EmailChannel implements VerificationChannelContract
{
    public function code(): string
    {
        return 'email';
    }

    public function send(string $verifyValue, string $message): void
    {
        $mailVerify = new MailVerify($verifyValue, $message);

        try {
            Mail::raw($message, function ($verificationRequest) use ($verifyValue) {
                $verificationRequest->to($verifyValue);
            });
        } catch (Throwable $throwable) {
            throw new VerificationFailedMessageFromChannelException;
        }
    }

}
<?php

namespace Esca7a\Verification\Service\Exceptions;

use Esca7a\Verification\Service\VerificationService;

class VerificationFailedMessageFromChannelException extends AbstractVerificationException
{
    protected $message = 'Не удалось отправить сообщение с выбранного канала связи';

    public function getReasonCode(): string
    {
        return 'FAILED_TO_SEND_MESSAGE_FROM_SELECTED_CHANNEL';
    }

    public function getAdditional(VerificationService $service): mixed
    {
        return null;
    }
}
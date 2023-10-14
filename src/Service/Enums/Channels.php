<?php

namespace Esca7a\Verification\Service\Enums;

use Esca7a\Verification\Service\Channels\CallChannel;
use Esca7a\Verification\Service\Channels\EmailChannel;
use Esca7a\Verification\Service\Channels\SmsChannel;
use Esca7a\Verification\Service\Channels\VerificationChannelContract;

enum Channels: string
{
    case SMS = 'sms';
    case EMAIL = 'email';
    case CALL = 'call';

    public function instance(): ?VerificationChannelContract
    {
        return match ($this) {
            self::SMS => new SmsChannel(),
            self::EMAIL => new EmailChannel(),
            self::CALL => new CallChannel(),
        };
    }

    /**
     * Возвращает объект канала по значению
     */
    public static function findInstance(string $value): ?VerificationChannelContract
    {
        foreach (Channels::cases() as $case) {
            if ($case->value === $value) {
                return $case->instance();
            }
        }

        return null;
    }
}

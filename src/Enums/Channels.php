<?php

namespace Esca7a\Verification\Enums;

use Esca7a\Verification\Channels\CallChannel;
use Esca7a\Verification\Channels\EmailChannel;
use Esca7a\Verification\Channels\SmsChannel;
use Esca7a\Verification\Channels\VerificationChannelContract;

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

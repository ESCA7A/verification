<?php

namespace Esca7a\Verification\Service\Enums;

enum VerificationStatus: string
{
    case SEND       = 'send';
    case TIMEOUT    = 'timeout';
    case CONFIRMED  = 'confirmed';
    case EXPIRED    = 'expired';
    case ERROR      = 'error';

    public function title(): string
    {
        return match($this) {
            self::SEND      => __('отправлено'),
            self::TIMEOUT   => __('таймаут'),
            self::CONFIRMED => __('подтверждено'),
            self::EXPIRED   => __('истек'),
            self::ERROR     => __('ошибка'),
        };
    }
}
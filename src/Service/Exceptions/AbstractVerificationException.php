<?php

namespace Esca7a\Verification\Service\Exceptions;

use Esca7a\Verification\Service\VerificationService;
use Exception;

abstract class AbstractVerificationException extends Exception
{
    abstract public function getReasonCode(): string;

    /**
     * Возвращает какую-то дополнительную информацию для HTTP ответа
     * В основном метку времени
     */
    abstract public function getAdditional(VerificationService $service): mixed;
}
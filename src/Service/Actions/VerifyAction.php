<?php

namespace Esca7a\Verification\Actions;

use Esca7a\Verification\Exceptions\VerificationCodeIsExpiredException;
use Esca7a\Verification\Exceptions\VerificationCodeIsNotExistsException;
use Esca7a\Verification\Exceptions\VerificationSomethingIsWrongException;
use Esca7a\Verification\Repositories\VerificationServiceRepository;

class VerifyAction
{
    /**
     * Код введенный клиентом
     */
    private string $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function run(VerificationServiceRepository $repository): bool
    {
        if ($repository->codeIsNotExists($this->code)) {
            throw new VerificationCodeIsNotExistsException;
        }

        if ($repository->codeIsExpired($this->code)) {
            throw new VerificationCodeIsExpiredException;
        }

        $record = $repository->findByCode($this->code);

        if (is_null($record)) {
            throw new VerificationSomethingIsWrongException;
        }

        return $repository->update($record);
    }
}
<?php

namespace Esca7a\Verification\Users\Requests\Verification\Contracts;

use Esca7a\Verification\Users\Models\User;

interface VerificationRequestContract
{
    public function verifyValue(): ?string;
    public function userByRequest(): ?User;
    public function channel(): string;
}
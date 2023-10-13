<?php

namespace Esca7a\Verification\Users\Requests\Verification;

use Esca7a\Verification\Users\Models\User;
use Esca7a\Verification\Users\Requests\Verification\Contracts\VerificationContract;
use Esca7a\Verification\Service\Enums\Channels;
use Illuminate\Auth\Access\AuthorizationException;

class BaseEmailRequest extends BaseVerificationRequest implements VerificationContract
{
    protected function failedAuthorization()
    {
        throw new AuthorizationException("Такой адрес электронной почты уже зарегистрирован другим пользователем");
    }

    public function userByRequest(): ?User
    {
        return User::whereEmail($this->email)->get()->first();
    }

    public function verifyValue(): ?string
    {
        if ($this->has(['email'])) {
            return $this->email;
        }

        return null;
    }

    public function channel(): string
    {
        return Channels::EMAIL->value;
    }
}
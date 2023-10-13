<?php

namespace Esca7a\Verification\Users\Requests\Verification;

use Esca7a\Verification\Users\Models\User;
use Esca7a\Verification\Service\Enums\Channels;
use Illuminate\Auth\Access\AuthorizationException;

class BasePhoneRequest extends BaseVerificationRequest
{
    protected function failedAuthorization()
    {
        throw new AuthorizationException("Такой номер телефона уже зарегистрирован другим пользователем");
    }

    public function userByRequest(): ?User
    {
        return User::findByPhone($this->phone, $this->phone_country);
    }

    public function verifyValue(): ?string
    {
        if ($this->has(['phone', 'phone_country']) && $this->phone_country && $this->phone) {
            return uniform_format_phone(phone_number($this->phone, $this->phone_country));
        }

        return null;
    }

    public function channel(): string
    {
        return Channels::SMS->value;
    }
}
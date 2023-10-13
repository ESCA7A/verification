<?php

namespace Esca7a\Verification\Users\Requests\Verification;

use Esca7a\Verification\Users\Models\User;
use Esca7a\Verification\Users\Requests\Verification\Contracts\VerificationContract;
use Esca7a\Verification\Service\Enums\Channels;
use Support\BaseRequest;

/**
 * @property string $phone
 * @property string $phone_country
 * @property string $email
 * @property Channels $channel
 * @property string $verify_value
 * @property string $code
 */
abstract class BaseVerificationRequest extends BaseRequest implements VerificationContract
{
    /**
     * Для мапа в реквест значения verify_value
     */
    abstract public function verifyValue(): ?string;

    /**
     * Для мапа в реквест значения канала
     */
    abstract public function channel(): string;

    /**
     * Возвращает пользователя соответствующего данным из запроса
     */
    abstract public function userByRequest(): ?User;

    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'email' => 'электронная почта',
            'phone_country' => 'код страны',
            'phone' => 'номер телефона',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'verify_value' => $this->verifyValue(),
            'channel' => $this->channel(),
        ]);
    }
}
<?php

namespace Esca7a\Verification\Users\Requests\Verification;

use Esca7a\Verification\Users\Requests\Verification\Contracts\VerificationContract;
use Esca7a\Verification\Service\Enums\Channels;

/**
 * @property string $phone
 * @property string $phone_country
 * @property string $email
 * @property Channels $channel
 * @property string $verify_value
 * @property string $code
 */
class ConfirmEmailRequest extends BaseEmailRequest implements VerificationContract
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc,strict',],
            'code' => ['required', 'digits:'.config('verification.code_length')],
        ];
    }
}
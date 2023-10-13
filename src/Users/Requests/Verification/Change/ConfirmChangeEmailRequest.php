<?php

namespace Esca7a\Verification\Users\Requests\Verification\Change;

use Auth;
use Esca7a\Verification\Users\Requests\Verification\BaseEmailRequest;
use Esca7a\Verification\Users\Requests\Verification\Contracts\VerificationContract;
use Esca7a\Verification\Users\Rules\EmailBelongsUser;
use Esca7a\Verification\Service\Enums\Channels;

/**
 * @property string $phone
 * @property string $phone_country
 * @property string $email
 * @property Channels $channel
 * @property string $verify_value
 * @property string $code
 */
class ConfirmChangeEmailRequest extends BaseEmailRequest implements VerificationContract
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc,strict', new EmailBelongsUser],
            'code' => ['required', 'digits:'.config('verification.code_length')],
        ];
    }
}
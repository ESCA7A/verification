<?php

namespace Esca7a\Verification\Users\Requests\Verification\Change;

use Auth;
use Esca7a\Verification\Users\Requests\Verification\BasePhoneRequest;
use Esca7a\Verification\Users\Requests\Verification\Contracts\VerificationContract;
use Esca7a\Verification\Users\Rules\PhoneBelongsUser;
use Esca7a\Verification\Service\Enums\Channels;

/**
 * @property string $phone
 * @property string $phone_country
 * @property string $email
 * @property Channels $channel
 * @property string $verify_value
 * @property string $code
 */
class ConfirmChangePhoneRequest extends BasePhoneRequest implements VerificationContract
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'max:191', 'phone:AUTO,RU',],
            'phone_country' => ['required', 'string', 'max:191',],
            'verify_value' => [new PhoneBelongsUser],
            'code' => ['required', 'digits:'.config('verification.code_length')],
        ];
    }
}
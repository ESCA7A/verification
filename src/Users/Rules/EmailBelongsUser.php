<?php

namespace Esca7a\Verification\Users\Rules;

use Auth;
use Esca7a\Verification\Users\Models\User;
use Illuminate\Contracts\Validation\Rule;

class EmailBelongsUser implements Rule
{
    public function passes($attribute, $value): bool
    {
        $user = User::whereEmail($value)->first();

        $authUser = Auth::user();

        if (is_null($user)) {
            return true;
        }

        if ($user->email === $authUser->email) {
            return true;
        }

        return false;
    }

    public function message(): string
    {
        return __("Этот адрес электронной почты занят");
    }
}

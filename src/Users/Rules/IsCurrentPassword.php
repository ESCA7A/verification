<?php

namespace Esca7a\Verification\Users\Rules;

use Hash;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class IsCurrentPassword implements Rule
{

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $currentPasswordHash = Auth::user()->getAuthPassword();

        return (Hash::check("{$value}", $currentPasswordHash)
            || $value === $currentPasswordHash);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __("Неверно введен текущий пароль.");
    }
}

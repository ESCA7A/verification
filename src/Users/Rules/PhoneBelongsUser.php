<?php

namespace Esca7a\Verification\Users\Rules;

use Auth;
use Domain\Users\Models\User;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;

class PhoneBelongsUser implements Rule, DataAwareRule
{
    private array $data;

    /**
     * Если из реквеста приходит телефон и для него есть соответствие в базе, проверяется соответствие текущего юзера к телефону
     * Если текущий юзер соответствует введенному телефону, то запрос считается разрешенным
     *
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $phone = $this->data['phone'];
        $phoneCountry = $this->data['phone_country'];

        if (is_null($phone) || is_null($phoneCountry)) {
            return false;
        }

        /** @var User $user */
        $user = User::wherePhone($this->data['phone'])->wherePhoneCountry($this->data['phone_country'])->get()->first();

        $authUser = Auth::user();

        if (is_null($user)) {
            return true;
        }

        if ($user->phone === $authUser->phone && $user->phone_country === $authUser->phone_country) {
            return true;

        }

        return false;
    }

    public function message(): string
    {
        $phone = $this->data['phone'];
        $phoneCountry = $this->data['phone_country'];

        if (is_null($phone) || is_null($phoneCountry)) {
            return __("Такой телефон не существует");
        }

        return __("Этот номер телефона занят");
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}

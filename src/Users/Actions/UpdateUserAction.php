<?php

namespace Esca7a\Verification\Users\Actions;

use Domain\Users\DataTransferObjects\UserData;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

class UpdateUserAction
{
    public function run(UserData $data): User
    {
        try {
            return DB::transaction(function () use ($data) {
                $user = User::findOrFail($data->id);

                // сброс подтверждения email при его обновлении
                if ($user->email !== $data->email) {
                    $user->fill([
                        'email_verified_at' => null
                    ]);
                }

                // обновление данных
                $user->fill([
                    'name' => $data->name,
                    'surname' => $data->surname,
                    'patronymic' => $data->patronymic,
                    'phone' => $data->phone,
                    'phone_country' => $data->phone_country,
                    'email' => $data->email,
                    'city_id' => $data->city_id,
                    'address' => $data->address,
                    'postal_code' => $data->postal_code,
                    'card' => $data->bank_card,
                ]);

                // смена пароля
                if ($data->password) {
                    $user->fill(['password' => Hash::make($data->password)]);
                }

                $user->save();

                return $user;
            });
        } catch (Throwable $e) {
            log_errors($e);
            abort(422, __('Не получилось отредактировать пользователя'));
        }
    }
}

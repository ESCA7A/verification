<?php

namespace Esca7a\Verification\Users\Actions;

use Domain\Users\DataTransferObjects\UserData;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

class CreateUserAction
{
    public function run(UserData $data): User
    {
        try {
            return DB::transaction(function () use ($data) {
                $password = is_null($data->password) ? $data->password : Hash::make($data->password);

                $user = (new User())->create([
                    'imshop_identifier' => $data->imshop_identifier,
                    'name' => $data->name,
                    'surname' => $data->surname,
                    'patronymic' => $data->patronymic,
                    'phone' => $data->phone,
                    'phone_country' => $data->phone_country,
                    'email' => $data->email,
                    'password' => $password,
                    'phone_verified_at' => $data->phone_verified_at,
                    'email_verified_at' => $data->email_verified_at,
                ]);

                return $user;
            });
        } catch (Throwable $e) {
            log_errors($e);
            abort(422, __('Не получилось создать пользователя'));
        }
    }
}

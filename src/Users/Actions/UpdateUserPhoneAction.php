<?php

namespace Esca7a\Verification\Users\Actions;

use Domain\Users\Models\User;
use Domain\Users\Requests\Verification\Contracts\VerificationContract;
use Illuminate\Support\Facades\DB;
use Throwable;

class UpdateUserPhoneAction
{
    /**
     * Обновляет телефон юзера в таблице
     */
    public function run(User $user, VerificationContract $request): User
    {
        try {
            return DB::transaction(function () use ($user, $request) {
                // обновление данных
                $user->fill([
                    'phone' => $request->phone,
                    'phone_country' => $request->phone_country,
                    'phone_verified_at' => now(),
                ]);

                $user->save();
                return $user;
            });
        } catch (Throwable $e) {
            log_errors($e);
            abort(422, __('Не получилось сменить телефон пользователя'));
        }
    }
}

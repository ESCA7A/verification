<?php

namespace Esca7a\Verification\Users\Actions;

use Carbon\Carbon;
use Domain\Users\Models\User;
use Domain\Users\Requests\Verification\Contracts\VerificationContract;
use Illuminate\Support\Facades\DB;
use Throwable;

class UpdateUserEmailAction
{
    public function run(User $user, VerificationContract $request): User
    {
        try {
            return DB::transaction(function () use ($user, $request) {
                // обновление данных
                $user->fill([
                    'email' => $request->email,
                    'email_verified_at' => Carbon::now(),
                ]);

                $user->save();
                return $user;
            });
        } catch (Throwable $e) {
            log_errors($e);
            abort(422, __('Не получилось сменить email пользователя'));
        }
    }
}
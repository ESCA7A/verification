<?php

namespace Esca7a\Verification\Users\Actions;

use Domain\Base\Sessions\SessionExternalId;
use Esca7a\Verification\Users\DataTransferObjects\UserData;
use Esca7a\Verification\Users\Models\User;
use Esca7a\Verification\Users\Requests\Verification\Contracts\VerificationContract;
use Esca7a\Verification\Service\Enums\Channels;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AuthorizeAction
{
    public function run(User $user, VerificationContract $request): string
    {
        $sessionExternalId = new SessionExternalId();

        try {
            Auth::loginUsingId($user->id);

            $user->update([
                new UserData([
                    'phone_verified_at' => ($request->channel() === Channels::SMS->value) ? now() : null,
                    'email_verified_at' => ($request->channel() === Channels::EMAIL->value) ? now() : null,
                ])
            ]);


            $tokenKey = 'auth-'.$sessionExternalId->getOrGenerate();
            $user = auth()->user();
            $user->tokens()->where('name', $tokenKey)->delete();

        } catch (Throwable $e) {
            log_errors($e);
            abort(422, __('Не получилось выполнить вход пользователя'));
        }

        return auth()->user()->createToken($tokenKey)->plainTextToken;
    }
}
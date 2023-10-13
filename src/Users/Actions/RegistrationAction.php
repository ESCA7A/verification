<?php

namespace Esca7a\Verification\Users\Actions;

use Domain\Base\Sessions\SessionExternalId;
use Domain\Users\DataTransferObjects\UserData;
use Domain\Users\Requests\Verification\Contracts\VerificationContract;
use Domain\Verification\Enums\Channels;
use Illuminate\Auth\Events\Registered;
use Throwable;

class RegistrationAction
{
    public function run(VerificationContract $request): string
    {
        $createUserAction = new CreateUserAction();
        $sessionExternalId = new SessionExternalId();

        try {
            $user = $createUserAction->run(
                new UserData([
                    'phone' => $request?->phone,
                    'phone_country' => $request?->phone_country,
                    'password' => null,
                    'phone_verified_at' => ($request->channel() === Channels::SMS->value) ? now() : null,
                    'email' => $request?->email,
                    'email_verified_at' => ($request->channel() === Channels::EMAIL->value) ? now() : null,
                ])
            );

            event(new Registered($user));

        } catch (Throwable $e) {
            log_errors($e);
        }

        return $user->createToken('auth-' . $sessionExternalId->getOrGenerate())->plainTextToken;
    }
}

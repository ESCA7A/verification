<?php

namespace Esca7a\Verification\Users\Actions\Verification;

use Esca7a\Verification\Users\Actions\AuthorizeAction;
use Esca7a\Verification\Users\Actions\RegistrationAction;
use Esca7a\Verification\Users\Requests\Verification\Contracts\VerificationContract;
use Esca7a\Verification\Service\Enums\Channels;
use Esca7a\Verification\Service\Exceptions\AbstractVerificationException;
use Esca7a\Verification\Service\VerificationService;
use Illuminate\Http\JsonResponse;
use Log;
use Support\Facades\ApiResponse;

class AuthorizationConfirm
{
    public function run(VerificationContract $request): JsonResponse
    {
        try {
            $resultMessage = '';

            /** @var string $token Полученный токен после выполнения регистрации или авторизации */
            $token = null;

            $service = new VerificationService();

            $channel = Channels::findInstance($request->channel());

            $service->channel($channel)->verifyValue($request->verify_value);

            if ($service->verify($request->code)) {
                $user = $request->userByRequest();

                if ($user) {
                    $resultMessage = 'авторизовались';
                    $token = (new AuthorizeAction())->run($user, $request);
                } else {
                    $resultMessage = 'зарегистрировались';
                    $token = (new RegistrationAction())->run($request);
                }
            }
        } catch (AbstractVerificationException $exception) {
            Log::channel('verification')
                ->debug(__("Тело запроса: :request, Ошибка: :error", [
                    'request' => json_encode($request->input()),
                    'error' => $exception->getMessage(),
                ]));

            return ApiResponse::error(__('Не удалось пройти верификацию'))
                ->data([
                    'reason' => $exception->getMessage(),
                    'additional' => $exception->getAdditional($service),
                    ])->get();
        }

        return ApiResponse::success(__('Вы успешно :result_message', ['result_message' => $resultMessage]))
            ->data(['token' => $token])->get();
    }
}

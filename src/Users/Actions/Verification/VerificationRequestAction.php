<?php

namespace Esca7a\Verification\Users\Actions\Verification;

use Esca7a\Verification\Users\Requests\Verification\Contracts\VerificationContract;
use Esca7a\Verification\Service\Enums\Channels;
use Esca7a\Verification\Service\Exceptions\AbstractVerificationException;
use Esca7a\Verification\Service\VerificationService;
use Illuminate\Http\JsonResponse;
use Log;
use Support\Facades\ApiResponse;

class VerificationRequestAction
{
    public function run(VerificationContract $request): JsonResponse
    {
        try {
            $service = new VerificationService();

            $message = __('вас приветствует meet-market. Ваш код доступа :code', [
                'code' => $service->code
            ]);

            $channel = Channels::findInstance($request->channel);

            $service->channel($channel)->verifyValue($request->verify_value)->message($message);

            $service->send();
        } catch (AbstractVerificationException $exception) {
            log_errors($exception);

            Log::channel('verification')
                ->debug(__("Тело запроса: :request, Ошибка: :error", [
                    'request' => json_encode($request->input()),
                    'error' => $exception->getMessage(),
                ]));

            return ApiResponse::error(__('Не удалось отправить сообщение для подтверждения действия'))
                ->data([
                    'reason' => $exception->getMessage(),
                    'additional' => $exception->getAdditional($service),
                ])->get();
        }

        return ApiResponse::success('Вам отправлен код подтверждения')->get();
    }
}

<?php

namespace Esca7a\Verification\Users\Actions\Verification;

use Auth;
use Esca7a\Verification\Users\Actions\UpdateUserEmailAction;
use Esca7a\Verification\Users\Actions\UpdateUserPhoneAction;
use Esca7a\Verification\Users\Models\User;
use Esca7a\Verification\Users\Requests\Verification\Contracts\VerificationContract;
use Esca7a\Verification\Service\Channels\EmailChannel;
use Esca7a\Verification\Service\Channels\SmsChannel;
use Esca7a\Verification\Service\Enums\Channels;
use Esca7a\Verification\Service\Exceptions\AbstractVerificationException;
use Esca7a\Verification\Service\VerificationService;
use Illuminate\Http\JsonResponse;
use Support\Facades\ApiResponse;

class UpdateVerifyValueAction
{
    public function run(VerificationContract $request): JsonResponse
    {
        try {
            $service = new VerificationService();

            $channel = Channels::findInstance($request->channel());
            $service->channel($channel)->verifyValue($request->verify_value);

            /** @var User $user */
            $user = Auth::guard('sanctum')->user();

            if ($service->verify($request->code)) {
                $updateMaxmaClientAction = new UpdateAction();

                if ($channel instanceof SmsChannel) {
                    $action = new UpdateUserPhoneAction();
                    $updatedUser = $action->run($user, $request);
                }

                if ($channel instanceof EmailChannel) {
                    $action = new UpdateUserEmailAction();
                    $updatedUser = $action->run($user, $request);
                }
            }
        }  catch (AbstractVerificationException $exception) {
            log_errors($exception);

            return ApiResponse::error(__('Не удалось сменить данные')
            )->data([
                'reason' => $exception->getMessage(),
                'additional' => $exception->getAdditional($service),
                ])->get();
        }

        return ApiResponse::success('Вы успешно сменили данные')->get();
    }
}

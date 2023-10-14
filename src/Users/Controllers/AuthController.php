<?php

namespace Esca7a\Verification\Users\Controllers;

use Throwable;
use Illuminate\Support\Facades\Auth;
use Esca7a\Verification\Users\Requests\LoginRequest;
use Domain\Base\Sessions\SessionExternalId;
use App\RetailRocket\Services\RetailRocketService;
use Esca7a\Verification\Users\Actions\Verification\AuthorizationConfirm;
use Esca7a\Verification\Users\Actions\Verification\VerificationRequestAction;
use Esca7a\Verification\Users\Requests\Verification\ConfirmPhoneRequest;
use Esca7a\Verification\Users\Requests\Verification\SendPhoneRequest;
use Illuminate\Http\JsonResponse;
use Support\Facades\ApiResponse;

class AuthController
{
    public function login(LoginRequest $request, RetailRocketService $retailRocketService, SessionExternalId $sessionExternalId): JsonResponse
    {
        $data = $request->validated();

        if (!Auth::attempt($data)) {
            return ApiResponse::error(__('Данные от учетной записи не совпадают'), 401)
                ->get();
        }

        $tokenKey = 'auth-'.$sessionExternalId->getOrGenerate();
        $user = auth()->user();
        $user->tokens()->where('name', $tokenKey)->delete();

        try {
            $retailRocketService->setCustomer(
                $sessionExternalId->getOrGenerate(),
                (string) $user->id,
                $user->email,
                phone_number($user->phone ?? '', $user->phone_country),
                null,
                null,
                !$user->unsubscribe_mailing
            );
        } catch (Throwable $e) {
            log_errors($e, __('Не удалось отправить данные в RetailRocket'));
        }

        return ApiResponse::success(__('Пользователь успешно авторизован'))
            ->data(['token' => auth()->user()->createToken($tokenKey)->plainTextToken])
            ->get();
    }

    /**
     * Отправка сообщения с подтверждением номера телефона
     *
     * @param SendPhoneRequest $request
     * @param VerificationRequestAction $action
     * @return JsonResponse
     */
    public function phoneRequest(SendPhoneRequest $request, VerificationRequestAction $action): JsonResponse
    {
        return $action->run($request);
    }

    /**
     * Подтверждение номера телефона, вход или регистрация
     *
     * @param ConfirmPhoneRequest $request
     * @param AuthorizationConfirm $action
     * @return JsonResponse
     */
    public function phoneConfirm(ConfirmPhoneRequest $request, AuthorizationConfirm $action): JsonResponse
    {
        return $action->run($request);
    }

    public function logout(SessionExternalId $sessionExternalId): JsonResponse
    {
        auth()->user()->tokens()->where('name', 'auth-'.$sessionExternalId->getOrGenerate())->delete();
        auth()->guard('web')->logout();

        return ApiResponse::success(__('Токен авторизации сброшен'))->get();
    }
}

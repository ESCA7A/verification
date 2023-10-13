<?php

namespace Esca7a\Verification\Service;

use Carbon\Carbon;
use Esca7a\Verification\Service\Actions\SendAction;
use Esca7a\Verification\Service\Actions\VerifyAction;
use Esca7a\Verification\Service\Channels\VerificationChannelContract;
use Esca7a\Verification\Service\Exceptions\VerificationStrictTypesChannelException;
use Esca7a\Verification\Service\Exceptions\VerificationStrictTypesVerifyValueException;
use Esca7a\Verification\Service\Repositories\RepositoryInterface;

class VerificationService
{
    /**
     * Канал отправки
     *
     * @var VerificationChannelContract
     */
    public VerificationChannelContract $channel;

    /**
     * Репозиторий
     */
    private RepositoryInterface $repository;

    /**
     * Строгий режим
     */
    public bool $strictType;

    /**
     * Сообщение для отправки
     */
    public string $message = ':code';

    /**
     * Сгенерированный код
     */
    public string $code;

    /**
     * Значение на которое отправляются данные (номер, почта и т.д.)
     */
    public string $verifyValue;

    /**
     * Ip адрес обращения
     */
    public ?string $ip;

    /**
     * Время истечения текущего кода
     */
    public Carbon $expiresAt;

    public function __construct(RepositoryInterface $repository, bool $strictType = false)
    {
        $this->strictType = $strictType;
        $this->ip = request()->ip();
        $this->code = $this->generateCode();
        $this->repository = new $repository($this);
    }

    /**
     * Проверяет наличие строгого режима
     *
     * Если сущность работает в строгом режиме,
     * то при неинициализированных значениях канала и адреса верификации отлавливает ошибки
     */
    public function checkStrictType(): void
    {
        $verifyValue = isset($this->verifyValue);
        $channel = isset($this?->channel);

        if ($this->strictType) {
            if (!$channel) {
                throw new VerificationStrictTypesChannelException;
            }

            if (!$verifyValue) {
                throw new VerificationStrictTypesVerifyValueException;
            }
        }
    }

    /**
     * При успешной попытке создает запись в таблице
     */
    public function send(): void
    {
        (new SendAction())->run($this, $this->repository);
    }

    /**
     * Верифицирует запись в таблице
     */
    public function verify(string $code): bool
    {
        return (new VerifyAction($code))->run($this->repository);
    }

    /**
     * Генерация секретного кода
     */
    public function generateCode(?int $symbolsCount = null): string
    {
        $result = [];
        $codeLength = $symbolsCount ?? config('verification.code_length');

        for ($i = 0; $i < $codeLength; $i++) {
            $result[] = '#';
        }

        $codeLength = implode($result);

        return faker()->numerify($codeLength);
    }

    /**
     * Установить канал передачи
     */
    public function channel(VerificationChannelContract $channel): VerificationService
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Установить сообщение
     */
    public function message(string $message): VerificationService
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Установить адрес объекта для связи
     */
    public function verifyValue(string $verifyValue): VerificationService
    {
        $this->verifyValue = $verifyValue;

        return $this;
    }

    /**
     * Устанавливаем время сгорания кода
     * по умолчанию берется из конфига
     */
    public function expiresAt(?int $seconds = null): Carbon
    {
        $this->expiresAt = Carbon::now()->addSeconds($seconds ?? config('verification.expire_seconds'));

        return $this->expiresAt;
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }
}
<?php

namespace Esca7a\Verification\Service\Repositories;

use Esca7a\Verification\Service\Models\Verification;
use Esca7a\Verification\Service\VerificationService;

interface RepositoryInterface
{
    public function __construct(VerificationService $service);

    /**
     * создание
     */
    public function create(): Verification;

    /**
     * Обновление
     */
    public function update(): bool;

    /**
     * Содержит в себе 2 проверки или установки таймаута.
     */
    public function prepare(): void;

    /**
     * Установлена ли пауза
     * можно ли отправить код повторно
     */
    public function pauseIsSet(): bool;

    /**
     * Установлен ли таймаут
     */
    public function timeoutIsSet(): bool;

    /**
     * Удаляет все не подтвержденные записи текущего канала не включая переданную
     */
    public function flushWithout(): void;

    /**
     * Код не существует ?
     */
    public function codeIsNotExists(): bool;

    /**
     * Код истек ?
     */
    public function codeIsExpired(): bool;

    /**
     * Возвращает сущность с искомым кодом и не в статусе "подтверждено"
     */
    public function findByCode(): ?Verification;
}
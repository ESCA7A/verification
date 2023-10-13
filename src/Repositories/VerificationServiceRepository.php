<?php

namespace Esca7a\Verification\Repositories;

use Auth;
use Carbon\Carbon;
use Esca7a\Verification\Enums\VerificationStatus;
use Esca7a\Verification\Exceptions\VerificationAddRecordException;
use Esca7a\Verification\Models\Verification;
use Illuminate\Database\Eloquent\Builder;
use Throwable;
use Esca7a\Verification\VerificationService;

class VerificationServiceRepository implements RepositoryInterface
{
    /**
     * __Текущий__ инстанс для работы в его контексте
     */
    private VerificationService $service;

    public function __construct(VerificationService $service)
    {
        $this->service = $service;
    }

    /**
     * создание записи Verifications
     */
    public function create(): Verification
    {
        try {
            $record = Verification::create([
                'user_id'       => Auth::check() ? Auth::id() : null,
                'ip'            => $this->service->ip,
                'verify_value'  => $this->service->verifyValue,
                'channel'       => $this->service->channel->code(),
                'code'          => $this->service->code,
                'expires_at'    => $this->service->expiresAt(),
                'status'        => VerificationStatus::SEND,
                'attempts'      => $this->setAttempts(),
            ]);
        } catch (Throwable $throwable) {
            throw new VerificationAddRecordException;
        }

        return $record;
    }

    public function update(?Verification $record): bool
    {
        if ($record) {
            $record->status = VerificationStatus::CONFIRMED;
            $record->update();

            $this->flushNonConfirmed();

            return true;
        }

        return false;
    }

    /**
     * Считает попытки в соответствии с конфигом
     */
    public function setAttempts(): int
    {
        /** @var Verification $record */
        $record = $this->query()->first();
        $attempts = config('verification.limit_send_count');

        if (is_null($record)) {
            return 1;
        }

        switch ($record->attempts) {
            case $record->attempts < $attempts:
                return ++$record->attempts;

            default:
                return $attempts;
        }
    }

    /**
     * Запрос содержит в себе сразу выборку записи по полям `ip`, `verify_value`, `channel`
     * возвращает __дефолтный билд__ с которого начинается __каждый__ запрос к базе
     */
    public function query(): Builder
    {
        $verification = new Verification();

        return $verification->newQuery()
            ->where('user_id', Auth::user()?->id)
            ->where('ip', $this->service->ip)
            ->where('verify_value', $this->service->verifyValue)
            ->where('channel', $this->service->channel->code());
    }

    public function findByCode(string $code): ?Verification
    {
        return $this->query()
            ->where('code', $code)
            ->whereNot('status', VerificationStatus::CONFIRMED)
            ->get()
            ->first();
    }

    /**
     * Не истекшие коды
     */
    public function scopeUnexpired(): Builder
    {
        return $this->query()
            ->whereTime('expires_at', '>', now())
            ->whereDate('expires_at', '>=', now());
    }

    /**
     * Истекшие коды
     */
    public function scopeExpired(): Builder
    {
        return $this->query()
            ->whereTime('expires_at', '<=', now())
            ->whereDate('expires_at', '<=', now());
    }

    /**
     * Сортирует по дате создания и возвращает неистекший код
     */
    public function lastUnexpiredCode(): Builder
    {
        return $this->scopeUnexpired()->limit(1);
    }

    public function codeIsExpired($code): bool
    {
        $raw = $this->query()
            ->whereCode($code)
            ->whereTime('expires_at', '<=', now())
            ->whereDate('expires_at', '<=', now())
            ->first();

        return (bool)$raw;
    }

    public function codeIsNotExists(string $code): bool
    {
        /** @var Verification $raw */
        $raw = $this->query()->where('code', $code)->whereNot('status', VerificationStatus::CONFIRMED->value)->first();

        return is_null($raw);
    }

    /**
     * Удаление кодов по ip, verify_value, channel и user_id
     */
    public function flush(): void
    {
        $record = $this->query()->get()->first();
        $record->delete();
    }

    /**
     * Удаление всех не подтвержденных кодов
     */
    public function flushNonConfirmed(): void
    {
        if (config('verification.flushCode')) {
            $this->query()->whereNot('status', VerificationStatus::CONFIRMED)->delete();
        }
    }

    public function flushWithout(Verification $record): void
    {
        $this->query()
            ->whereNot('status', VerificationStatus::CONFIRMED)
            ->whereNot('created_at', $record->created_at)
            ->delete();
    }

    /*------------------------------------------------------------------------------------------------------------------
    |
    | Логика таймаутов и попыток отправления кода смс-подтверждения
    |
    |-------------------------------------------------------------------------------------------------------------------
    */

    public function pauseIsSet(): bool
    {
        $pauseSec = config('verification.next_send_after', 30);
        $record = $this->lastUnexpiredCode()->get();

        if ($record->count() === 0) {
            return false;
        }

        $timePast = Carbon::now()->diffInSeconds($record->first()->created_at);

        return $pauseSec > $timePast;
    }

    /**
     * Время когда пауза заканчивается
     */
    public function pauseExpiredTime(): Carbon|false
    {
        $pauseSec = config('verification.next_send_after', 30);
        $record = $this->lastUnexpiredCode()->get();

        if ($record->count() === 0) {
            return false;
        }

        $createdAt = $record->first()->created_at;
        $pauseIsEnd = Carbon::create($createdAt)->addSeconds($pauseSec);

        return $pauseIsEnd;
    }

    public function timeoutIsSet(): bool
    {
        $blockedPhone = $this->query()
            ->where('status', VerificationStatus::TIMEOUT)
            ->orderByDesc('created_at')
            ->get()
            ->first();

        return (bool)$blockedPhone;
    }

    /**
     * Вернуть время таймаута
     */
    public function timeoutExpiredTime(): null|Carbon|string
    {

        $blockedPhone = $this->query()
            ->where('status', VerificationStatus::TIMEOUT)
            ->orderByDesc('created_at')
            ->get()
            ->first();

        if ($blockedPhone) {
            return $blockedPhone->timeout;
        }

        return null;
    }

    /**
     * Устанавливает таймаут
     */
    public function setTimeout(): void
    {
        $timeoutCfg = config('verification.timeout', 600);
        $timeout = Carbon::now()->addSeconds($timeoutCfg);

        // не обновлять таймаут, если он уже установлен
        if ($this->timeoutIsSet()) {
            return;
        }

        $blockedPhone = $this->query()->get()->first();
        $blockedPhone->status = VerificationStatus::TIMEOUT;
        $blockedPhone->timeout = $timeout;
        $blockedPhone->update();
    }

    public function prepare(): void
    {
        $this->tryUnsetTimeout();
        $this->SetTimeoutIfAttemptIsLast();
    }

    /**
     * Если время таймаута закончилось, то запись удаляется
     */
    public function tryUnsetTimeout(): void
    {
        $record = $this->query()->where('status', VerificationStatus::TIMEOUT)->get()->first();

        if ($record && Carbon::now() > $record->timeout) {
            $this->flush();
        }
    }

    /**
     * Устанавливает таймаут на запись если кол-во попыток исчерпано
     */
    public function SetTimeoutIfAttemptIsLast(): void
    {
        /** @var Verification $record */
        $record = $this->query()->get()->first();
        $attempts = config('verification.limit_send_count');

        if ($record && $record->attempts === $attempts) {
            $this->setTimeout();
        }
    }
}
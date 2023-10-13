<?php

namespace Esca7a\Verification\Models\Traits;

trait PhoneVerification
{
    /**
     * Если найден только 1 номер телефона или вообще не найден вернет true
     */
    public static function isUniquePhone(string $phone): bool
    {
        $count = self::query()->where('phone', $phone)->count();

        return 1 <= $count;
    }

    public function setVerifiedPhone($phone): bool
    {
        $this->forceFill([
            'phone' => $phone,
            'phone_country' => 'ru',
            'phone_verified_at' => $this->freshTimestamp(),
        ]);

        return $this->save();
    }

    public function setVerifiedEmail($email): bool
    {
        $this->forceFill([
            'email' => $email,
            'email_verified_at' => $this->freshTimestamp(),
        ]);

        return $this->save();
    }
}
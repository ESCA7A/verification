<?php

namespace Esca7a\Verification\Service\Models;

use Esca7a\Verification\Users\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Support\Traits\Models\HasFactory;
use Support\Traits\QueryBuilders\HasApiQueryBuilder;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $ip
 * @property string $verify_value Значение адреса на который идет запрос: номер телефона, email адрес и тд
 * @property string $channel Канал по которому происходит верификация: телефон, почта и т.д.
 * @property string $code Секретный код
 * @property int|null $attempts Номер попытки
 * @property string $status
 * @property string $expires_at
 * @property string|null $timeout
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 */
class Verification extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function findVerifyValue(string $verifyValue) : ?string
    {
        $raw = $this->where('verify_value', $verifyValue)->first();

        return $raw?->verify_value;
    }
}

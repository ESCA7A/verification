<?php

namespace Esca7a\Verification\Users\Requests\Verification\Contracts;

use Esca7a\Verification\Service\Enums\Channels;

/**
 * @property string $phone
 * @property string $phone_country
 * @property string $email
 * @property Channels $channel
 * @property string $verify_value
 * @property string $code
 */
interface VerificationContract extends VerificationRequestContract
{
}
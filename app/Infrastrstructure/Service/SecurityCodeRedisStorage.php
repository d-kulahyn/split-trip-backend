<?php

namespace App\Infrastrstructure\Service;

use App\Infrastrstructure\API\Enum\UserConfirmationCodeEnum;
use App\Infrastrstructure\Service\Interface\SecurityCodeStorageInterface;
use DateInterval;
use Exception;
use Illuminate\Support\Facades\Cache;

class SecurityCodeRedisStorage implements SecurityCodeStorageInterface
{
    /**
     * @var string
     */
    public static string $dateInterval = 'P1D';

    /**
     * @param int $userId
     * @return string
     * @throws Exception
     */
    public function set(int $userId): string
    {
        $code = random_int(100000, 999999);

        $res = Cache::set(UserConfirmationCodeEnum::USER_CONFIRMATION_EMAIL_CODE->map([
            '{user_id}' => $userId
        ]), $code, new DateInterval(self::$dateInterval));

        if (!$res) {
            throw new Exception('Failed to set code');
        }

        return $code;
    }

    /**
     * @param int $userId
     * @return string|null
     */
    public function extract(int $userId): ?string
    {
        $cacheKey = UserConfirmationCodeEnum::USER_CONFIRMATION_EMAIL_CODE->map([
            '{user_id}' => $userId
        ]);
        return Cache::get($cacheKey);
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function forget(int $userId): bool
    {
        $cacheKey = UserConfirmationCodeEnum::USER_CONFIRMATION_EMAIL_CODE->map([
            '{user_id}' => $userId
        ]);
        return Cache::forget($cacheKey);
    }
}
